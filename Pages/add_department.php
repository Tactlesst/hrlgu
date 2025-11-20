<?php
include 'db_connect.php';

$name = trim($_POST['department_name']);

$stmt = $conn->prepare("INSERT INTO Department (DepartmentName) VALUES (?)");
$stmt->bind_param("s", $name);
$success = $stmt->execute();
$stmt->close();

// Return JSON only (no redirects)
header('Content-Type: application/json');


if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}