<?php
include 'db_connect.php';

$sql = "SELECT 
            e.EmployeeID,
            e.FirstName,
            e.MiddleName,
            e.LastName,
            e.Birthdate,
            d.DepartmentName,
            p.PositionName,
            e.Status,
            e.DateHired
        FROM Employee e
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN `Position` p ON e.PositionID = p.PositionID
        ORDER BY e.EmployeeID DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = htmlspecialchars(($row['FirstName'] ?? '') . ' ' . ($row['MiddleName'] ?? '') . ' ' . ($row['LastName'] ?? ''));
        echo "<tr>
            <td>{$row['EmployeeID']}</td>
            <td>{$fullName}</td>
            <td>{$row['Birthdate']}</td>
            <td>" . htmlspecialchars($row['DepartmentName'] ?? 'N/A') . "</td>
            <td>" . htmlspecialchars($row['PositionName'] ?? 'N/A') . "</td>
            <td>" . htmlspecialchars($row['Status'] ?? 'N/A') . "</td>
            <td>{$row['DateHired']}</td>
            <td style='text-align: center; display: flex; gap: 10px; justify-content: center;'>
                <i class='fas fa-edit edit-employee-btn' data-id='{$row['EmployeeID']}' style='font-size: 18px; color: #007bff; cursor: pointer; transition: all 0.3s ease;' title='Edit Employee'></i>
                <i class='fas fa-eye view-employee-btn' data-id='{$row['EmployeeID']}' style='font-size: 18px; color: #28a745; cursor: pointer; transition: all 0.3s ease;' title='View Employee'></i>
                <i class='fas fa-trash-alt archive-employee-btn' data-id='{$row['EmployeeID']}' style='font-size: 18px; color: #dc3545; cursor: pointer; transition: all 0.3s ease;' title='Archive Employee'></i>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No employees found.</td></tr>";
}
?>