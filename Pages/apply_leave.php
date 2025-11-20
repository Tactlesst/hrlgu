<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: Login.php');
    exit;
}

include 'db_connect.php';

// helper functions
function count_workdays($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    if ($end < $start)
        return 0;
    $days = 0;
    for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
        $dow = (int) $d->format('N'); // 1 (Mon) .. 7 (Sun)
        if ($dow < 6)
            $days++;
    }
    return $days;
}
function add_workdays($startDate, $workdays)
{
    $d = new DateTime($startDate);
    if ($workdays <= 1)
        return $d;
    $remaining = $workdays - 1; 
    while ($remaining > 0) {
        $d->modify('+1 day');
        if ((int) $d->format('N') < 6)
            $remaining--;
    }
    return $d;
}

function create_abbreviation($name) {
    $words = preg_split("/\s+/", $name);
    $abbr = "";
    foreach ($words as $word) {
        if (!empty($word)) {
            $abbr .= strtoupper(substr($word, 0, 1));
        }
    }
    return $abbr;
}

function redirect_with($key, $msg)
{
    $loc = 'Employee-Dashboard.php';
    $params = [$key => urlencode($msg), 'tab' => 'apply-leave-section'];
    header('Location: ' . $loc . '?' . http_build_query($params));
    exit;
}

// Read & validate input
$employeeId = (int) $_SESSION['user_id'];
$leaveTypeId = isset($_POST['leave_type']) ? (int) $_POST['leave_type'] : 0;
$startDateRaw = $_POST['start_date'] ?? '';
$endDateRaw = $_POST['end_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (!$leaveTypeId || !$startDateRaw) {
    redirect_with('error', 'Missing required fields.');
}

try {
    $start = new DateTimeImmutable($startDateRaw);
} catch (Exception $e) {
    redirect_with('error', 'Invalid start date format.');
}

// Fetch LeaveType details
$stmt = $conn->prepare("SELECT TypeName, UnitType, AllowDocuments, MaxDocuments, MaxDaysPerUsage, DeductFromLeaveTypeID FROM LeaveType WHERE LeaveTypeID = ?");
$stmt->bind_param("i", $leaveTypeId);
$stmt->execute();
$stmt->bind_result($typeName, $unitType, $allowDocs, $maxDocs, $fixedDays, $deductFromId); 
if (!$stmt->fetch()) {
    $stmt->close();
    redirect_with('error', 'Selected leave type not found.');
}
$stmt->close();


// --- ### CHANGE 1: Validate UnitType ### ---
// Add 'Grant' as a valid type
if ($unitType !== 'Leave Credit' && $unitType !== 'Fixed Days' && $unitType !== 'Grant') {
    redirect_with('error', 'This leave type is not supported by this form.');
}
// --- ### END CHANGE 1 ### ---


// Calculate duration and end date
if ($unitType === 'Fixed Days') {
    if ((int) $fixedDays < 1)
        redirect_with('error', 'Leave type is misconfigured (fixed days).');
    
    $endDt = add_workdays($start->format('Y-m-d'), (int) $fixedDays);
    $end = DateTimeImmutable::createFromMutable($endDt);
    $startStr = $start->format('Y-m-d');
    $endStr = $end->format('Y-m-d');
    $durationDays = (float) $fixedDays;
} else {
    // 'Leave Credit' or 'Grant' type
    if (!$endDateRaw)
        redirect_with('error', 'Please provide an end date.');
    try {
        $end = new DateTimeImmutable($endDateRaw);
    } catch (Exception $e) {
        redirect_with('error', 'Invalid end date format.');
    }
    if ($end < $start)
        redirect_with('error', 'End date must be on or after start date.');

    $durationDays = (float) count_workdays($start->format('Y-m-d'), $end->format('Y-m-d'));
    $startStr = $start->format('Y-m-d');
    $endStr = $end->format('Y-m-d');
}

// Determine WHICH balance to check
$balanceCheckLeaveTypeId = $leaveTypeId; 

// --- This logic works for both Credit and Grant ---
if (($unitType === 'Leave Credit' || $unitType === 'Grant') && !empty($deductFromId) && $deductFromId > 0) {
    $balanceCheckLeaveTypeId = (int)$deductFromId;
}

// --- ### START: MODIFIED BALANCE CHECK ### ---
// --- ### CHANGE 2: Balance Check ### ---
// Check balance for 'Leave Credit' OR 'Grant'
if ($unitType === 'Leave Credit' || $unitType === 'Grant') {
    
    // Get the employee's current balances
    $stmt = $conn->prepare(
        "SELECT BalanceAccruedDays, TakenDays, PlannedDays 
         FROM LeaveAvailability 
         WHERE EmployeeID = ? AND LeaveTypeID = ? LIMIT 1"
    );
    $stmt->bind_param("ii", $employeeId, $balanceCheckLeaveTypeId); 
    $stmt->execute();
    $stmt->bind_result($accrued, $taken, $planned);
    $hasRow = $stmt->fetch();
    $stmt->close();
    
    $available = 0.0;
    
    if ($hasRow) {
        // Calculate true available balance
        $available = (float)$accrued - (float)$taken - (float)$planned;
    } else {
        // This employee does not have a balance row. Let's create one.
        if ($balanceCheckLeaveTypeId == $leaveTypeId) {
            $defAllow = 0.0;
            $defBalance = 0.0;
            $r = $conn->prepare("SELECT COALESCE(DefaultAllowanceDays,0.0), COALESCE(DefaultBalanceDays,0.0) FROM LeaveType WHERE LeaveTypeID = ? LIMIT 1");
            if ($r) {
                $r->bind_param("i", $leaveTypeId);
                $r->execute();
                $r->bind_result($defAllow, $defBalance);
                $r->fetch();
                $r->close();
            }

            // Create the new row
            $ins = $conn->prepare("INSERT IGNORE INTO LeaveAvailability (EmployeeID, LeaveTypeID, AllowanceAnnualDays, BalanceAccruedDays, LastAccrualDate, TakenDays, PlannedDays) VALUES (?, ?, ?, ?, NULL, 0.0, 0.0)");
            if ($ins) {
                $ins->bind_param("iidd", $employeeId, $leaveTypeId, $defAllow, $defBalance);
                $ins->execute();
                $ins->close();
            }
            $available = (float) $defBalance;
        }
    }

    // Final check
    if ($durationDays > $available) {
        redirect_with('error', "Requested {$durationDays} workday(s) exceeds available balance ({$available}).");
    }
}
// --- ### END: MODIFIED BALANCE CHECK ### ---

// File validation (no change needed)
$uploadedFiles = $_FILES['documents'] ?? null;
// ... (rest of file validation) ...
if ((int) $allowDocs === 1 && $uploadedCount === 0) {
    redirect_with('error', 'This leave type requires at least one supporting document.');
}
if ((int) $maxDocs > 0 && $uploadedCount > (int) $maxDocs) {
    redirect_with('error', "You uploaded {$uploadedCount} files but the maximum allowed is {$maxDocs}.");
}


// --- 1. Insert application ---
$ins = $conn->prepare("INSERT INTO LeaveApplication (EmployeeID, LeaveTypeID, StartDate, EndDate, DurationDays, Status, Reason) VALUES (?, ?, ?, ?, ?, 'Pending', ?)");
$ins->bind_param("iissds", $employeeId, $leaveTypeId, $startStr, $endStr, $durationDays, $reason);
$ok = $ins->execute();
$appId = $conn->insert_id;
$ins->close();

if (!$ok) {
    redirect_with('error', 'Failed to submit application. Please try again.');
}

// --- 2. Update Control Number & Reserve PlannedDays ---
if ($ok && $appId > 0) {
    
    // --- ### CHANGE 3: Reserve PlannedDays ### ---
    // Reserve days for 'Leave Credit' OR 'Grant'
    if ($unitType === 'Leave Credit' || $unitType === 'Grant') {
        $upd_planned = $conn->prepare(
            "UPDATE LeaveAvailability 
             SET PlannedDays = PlannedDays + ?
             WHERE EmployeeID = ? AND LeaveTypeID = ?"
        );
        if ($upd_planned) {
            $upd_planned->bind_param("dii", $durationDays, $employeeId, $balanceCheckLeaveTypeId);
            $upd_planned->execute();
            $upd_planned->close();
        }
    }
    // --- ### END CHANGE 3 ### ---

    // Generate and save Control Number
    $leaveAbbr = create_abbreviation($typeName);
    $dateStr = date('Ymd');
    $controlNumber = $leaveAbbr . '-' . $dateStr . '-' . $appId;
    
    $upd = $conn->prepare("UPDATE LeaveApplication SET ControlNumber = ? WHERE ApplicationID = ?");
    if ($upd) {
        $upd->bind_param("si", $controlNumber, $appId);
        $upd->execute();
        $upd->close();
    }
}

// --- 3. Handle uploaded documents ---
// ... (no change needed in this block) ...
if ($uploadedFiles && is_array($uploadedFiles['name']) && $uploadedCount > 0) {
    $destDir = __DIR__ . '/../Documents/EmployeeFiles/' . $employeeId;
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }
    for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
        if (empty($uploadedFiles['name'][$i]) || $uploadedFiles['error'][$i] !== UPLOAD_ERR_OK)
            continue;
        $origName = basename($uploadedFiles['name'][$i]);
        $ext = pathinfo($origName, PATHINFO_EXTENSION);
        $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
        $filename = $safeBase . '_' . time() . '_' . $i . '.' . $ext;
        $target = $destDir . '/' . $filename;
        if (move_uploaded_file($uploadedFiles['tmp_name'][$i], $target)) {
            $webPath = '/HR-Leave-Management-System/Documents/EmployeeFiles/' . $employeeId . '/' . $filename;
            $docType = null;
            $stmt = $conn->prepare("INSERT INTO EmployeeDocument (EmployeeID, ApplicationID, FilePath, DocType) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiss", $employeeId, $appId, $webPath, $docType);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}


// --- 4. Optional: audit log ---
// ... (no change needed in this block) ...
if (isset($conn)) {
    $act = 'ApplyLeave';
    $table = 'LeaveApplication';
    $recordId = $appId;
    $details = "Employee {$employeeId} applied for leave type {$leaveTypeId} ({$durationDays} days) from {$startStr} to {$endStr}.";
    $alog = $conn->prepare("INSERT INTO AuditLog (UserID, Action, TableName, RecordID, Details) VALUES (?, ?, ?, ?, ?)");
    if ($alog) {
        $alog->bind_param("issis", $employeeId, $act, $table, $recordId, $details);
        $alog->execute();
        $alog->close();
    }
}

redirect_with('success', 'Leave application submitted successfully.');
?>