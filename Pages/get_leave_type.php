<?php
include 'db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM LeaveType WHERE LeaveTypeID = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Leave type not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>