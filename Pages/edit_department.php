<?php
include 'db_connect.php';
$id = intval($_POST['department_id']);
$name = trim($_POST['department_name']);
$stmt = $conn->prepare("UPDATE Department SET DepartmentName=? WHERE DepartmentID=?");
$stmt->bind_param("si", $name, $id);
$success = $stmt->execute();
$stmt->close();

header('Content-Type: application/json');

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error message']);
}
?>