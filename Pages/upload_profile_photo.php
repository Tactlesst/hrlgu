<?php
// FILE: /Pages/upload_profile_photo.php
session_start();
header('Content-Type: application/json');
include 'db_connect.php'; // Make sure this path is correct!

$response = ['success' => false, 'error' => 'Unknown error.'];

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'User not authenticated.';
    echo json_encode($response);
    exit;
}
$employeeId = (int)$_SESSION['user_id'];

// 2. Check if a file was sent
if (isset($_FILES['croppedImage'])) {

    // 3. Define the upload directory
    $uploadDir = __DIR__ . "/../Documents/ProfilePhotos/"; 
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // 4. Create a unique filename
    $fileName = "emp_{$employeeId}_" . time() . ".png";
    $targetPath = $uploadDir . $fileName;

    // 5. Move the uploaded file
    if (move_uploaded_file($_FILES['croppedImage']['tmp_name'], $targetPath)) {
        
        // 6. Create the web-accessible path to save in the DB
        // --- **THE FIX IS HERE** ---
        // Removed the extra "hrlgu/" from the path
        $webPath = "Documents/ProfilePhotos/" . $fileName;

        // 7. Update the Employee table
        $sql = "UPDATE Employee SET EmployeePhoto = ? WHERE EmployeeID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $webPath, $employeeId);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['newPath'] = $webPath; // Send the new path back
        } else {
            $response['error'] = 'Database update failed.';
        }
        $stmt->close();
    } else {
        $response['error'] = 'Failed to move uploaded file.';
    }
} else {
    $response['error'] = 'No file data received.';
}

$conn->close();
echo json_encode($response);
?>