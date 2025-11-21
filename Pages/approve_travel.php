<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing ID']);
    exit;
}

include 'db_connect.php';
$travelId = (int) $_POST['id'];
$adminId = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE TravelOrder SET Status = 'Approved', ApprovedByAdminID = ?, DateApproved = NOW() WHERE TravelID = ?");
$stmt->bind_param("ii", $adminId, $travelId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>