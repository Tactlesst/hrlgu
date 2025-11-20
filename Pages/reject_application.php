<?php
session_start();
header('Content-Type: application/json');

// --- 1. Standard Checks ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
// This error is triggered by your JavaScript
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}

include 'db_connect.php';
$appId = (int) $_POST['id'];
$adminId = (int) $_SESSION['user_id']; // This is used for LeaveHistory

// Enable strict error reporting for transactions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // --- 2. START THE TRANSACTION ---
    $conn->begin_transaction();

    // --- 3. Get ALL application details (Expanded Query) ---
    $stmt = $conn->prepare(
        "SELECT 
            app.EmployeeID, app.LeaveTypeID, app.StartDate, app.EndDate,
            app.DurationDays, app.Status,
            lt.UnitType, lt.DeductFromLeaveTypeID
         FROM LeaveApplication AS app
         JOIN LeaveType AS lt ON app.LeaveTypeID = lt.LeaveTypeID
         WHERE app.ApplicationID = ?"
    );
    $stmt->bind_param("i", $appId);
    $stmt->execute();
    $result = $stmt->get_result();
    $app = $result->fetch_assoc();
    $stmt->close();

    // Check if found AND still pending
    if (!$app) {
        throw new Exception('Application not found.');
    }
    if ($app['Status'] !== 'Pending') {
        throw new Exception('Application has already been processed.');
    }

    // --- 4. Update application status ---
    //
    // --- THIS IS THE FIX ---
    // Removed 'ApprovedByUserID' to match your working approve_application.php
    //
    $u = $conn->prepare("UPDATE LeaveApplication SET Status = 'Rejected' WHERE ApplicationID = ?");
    $u->bind_param("i", $appId);
    $u->execute();
    $u->close();
    // --- END OF FIX ---

    // --- 5. Insert into LeaveHistory ---
    // This now matches your working approve_application.php
    $ins = $conn->prepare("INSERT INTO LeaveHistory (ApplicationID, EmployeeID, LeaveTypeID, StartDate, EndDate, Status, ProcessedByAdminID) VALUES (?, ?, ?, ?, ?, 'Rejected', ?)");
    $ins->bind_param("iiissi", $appId, $app['EmployeeID'], $app['LeaveTypeID'], $app['StartDate'], $app['EndDate'], $adminId);
    $ins->execute();
    $ins->close();

    // --- 6. REFUND THE "PLANNED" DAYS ---
    // This logic is correct and handles 'Leave Credit' and 'Grant'
    if ($app['UnitType'] === 'Leave Credit' || $app['UnitType'] === 'Grant') {
        
        $duration = (float) $app['DurationDays'];

        // Find the correct balance row
        $balanceLeaveTypeId = $app['LeaveTypeID'];
        if (!empty($app['DeductFromLeaveTypeID']) && $app['DeductFromLeaveTypeID'] > 0) {
            $balanceLeaveTypeId = (int) $app['DeductFromLeaveTypeID'];
        }

        // Subtract the duration from PlannedDays
        $ref = $conn->prepare(
            "UPDATE LeaveAvailability 
             SET PlannedDays = PlannedDays - ?
             WHERE 
                 EmployeeID = ? AND LeaveTypeID = ?"
        );
        $ref->bind_param("dii", $duration, $app['EmployeeID'], $balanceLeaveTypeId);
        $ref->execute();
        $ref->close();
    }
    
    // --- 7. COMMIT ---
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // --- 8. ROLLBACK ---
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>