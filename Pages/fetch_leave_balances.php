<?php
// filepath: d:\Xampp\htdocs\HR-Leave-Management-System\Pages\fetch_leave_balances.php
if (!isset($conn))
    include 'db_connect.php';
$leaveTypes = [];
$leaveBalances = [];
$employeeID = $_SESSION['user_id'];

// Get all leave types
$leaveTypeSql = "SELECT LeaveTypeID, TypeName, UnitType, PointCost FROM LeaveType";
$leaveTypeResult = $conn->query($leaveTypeSql);
while ($row = $leaveTypeResult->fetch_assoc()) {
    $leaveTypes[$row['LeaveTypeID']] = $row;
}

// Get leave balances for this employee
$leaveAvailSql = "SELECT la.*, lt.TypeName, lt.UnitType
    FROM LeaveAvailability la
    JOIN LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
    WHERE la.EmployeeID = ?";
$leaveAvailStmt = $conn->prepare($leaveAvailSql);
$leaveAvailStmt->bind_param("i", $employeeID);
$leaveAvailStmt->execute();
$leaveAvailResult = $leaveAvailStmt->get_result();
while ($row = $leaveAvailResult->fetch_assoc()) {
    $leaveBalances[$row['LeaveTypeID']] = $row;
}
$leaveAvailStmt->close();
?>