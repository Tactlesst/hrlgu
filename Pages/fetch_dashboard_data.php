<?php
// fetch_dashboard_data.php
include 'db_connect.php'; // your DB connection

// Count by status
$statusQuery = "
    SELECT Status, COUNT(*) as total
    FROM LeaveApplication
    GROUP BY Status
";
$statusResult = $conn->query($statusQuery);

$statusData = [
    "Pending" => 0,
    "Approved" => 0,
    "Rejected" => 0
];
while ($row = $statusResult->fetch_assoc()) {
    $statusData[$row['Status']] = (int) $row['total'];
}

// Count by leave type
$typeQuery = "
    SELECT lt.TypeName, COUNT(*) as total
    FROM LeaveApplication la
    JOIN LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
    WHERE la.Status = 'Approved'
    GROUP BY lt.TypeName
";
$typeResult = $conn->query($typeQuery);

$typeLabels = [];
$typeCounts = [];
while ($row = $typeResult->fetch_assoc()) {
    $typeLabels[] = $row['TypeName'];
    $typeCounts[] = (int) $row['total'];
}

echo json_encode([
    "statusData" => $statusData,
    "typeLabels" => $typeLabels,
    "typeCounts" => $typeCounts
]);
?>