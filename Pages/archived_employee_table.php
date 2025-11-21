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
            <td style='display:flex;gap:8px;align-items:center;justify-content:center;'>
                <button class='restore-employee-btn btn-restore' data-id='{$row['EmployeeID']}' title='Restore Employee' style='background:none;border:none;padding:8px;cursor:pointer;color:#007bff;font-size:18px;transition:all 0.3s ease;' onmouseover='this.style.color=\"#0056b3\"; this.style.transform=\"scale(1.2)\"' onmouseout='this.style.color=\"#007bff\"; this.style.transform=\"scale(1)\"'>↩️</button>
                <button class='delete-employee-btn btn-delete' data-id='{$row['EmployeeID']}' title='Delete Employee' style='background:none;border:none;padding:8px;cursor:pointer;color:#dc3545;font-size:18px;transition:all 0.3s ease;' onmouseover='this.style.color=\"#c82333\"; this.style.transform=\"scale(1.2)\"' onmouseout='this.style.color=\"#dc3545\"; this.style.transform=\"scale(1)\"'>🗑️</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No employees found.</td></tr>";
}
?>