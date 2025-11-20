<?php
// FILE: /Pages/fetch_employee_leave_data.php

// Ensure session is started, but not twice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check for database connection
if (!isset($conn)) {
    include_once 'db_connect.php';
}

// 2. Check if Employee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    // If not, create empty arrays to prevent errors on the dashboard
    $leaveTypes = [];
    $leaveBalances = [];
    $fixedLeaveUsage = [];
    // You could also redirect to login, but this is safer for an include
    return; 
}

// 3. Get Employee ID and Current Year
$employeeId = (int) $_SESSION['user_id'];
$currentYear = date('Y');

// --- Arrays to be populated ---
$leaveTypes = [];
$leaveBalances = [];
$fixedLeaveUsage = [];


// 4. Fetch ALL LeaveType rules (The "Rule Book")
$typeSql = "SELECT * FROM LeaveType ORDER BY TypeName ASC";
$typeRes = $conn->query($typeSql);
if ($typeRes) {
    while ($row = $typeRes->fetch_assoc()) {
        // Normalize data
        $row['LeaveTypeID'] = (int) $row['LeaveTypeID'];
        $row['MaxDaysPerUsage'] = (int) $row['MaxDaysPerUsage'];
        $row['PointCost'] = (float) $row['PointCost'];
        $row['DeductFromLeaveTypeID'] = empty($row['DeductFromLeaveTypeID']) ? null : (int) $row['DeductFromLeaveTypeID'];
        
        $leaveTypes[$row['LeaveTypeID']] = $row;
    }
}

// 5. Fetch Employee's 'Leave Credit' balances (The "Wallet")
$balSql = "SELECT * FROM LeaveAvailability WHERE EmployeeID = ?";
$balStmt = $conn->prepare($balSql);
$balStmt->bind_param("i", $employeeId);
$balStmt->execute();
$balRes = $balStmt->get_result();
if ($balRes) {
    while ($row = $balRes->fetch_assoc()) {
        // Normalize data
        $row['BalanceAccruedDays'] = (float) ($row['BalanceAccruedDays'] ?? 0);
        $row['TakenDays'] = (float) ($row['TakenDays'] ?? 0);
        $row['PlannedDays'] = (float) ($row['PlannedDays'] ?? 0);
        
        // Calculate the "Available" days
        $row['AvailableToTakeDays'] = $row['BalanceAccruedDays'] - $row['TakenDays'] - $row['PlannedDays'];

        $leaveBalances[$row['LeaveTypeID']] = $row;
    }
}
$balStmt->close();


// 6. Fetch Employee's 'Fixed Leave' usage (The "Ledger")
$usageSql = "SELECT LeaveTypeID, SUM(DaysUsed) as TotalDaysUsed
             FROM LeaveUsageLog
             WHERE EmployeeID = ? AND YEAR(UsageDate) = ?
             GROUP BY LeaveTypeID";
$usageStmt = $conn->prepare($usageSql);
$usageStmt->bind_param("is", $employeeId, $currentYear);
$usageStmt->execute();
$usageRes = $usageStmt->get_result();
if ($usageRes) {
    while ($row = $usageRes->fetch_assoc()) {
        $fixedLeaveUsage[(int) $row['LeaveTypeID']] = (float) $row['TotalDaysUsed'];
    }
}
$usageStmt->close();

// This script is now finished.
// The Employee-Dashboard.php file can now use the 3 arrays:
// $leaveTypes
// $leaveBalances
// $fixedLeaveUsage
?>