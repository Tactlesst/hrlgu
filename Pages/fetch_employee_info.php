<?php
// filepath: d:\Xampp\htdocs\HR-Leave-Management-System\Pages\fetch_employee_info.php
if (!isset($conn))
    include 'db_connect.php';
$employeeID = $_SESSION['user_id'] ?? 0;

/* ---------------------------
   Employee basic info
   --------------------------- */
$sql = "SELECT 
            e.FirstName, e.MiddleName, e.LastName, e.Birthdate, e.EmployeePhoto,
            d.DepartmentName, p.PositionName
        FROM Employee e
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN `Position` p ON e.PositionID = p.PositionID
        WHERE e.EmployeeID = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $employeeID);
    $stmt->execute();
    $stmt->bind_result($firstName, $middleName, $lastName, $birthdate, $photo, $department, $position);
    $stmt->fetch();
    $stmt->close();
} else {
    // prepare failed — set defaults and log error
    error_log("fetch_employee_info.php: prepare failed (employee info): " . $conn->error);
    $firstName = $middleName = $lastName = $birthdate = $photo = $department = $position = '';
}

/* Calculate age */
$age = '';
if (!empty($birthdate)) {
    try {
        $birthDateObj = new DateTime($birthdate);
        $today = new DateTime('today');
        $age = $birthDateObj->diff($today)->y;
    } catch (Exception $e) {
        $age = '';
    }
}

$fullName = htmlspecialchars(trim("$firstName $middleName $lastName"));
$department = htmlspecialchars($department);
$position = htmlspecialchars($position);
$birthdate = htmlspecialchars($birthdate);
$photoPath = $photo ? htmlspecialchars($photo) : "Pictures/DefaultPicture.jpg";

/* ---------------------------
   Leave Analytics
   - this_month_requests
   - this_year_requests
   - total balance (days/hours)
   - total_points_spent (approved only)
   --------------------------- */

$thisMonthRequests = 0;
$thisYearRequests = 0;
$totalBalanceDays = 0;
$totalBalanceHours = 0;
$totalPointsSpent = 0;

// 1) This month's requests (use your LeaveApplication table)
$sqlMonth = "SELECT COUNT(*) AS total FROM LeaveApplication WHERE EmployeeID = ? AND MONTH(StartDate) = MONTH(CURDATE()) AND YEAR(StartDate) = YEAR(CURDATE())";
$stmtMonth = $conn->prepare($sqlMonth);
if ($stmtMonth) {
    $stmtMonth->bind_param("i", $employeeID);
    $stmtMonth->execute();
    $res = $stmtMonth->get_result();
    $row = $res->fetch_assoc() ?? null;
    $thisMonthRequests = intval($row['total'] ?? 0);
    $stmtMonth->close();
} else {
    error_log("fetch_employee_info.php: prepare failed (monthRequests): " . $conn->error);
}

// 2) This year's requests
$sqlYear = "SELECT COUNT(*) AS total FROM LeaveApplication WHERE EmployeeID = ? AND YEAR(StartDate) = YEAR(CURDATE())";
$stmtYear = $conn->prepare($sqlYear);
if ($stmtYear) {
    $stmtYear->bind_param("i", $employeeID);
    $stmtYear->execute();
    $res = $stmtYear->get_result();
    $row = $res->fetch_assoc() ?? null;
    $thisYearRequests = intval($row['total'] ?? 0);
    $stmtYear->close();
} else {
    error_log("fetch_employee_info.php: prepare failed (yearRequests): " . $conn->error);
}

// 3) Total leave balance (sum of generated available columns)
// Note: schema has AvailableToTakeDays and AvailableToTakeHours (generated columns)
$sqlBalance = "SELECT 
                 COALESCE(SUM(AvailableToTakeDays),0) AS days
               FROM LeaveAvailability
               WHERE EmployeeID = ?";
$stmtBal = $conn->prepare($sqlBalance);
if ($stmtBal) {
    $stmtBal->bind_param("i", $employeeID);
    $stmtBal->execute();
    $res = $stmtBal->get_result();
    $row = $res->fetch_assoc() ?? null;
    $totalBalanceDays = floatval($row['days'] ?? 0);
    $stmtBal->close();
} else {
    error_log("fetch_employee_info.php: prepare failed (balance): " . $conn->error);
}

// 4) Total points spent — use LeaveApplication joined to LeaveType
// We'll calculate duration as DATEDIFF(EndDate,StartDate)+1 for days. Only Approved entries are counted.
$sqlSpent = "SELECT 
                COALESCE(SUM( (DATEDIFF(EndDate, StartDate) + 1) * lt.PointCost ), 0) AS totalSpent
             FROM LeaveApplication lr
             JOIN LeaveType lt ON lr.LeaveTypeID = lt.LeaveTypeID
             WHERE lr.EmployeeID = ? AND lr.Status = 'Approved'";
$stmtSpent = $conn->prepare($sqlSpent);
if ($stmtSpent) {
    $stmtSpent->bind_param("i", $employeeID);
    $stmtSpent->execute();
    $res = $stmtSpent->get_result();
    $row = $res->fetch_assoc() ?? null;
    $totalPointsSpent = floatval($row['totalSpent'] ?? 0);
    $stmtSpent->close();
} else {
    error_log("fetch_employee_info.php: prepare failed (totalPointsSpent): " . $conn->error);
}
?>