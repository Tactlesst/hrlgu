<?php
// FILE: delete_admin.php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

$response = ['success' => false, 'error' => 'An unknown error occurred.'];

// Security: Only super_admin can delete an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    $response['error'] = 'Unauthorized access.';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminId = intval($_POST['id'] ?? 0);

    // Validation
    if ($adminId <= 0) {
        $response['error'] = 'Invalid Admin ID.';
        echo json_encode($response);
        exit;
    }
    
    // --- CRITICAL: Prevent self-deletion ---
    if ($adminId == $_SESSION['user_id']) {
        $response['error'] = 'You cannot delete your own account.';
        echo json_encode($response);
        exit;
    }

    // Delete the admin
    // We also double-check the Role is 'admin' so you can't delete other super_admins
    $sql = "DELETE FROM Admin WHERE AdminID = ? AND Role = 'admin'";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $adminId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['error'] = '';
            } else {
                $response['error'] = 'Admin not found or is not a regular admin.';
            }
        } else {
            $response['error'] = 'Database error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['error'] = 'Database prepare error: ' . $conn->error;
    }
}

$conn->close();
echo json_encode($response);
?>