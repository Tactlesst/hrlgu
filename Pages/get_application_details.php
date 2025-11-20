<?php
// FILE: get_application_details.php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    echo json_encode(['error' => 'User not authenticated.']);
    exit;
}

$employeeId = (int) $_SESSION['user_id'];
$applicationId = (int) ($_GET['id'] ?? 0);
// ... (validation) ...


// --- NEW, UPDATED SQL QUERY ---
// We must JOIN Employee and Admin tables to get all the data
$sql = "SELECT 
            la.ControlNumber, 
            la.StartDate, 
            la.EndDate, 
            la.DurationDays,
            la.Status, 
            la.Reason,
            la.DateRequested,
            lt.TypeName,
            
            -- Employee Data --
            e.FirstName,
            e.LastName,
            e.MiddleName,
            e.MonthlySalary, -- <-- FIX THIS
            
            -- Joined Data --
            p.PositionName AS Position,           -- <-- FIXED THIS
            d.DepartmentName AS Department,
            
            -- Admin/Action Data --
            la.Reason
        FROM 
            LeaveApplication la
        JOIN 
            LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
        JOIN 
            Employee e ON la.EmployeeID = e.EmployeeID
        LEFT JOIN
            Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN
            Position p ON e.PositionID = p.PositionID  -- <-- FIXED THIS
        WHERE 
            la.ApplicationID = ? AND la.EmployeeID = ?";
// --- END NEW QUERY ---

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $applicationId, $employeeId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Add a formatted date for the form
    $date = new DateTime($row['DateRequested']);
    $row['DateRequestedFormatted'] = $date->format('F j, Y'); // e.g., November 10, 2025
    
    // You may also want to get Agency Name/Address from a config file
    $row['AgencyName'] = "Municipal Goverment of Balingasag";
    $row['AgencyAddress'] = "Balingasag, Misamis Oriental";

    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Application not found.']);
}

$stmt->close();
$conn->close();
?>