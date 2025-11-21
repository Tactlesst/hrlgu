<?php
session_start();
header('Content-Type: application/json');

// Allow both admin AND employee to view details (for their respective dashboards)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connect.php';
$travelId = (int) ($_GET['id'] ?? 0);

$sql = "SELECT 
            t.*, 
            e.FirstName, e.LastName,
            d.DepartmentName,
            p.PositionName
        FROM TravelOrder t
        JOIN Employee e ON t.EmployeeID = e.EmployeeID
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN Position p ON e.PositionID = p.PositionID
        WHERE t.TravelID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $travelId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Not found']);
}
?>