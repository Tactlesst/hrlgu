<?php
// FILE: get_application_details_admin.php
session_start();
header('Content-Type: application/json');
include 'db_connect.php'; // Make sure this path is correct!

// **ADMIN SECURITY CHECK**
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

$applicationId = (int) ($_GET['id'] ?? 0);
if ($applicationId <= 0) {
     echo json_encode(['error' => 'Invalid Application ID.']);
    exit;
}

// **MODIFIED QUERY**
// This query is the same as the employee's, but the WHERE clause is simpler.
// It does NOT check if the EmployeeID matches the session, so the admin can see all.
//
// **!! IMPORTANT !!**
// Replace "YOUR_SALARY_COLUMN_NAME" with your actual salary column from the 'Employee' table.
//
$sql = "SELECT 
            la.ControlNumber, 
            la.StartDate, 
            la.EndDate, 
            la.DurationDays,
            la.Status, 
            la.Reason,
            la.DateRequested,
            lt.TypeName,
            
            e.FirstName,
            e.LastName,
            e.MiddleName,
            
            e.MonthlySalary AS Salary,
            p.PositionName AS Position,           
            d.DepartmentName AS Department
            
            -- ** THE FIX IS HERE ** --
            -- I have removed 'la.RejectionReason' from the query
        FROM 
            LeaveApplication la
        JOIN 
            LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
        JOIN 
            Employee e ON la.EmployeeID = e.EmployeeID
        LEFT JOIN
            Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN
            Position p ON e.PositionID = p.PositionID
        WHERE 
            la.ApplicationID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $applicationId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['DateRequested']);
    $row['DateRequestedFormatted'] = $date->format('F j, Y'); 
    
    // You can hard-code your agency details
    $row['AgencyName'] = "Your Agency Name";
    $row['AgencyAddress'] = "Your Agency Address";

    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Application not found.']);
}

$stmt->close();
$conn->close();
?>