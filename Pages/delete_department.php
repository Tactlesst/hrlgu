<?php
include 'db_connect.php';
$id = intval($_POST['department_id']);
$stmt = $conn->prepare("DELETE FROM Department WHERE DepartmentID=?");
$stmt->bind_param("i", $id);
$success = $stmt->execute();
$stmt->close();

header('Content-Type: application/json');

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error message']);
}
?>