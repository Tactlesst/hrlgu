<?php
// assign_travel.php
session_start();

// Prevent any output before JSON
ob_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 1. Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

include 'db_connect.php';

function generate_control_number($id) {
    return 'TO-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Admin Inputs
    $employeeId = (int)($_POST['employee_id'] ?? 0);
    $destination = trim($_POST['destination'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    
    // Validation
    if ($employeeId <= 0 || !$destination || !$purpose || !$startDate || !$endDate) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }
    
    // Retrieve Admin Name for the "DirectedBy" field
    $adminId = $_SESSION['user_id'];
    $adminNameQuery = $conn->query("SELECT FirstName, LastName FROM Employee WHERE EmployeeID = $adminId");
    
    if (!$adminNameQuery) {
        echo json_encode(['success' => false, 'error' => 'Failed to retrieve admin information']);
        exit;
    }
    
    $adminRow = $adminNameQuery->fetch_assoc();
    if (!$adminRow) {
        echo json_encode(['success' => false, 'error' => 'Admin not found']);
        exit;
    }
    
    $directedBy = $adminRow['FirstName'] . ' ' . $adminRow['LastName'];

    // 3. Insert as APPROVED Immediately (Since it is a directive)
    $stmt = $conn->prepare("INSERT INTO TravelOrder (EmployeeID, Destination, Purpose, StartDate, EndDate, Status, DirectedBy, DateApproved, ApprovedByAdminID) VALUES (?, ?, ?, ?, ?, 'Approved', ?, NOW(), ?)");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Database prepare error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("isssssi", $employeeId, $destination, $purpose, $startDate, $endDate, $directedBy, $adminId);
    
    if ($stmt->execute()) {
        $travelId = $stmt->insert_id;
        $stmt->close();

        // Generate Control Number
        $ctrlNo = generate_control_number($travelId);
        $updateStmt = $conn->prepare("UPDATE TravelOrder SET ControlNumber = ? WHERE TravelID = ?");
        $updateStmt->bind_param("si", $ctrlNo, $travelId);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Travel order assigned successfully', 'controlNumber' => $ctrlNo]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to generate control number']);
        }
        $updateStmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
        $stmt->close();
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>