<?php
// FILE: create_admin.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING LINES ---

session_start();
header('Content-Type: application/json');

// **FIX:** Adjust this path to find your db_connect.php file
// This path assumes db_connect.php is one folder UP from /Pages/
include __DIR__ . '/db_connect.php'; 

$response = ['success' => false, 'error' => 'An unknown error occurred.'];

// Security: Only super_admin can create an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    $response['error'] = 'Unauthorized access.';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (empty($username) || empty($password)) {
        $response['error'] = 'Username and password are required.';
        echo json_encode($response);
        exit;
    }

    if (strlen($password) < 8) {
        $response['error'] = 'Password must be at least 8 characters long.';
        echo json_encode($response);
        exit;
    }

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT AdminID FROM Admin WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['error'] = 'This username is already taken.';
        $stmt->close();
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // Securely hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Set the role for the new user as 'admin'
    $role = 'admin';

    // --- **THE FIX IS HERE** ---
    // Removed 'DateCreated' from the INSERT query.
    $sql = "INSERT INTO Admin (Username, Password, Role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // The bind_param now only has 3 strings ("sss")
        $stmt->bind_param("sss", $username, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['error'] = '';
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