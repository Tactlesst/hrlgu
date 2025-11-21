<?php
session_start();
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connect.php';

// Get status filter from query parameter
$status = isset($_GET['status']) ? $_GET['status'] : 'Pending';
$statuses = array_map('trim', explode(',', $status));

// Build the WHERE clause
$placeholders = implode(',', array_fill(0, count($statuses), '?'));
$sql = "SELECT 
            t.TravelID,
            t.ControlNumber,
            t.Destination,
            t.Purpose,
            t.StartDate,
            t.EndDate,
            t.Status,
            e.FirstName,
            e.LastName,
            e.EmployeeID
        FROM TravelOrder t
        JOIN Employee e ON t.EmployeeID = e.EmployeeID
        WHERE t.Status IN ($placeholders)
        ORDER BY t.TravelID DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

// Bind parameters dynamically
$types = str_repeat('s', count($statuses));
$stmt->bind_param($types, ...$statuses);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>
