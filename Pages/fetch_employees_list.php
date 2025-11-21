<?php
session_start();
header('Content-Type: application/json');

// Security check - allow if admin OR if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connect.php';

$sql = "SELECT 
            e.EmployeeID,
            e.FirstName,
            e.LastName,
            d.DepartmentName
        FROM Employee e
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        WHERE e.Status = 'Active'
        ORDER BY e.FirstName ASC";

$result = $conn->query($sql);
$employees = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

echo json_encode($employees);

$conn->close();
?>
