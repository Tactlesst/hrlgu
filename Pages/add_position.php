<?php
include 'db_connect.php';
$name = trim($_POST['position_name']);
$dept = intval($_POST['department_id']);
$stmt = $conn->prepare("INSERT INTO Position (PositionName, DepartmentID) VALUES (?, ?)");
$stmt->bind_param("si", $name, $dept);
$success = $stmt->execute();
$stmt->close();
header('Content-Type: application/json');
if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error message']);
}
?>