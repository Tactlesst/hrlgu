<?php
if (!isset($conn)) {
    include_once 'db_connect.php';
}

// --- FIX 1: Added "la.DurationDays" to the SQL query ---
$sql = "SELECT la.ApplicationID, la.EmployeeID, la.LeaveTypeID, la.StartDate, la.EndDate, la.Reason, la.DateRequested,
               la.DurationDays, -- Added this
               e.FirstName, e.LastName, d.DepartmentName, lt.TypeName, lt.PointCost
        FROM LeaveApplication la
        JOIN Employee e ON la.EmployeeID = e.EmployeeID
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        JOIN LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
        WHERE la.Status = 'Pending'
        ORDER BY la.DateRequested DESC";

$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $appId = (int) $row['ApplicationID'];
        $applicant = htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']);
        $dept = htmlspecialchars($row['DepartmentName'] ?? '—');
        $type = htmlspecialchars($row['TypeName']);
        
        // --- FIX 2: Calculate the total cost ---
        $duration = (float) $row['DurationDays'];
        $pointCostPerDay = (float) $row['PointCost'];
        $totalCost = $duration * $pointCostPerDay;
        
        // Format to 3 decimal places (to match our database)
        $cost = number_format($totalCost, 3);
        // --- END OF FIX ---

        $start = htmlspecialchars($row['StartDate']);
        $end = htmlspecialchars($row['EndDate']);
        $reason = htmlspecialchars($row['Reason']);
        
        // This 'echo' block is now correct
        echo "<tr id='app-row-{$appId}'>
                <td>{$applicant}</td>
                <td>{$dept}</td>
                <td>{$type}</td>
                <td>{$cost}</td>
                <td>{$start}</td>
                <td>{$end}</td>
                <td>{$reason}</td>
                <td>
                    <button class='view-application-btn' data-id='{$appId}' style='background:#6c757d;color:#fff;border:none;padding:6px 8px;border-radius:4px;cursor:pointer;'>View</button>
                    
                    <button class='approve-app-btn' data-id='{$appId}' style='background:#007bff;color:#fff;border:none;padding:6px 8px;border-radius:4px;cursor:pointer;margin-left:6px;'>Approve</button>
                    <button class='reject-app-btn' data-id='{$appId}' style='background:#dc3545;color:#fff;border:none;padding:6px 8px;border-radius:4px;cursor:pointer;margin-left:6px;'>Reject</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align:center;color:#666;'>No pending applications</td></tr>";
}
?>