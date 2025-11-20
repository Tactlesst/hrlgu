<?php
include 'db_connect.php';

$sql = "SELECT DepartmentID, DepartmentName FROM Department ORDER BY DepartmentID DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deptId = htmlspecialchars($row['DepartmentID']);
        $deptName = htmlspecialchars($row['DepartmentName']);
        echo "<tr>
            <td>{$deptId}</td>
            <td>{$deptName}</td>
            <td>
                <button class='edit-department-btn' data-id='{$deptId}' data-name=\"{$deptName}\">Edit</button>
                <button class='delete-department-btn' data-id='{$deptId}'>Delete</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No departments found.</td></tr>";
}
?>