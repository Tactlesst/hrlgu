<?php
session_start();
header('Content-Type: application/json');

// --- 1. Standard Checks ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}

include 'db_connect.php';
$appId = (int) $_POST['id'];
$adminId = (int) $_SESSION['user_id'];

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

    if (!$app) {
        throw new Exception('Application not found.');
    }
    if ($app['Status'] !== 'Pending') {
        throw new Exception('Application is not pending or already processed.');
    }

    // --- 4. Update application status (Your existing logic) ---
    // --- THIS IS THE FIX: UNCOMMENT THESE LINES ---
   $u = $conn->prepare("UPDATE LeaveApplication SET Status = 'Approved' WHERE ApplicationID = ?");
$u->bind_param("i", $appId);
$u->execute();
$u->close();
    // --- END OF FIX ---

    // --- 5. Insert into LeaveHistory (Your existing logic) ---
    $ins = $conn->prepare("INSERT INTO LeaveHistory (ApplicationID, EmployeeID, LeaveTypeID, StartDate, EndDate, Status, ProcessedByAdminID) VALUES (?, ?, ?, ?, ?, 'Approved', ?)");
    // Note the new 'i' at the start for ApplicationID
    $ins->bind_param("iiissi", $appId, $app['EmployeeID'], $app['LeaveTypeID'], $app['StartDate'], $app['EndDate'], $adminId);
    $ins->execute();
    $ins->close();

    // --- 6. DEDUCT CREDITS (The New Logic) ---
    if ($app['UnitType'] === 'Leave Credit' || $app['UnitType'] === 'Grant') {
        
        $duration = (float) $app['DurationDays'];

        // Find the correct balance row to deduct from
        $balanceLeaveTypeId = $app['LeaveTypeID'];
        if (!empty($app['DeductFromLeaveTypeID']) && $app['DeductFromLeaveTypeID'] > 0) {
            $balanceLeaveTypeId = (int) $app['DeductFromLeaveTypeID'];
        }

        // Move the "reserved" days from Planned to Taken
        $dec = $conn->prepare(
            "UPDATE LeaveAvailability 
             SET 
                 PlannedDays = PlannedDays - ?,
                 TakenDays = TakenDays + ?
             WHERE 
                 EmployeeID = ? AND LeaveTypeID = ?"
        );
        // Use 'd' for double/decimal
        $dec->bind_param("ddii", $duration, $duration, $app['EmployeeID'], $balanceLeaveTypeId);
        $dec->execute();
        
        if ($dec->affected_rows === 0) {
            throw new Exception('Failed to update leave balance: No balance row found for this employee.');
        }
        $dec->close();
    }
    
    // --- 7. COMMIT ---
    // All steps succeeded, make the changes permanent
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // --- 8. ROLLBACK ---
    // Something failed, undo all database changes from this script
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>