<?php
include 'db_connect.php';

$sql = "SELECT p.PositionID, p.PositionName, d.DepartmentName, p.DepartmentID
        FROM `Position` p
        LEFT JOIN Department d ON p.DepartmentID = d.DepartmentID
        ORDER BY p.PositionID DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $positionId = htmlspecialchars($row['PositionID']);
        $positionName = htmlspecialchars($row['PositionName']);
        $departmentName = htmlspecialchars($row['DepartmentName']);
        $departmentId = htmlspecialchars($row['DepartmentID']);

        echo "<tr>
            <td>{$positionId}</td>
            <td>{$positionName}</td>
            <td>{$departmentName}</td>
            <td>
                <button class='edit-position-btn'
                    data-id=\"{$positionId}\"
                    data-name=\"{$positionName}\"
                    data-dept=\"{$departmentId}\">Edit</button>
                <button class='delete-position-btn' data-id=\"{$positionId}\">Delete</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No positions found.</td></tr>";
}
?>