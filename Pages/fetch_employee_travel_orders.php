<?php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated.']);
    exit;
}

$employeeId = $_SESSION['user_id'];

// Fetch all travel orders for the logged-in employee
$sql = "SELECT 
            t.TravelID,
            t.EmployeeID,
            t.Destination,
            t.Purpose,
            t.StartDate,
            t.EndDate,
            t.Status,
            t.DateRequested,
            e.FirstName,
            e.LastName,
            d.DepartmentName
        FROM TravelOrder t
        JOIN Employee e ON t.EmployeeID = e.EmployeeID
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        WHERE t.EmployeeID = ?
        ORDER BY t.DateRequested DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Database prepare failed.']);
    exit;
}

$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

$travelOrders = [];
while ($row = $result->fetch_assoc()) {
    $travelOrders[] = [
        'TravelID' => $row['TravelID'],
        'Destination' => htmlspecialchars($row['Destination']),
        'Purpose' => htmlspecialchars($row['Purpose']),
        'StartDate' => $row['StartDate'],
        'EndDate' => $row['EndDate'],
        'Status' => htmlspecialchars($row['Status']),
        'DateRequested' => $row['DateRequested'],
        'FullName' => htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']),
        'Department' => htmlspecialchars($row['DepartmentName'] ?? 'N/A')
    ];
}

$stmt->close();
$conn->close();

echo json_encode($travelOrders);
?>
