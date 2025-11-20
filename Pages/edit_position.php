<?php
include 'db_connect.php';
$id = intval($_POST['position_id']);
$name = trim($_POST['position_name']);
$dept = intval($_POST['department_id']);
$stmt = $conn->prepare("UPDATE Position SET PositionName=?, DepartmentID=? WHERE PositionID=?");
$stmt->bind_param("sii", $name, $dept, $id);
$success = $stmt->execute();
$stmt->close();

header('Content-Type: application/json');

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error message']);
}
?>