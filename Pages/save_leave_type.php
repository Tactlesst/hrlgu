<?php
header('Content-Type: application/json');
include 'db_connect.php';

// --- 1. Get All Inputs from the New Modal ---
$id = intval($_POST['LeaveTypeID'] ?? 0);
$name = trim($_POST['TypeName'] ?? '');
$description = trim($_POST['Description'] ?? '');

// New policy logic
$unitType = $_POST['UnitType'] ?? 'Leave Credit'; // 'Leave Credit' or 'Fixed Days'
$fixedDays = intval($_POST['FixedDays'] ?? 0);

// Credit-specific fields
$points = intval($_POST['PointCost'] ?? 0);
$deductFromId = $_POST['DeductFromLeaveTypeID'] ?? null;

// Document fields
$allowDocuments = intval($_POST['AllowDocuments'] ?? 0);
$maxDocuments = intval($_POST['MaxDocuments'] ?? 0);


// --- 2. Validation ---
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid Leave Type ID.']);
    exit;
}
if ($name === '') {
    echo json_encode(['success' => false, 'error' => 'Leave type name is required.']);
    exit;
}
if (!preg_match('/^[A-Za-z\s\-]+$/', $name)) {
    echo json_encode(['success' => false, 'error' => 'Name must only contain letters, spaces, and hyphens.']);
    exit;
}
if (!in_array($unitType, ['Leave Credit', 'Fixed Days'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid unit type.']);
    exit;
}

// --- 3. Enforce Logic Based on UnitType ---
if ($unitType == 'Fixed Days') {
    $points = 0;
    $deductFromId = null;
    if ($fixedDays < 1 || $fixedDays > 365) {
        echo json_encode(['success' => false, 'error' => 'Fixed days must be between 1 and 365.']);
        exit;
    }
} else {
    $fixedDays = 0;
    if ($deductFromId === '') {
        $deductFromId = null;
    }
}

// Validate documents
if ($allowDocuments) {
    if ($maxDocuments < 1 || $maxDocuments > 10) {
        echo json_encode(['success' => false, 'error' => 'Max documents must be between 1 and 10.']);
        exit;
    }
} else {
    $maxDocuments = 0;
}

// --- 4. Check for Duplicate Name (on a different record) ---
$stmt = $conn->prepare("SELECT LeaveTypeID FROM LeaveType WHERE TypeName = ? AND LeaveTypeID != ?");
$stmt->bind_param("si", $name, $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'A leave type with this name already exists.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// --- 5. Database Update ---
$sql = "UPDATE LeaveType SET 
            TypeName = ?, 
            UnitType = ?, 
            FixedDays = ?, 
            Description = ?, 
            PointCost = ?, 
            DeductFromLeaveTypeID = ?, 
            AllowDocuments = ?, 
            MaxDocuments = ?
        WHERE 
            LeaveTypeID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database error (prepare): ' . $conn->error]);
    exit;
}

// All fields + ID at the end. Types: s, s, i, s, i, i, i, i, i
$stmt->bind_param(
    "ssisisiii",
    $name,
    $unitType,
    $fixedDays,
    $description,
    $points,
    $deductFromId,
    $allowDocuments,
    $maxDocuments,
    $id
);

$success = $stmt->execute();
$errorMsg = $stmt->error;
$stmt->close();
$conn->close();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error updating leave type: ' . htmlspecialchars($errorMsg)]);
}
?>