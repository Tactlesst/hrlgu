<?php
include 'db_connect.php';

$sql = "SELECT 
            a.EmployeeID,
            a.FirstName,
            a.MiddleName,
            a.LastName,
            a.Birthdate,
            d.DepartmentName,
            p.PositionName,
            a.Status,
            a.DateHired,
            a.DateArchived
        FROM ArchivedEmployee a
        LEFT JOIN Department d ON a.DepartmentID = d.DepartmentID
        LEFT JOIN `Position` p ON a.PositionID = p.PositionID
        ORDER BY a.EmployeeID DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = htmlspecialchars($row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName']);
        echo "<tr>
            <td>{$row['EmployeeID']}</td>
            <td>{$fullName}</td>
            <td>{$row['Birthdate']}</td>
            <td>" . htmlspecialchars($row['DepartmentName']) . "</td>
            <td>" . htmlspecialchars($row['PositionName']) . "</td>
            <td>{$row['DateHired']}</td>
            <td>{$row['DateArchived']}</td>
            <td>
                <button class='restore-employee-btn btn-restore' data-id='{$row['EmployeeID']}' style='background:#007bff;color:#fff;border:none;padding:6px 8px;border-radius:4px;cursor:pointer;'>Restore</button>
                <button class='delete-employee-btn btn-delete' data-id='{$row['EmployeeID']}' style='background:#dc3545;color:#fff;border:none;padding:6px 8px;border-radius:4px;cursor:pointer;margin-left:6px;'>Delete</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No employees found.</td></tr>";
}
?>