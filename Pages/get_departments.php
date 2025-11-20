<?php
header('Content-Type: application/json');
include 'db_connect.php';

$sql = "SELECT DepartmentID, DepartmentName FROM Department ORDER BY DepartmentName ASC";
$result = $conn->query($sql);

$departments = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = [
            'DepartmentID' => $row['DepartmentID'],
            'DepartmentName' => htmlspecialchars($row['DepartmentName'] ?? 'N/A')
        ];
    }
}

echo json_encode($departments);
$conn->close();
?>
