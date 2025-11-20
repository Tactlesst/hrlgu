<?php
// FILE: create_leave_type.php (Updated)

header('Content-Type: application/json');
include 'db_connect.php'; 

$response = ['success' => false, 'error' => 'Invalid request.'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode($response);
    exit;
}

// Collect inputs
$name = trim($_POST['TypeName'] ?? '');
$description = trim($_POST['Description'] ?? '');
$unitType = trim($_POST['UnitType'] ?? 'Leave Credit');
$maxDays = intval($_POST['FixedDays'] ?? 0); // Form still uses "FixedDays"
$points = intval($_POST['PointCost'] ?? 0);
$deductFromId = $_POST['DeductFromLeaveTypeID'] ?? null;
$allowDocuments = intval($_POST['AllowDocuments'] ?? 0);
$maxDocuments = intval($_POST['MaxDocuments'] ?? 0);
$usageFrequency = trim($_POST['UsageFrequency'] ?? 'NA');

// Basic validation
if ($name === '') {
    $response['error'] = 'Leave type name is required.';
    echo json_encode($response);
    exit;
}

// --- Simplified Validation ---
$allowedUnitTypes = ['Leave Credit', 'Fixed Days'];
$allowedFrequencies = ['NA', 'PerYear', 'PerEvent'];

if (!in_array($unitType, $allowedUnitTypes)) {
    $unitType = 'Leave Credit'; // Default
}
if (!in_array($usageFrequency, $allowedFrequencies)) {
    $usageFrequency = 'NA'; // Default
}

// --- Enforce logic based on unit type ---
if ($unitType === 'Fixed Days') {
    $points = 0;
    $deductFromId = null;
    if ($maxDays < 1) $maxDays = 1;
    if ($usageFrequency === 'NA') $usageFrequency = 'PerYear'; 
} else {
    // 'Leave Credit'
    $maxDays = 0; // Use the $maxDays variable
    $usageFrequency = 'NA'; 
    if ($deductFromId === '') $deductFromId = null;
}
if ($allowDocuments === 0) {
    $maxDocuments = 0;
}

// Check duplicate name
$stmt = $conn->prepare("SELECT LeaveTypeID FROM LeaveType WHERE TypeName = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $response['error'] = 'A leave type with this name already exists.';
    $stmt->close();
    echo json_encode($response);
    exit;
}
$stmt->close();

// --- **THE FIX IS HERE** ---
// Changed 'FixedDays' to 'MaxDaysPerUsage' in the SQL query
$sql = "INSERT INTO LeaveType 
            (TypeName, UnitType, MaxDaysPerUsage, Description, PointCost, DeductFromLeaveTypeID, AllowDocuments, MaxDocuments, UsageFrequency) 
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['error'] = 'Prepare failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Ensure deductFromId is proper (NULL or int)
$deduct = ($deductFromId === null || $deductFromId === '') ? null : intval($deductFromId);

// --- Bind types and execute (ssisiiiis) ---
// We use the $maxDays variable (which got its value from 'FixedDays' in the form)
if (!$stmt->bind_param("ssisiiiis", $name, $unitType, $maxDays, $description, $points, $deduct, $allowDocuments, $maxDocuments, $usageFrequency)) {
    $response['error'] = 'Bind param failed: ' . $stmt->error;
    $stmt->close();
    echo json_encode($response);
    exit;
}

if ($stmt->execute()) {
    $response['success'] = true;
    $response['error'] = '';
} else {
    $response['error'] = 'Database error: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>