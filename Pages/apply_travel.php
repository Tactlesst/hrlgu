<?php
session_start();
// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: Login.php');
    exit;
}

include 'db_connect.php';

// Helper function to generate control number (e.g., TO-20251120-0042)
function generate_control_number($id) {
    return 'TO-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
}

// 2. Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Get and Sanitize Inputs
    $employeeId = $_SESSION['user_id'];
    $destination = trim($_POST['destination'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    // 4. Validation
    if (empty($destination) || empty($purpose) || empty($startDate) || empty($endDate)) {
        // Redirect with error if fields are missing
        header('Location: Employee-Dashboard.php?error=Missing required fields&tab=travel-section');
        exit;
    }

    // 5. Insert Request into Database
    // Note: 'DirectedBy' is NOT set here because this is an employee request
    $stmt = $conn->prepare("INSERT INTO TravelOrder (EmployeeID, Destination, Purpose, StartDate, EndDate, Status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    
    if ($stmt) {
        $stmt->bind_param("issss", $employeeId, $destination, $purpose, $startDate, $endDate);
        
        if ($stmt->execute()) {
            // Get the new ID
            $travelId = $stmt->insert_id;
            $stmt->close();

            // 6. Generate and Update Control Number
            $ctrlNo = generate_control_number($travelId);
            $updateStmt = $conn->prepare("UPDATE TravelOrder SET ControlNumber = ? WHERE TravelID = ?");
            $updateStmt->bind_param("si", $ctrlNo, $travelId);
            $updateStmt->execute();
            $updateStmt->close();

            // 7. Success Redirect
            header('Location: Employee-Dashboard.php?success=Travel request submitted successfully&tab=travel-section');
            exit;
        } else {
            // Database Execution Error
            header('Location: Employee-Dashboard.php?error=Database error: ' . urlencode($stmt->error) . '&tab=travel-section');
            exit;
        }
    } else {
        // Prepare Error
        header('Location: Employee-Dashboard.php?error=Database prepare error&tab=travel-section');
        exit;
    }
    
    $conn->close();
} else {
    // If accessed directly without POST, redirect home
    header('Location: Employee-Dashboard.php');
    exit;
}
?>