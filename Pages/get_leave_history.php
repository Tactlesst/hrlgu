<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connect.php';

$search = trim($_GET['search'] ?? '');

// This query is now correct.
// It starts with LeaveHistory and joins everything else.
$sql = "SELECT 
            hist.ApplicationID,
            hist.StartDate,
            hist.EndDate,
            hist.Status,
            app.ControlNumber, -- Get ControlNumber from LeaveApplication
            emp.FirstName,
            emp.LastName,
            dept.DepartmentName,
            lt.TypeName,
            (SELECT COUNT(*) FROM EmployeeDocument doc WHERE doc.ApplicationID = hist.ApplicationID) AS DocumentCount
        FROM 
            LeaveHistory AS hist
        JOIN 
            Employee AS emp ON hist.EmployeeID = emp.EmployeeID
        JOIN 
            Department AS dept ON emp.DepartmentID = dept.DepartmentID
        JOIN 
            LeaveType AS lt ON hist.LeaveTypeID = lt.LeaveTypeID
        LEFT JOIN
            LeaveApplication AS app ON hist.ApplicationID = app.ApplicationID
        "; // WHERE clause is removed, we want all history

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " WHERE (
                emp.FirstName LIKE ? 
                OR emp.LastName LIKE ? 
                OR app.ControlNumber LIKE ?
                OR lt.TypeName LIKE ?
            )";
    
    $searchTerm = "%{$search}%";
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $types .= "ssss";
}

$sql .= " ORDER BY hist.HistoryID DESC LIMIT 50"; // Use HistoryID if it exists, or ApplicationID

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

echo json_encode($history);
?>