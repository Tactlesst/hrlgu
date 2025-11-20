<?php
include 'db_connect.php';
header('Content-Type: application/json');
$positions = [];

if (isset($_GET['department_id'])) {
    // Get positions for specific department
    $deptId = intval($_GET['department_id']);
    $result = $conn->query("SELECT PositionID, PositionName FROM `Position` WHERE DepartmentID = $deptId ORDER BY PositionName ASC");
} else {
    // Get all positions
    $result = $conn->query("SELECT PositionID, PositionName FROM `Position` ORDER BY PositionName ASC");
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $positions[] = [
            'PositionID' => $row['PositionID'],
            'PositionName' => htmlspecialchars($row['PositionName'] ?? 'N/A')
        ];
    }
}

echo json_encode($positions);
$conn->close();
?>