<?php
session_start();
include 'db_connect.php';

// --- Start JSON Response ---
header('Content-Type: application/json');
$response = ['success' => false, 'error' => 'Unknown error'];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'User not authenticated.';
    echo json_encode($response);
    exit;
}

$employeeId = $_SESSION['user_id'];
$field = $_POST['field'] ?? '';

$allowedFields = [
    "ResumePath", "DiplomaPath", "GovtIDPath", "LocalCertificatePath",
    "CivilServiceEligibility", "PSABirthCertificate", "PSAMarriageCertificate",
    "NBIClearance", "PoliceClearance", "MedicalCertificate", "PersonalDataSheet",
    "ValidGovID", "PRCLicense", "TranscriptOfRecords", "CertificatesOfTraining",
    "ServiceRecord", "PerformanceRating", "HonorGraduateEligibility", "TIN",
    "SSS", "PagIBIG", "PhilHealth", "OtherDocuments"
];

// Check if the field is valid
if (!in_array($field, $allowedFields)) {
    $response['error'] = 'Invalid document type specified.';
    echo json_encode($response);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    $response['error'] = 'No file uploaded or an upload error occurred. Code: ' . $_FILES['document']['error'];
    echo json_encode($response);
    exit;
}

// File upload handling
// **IMPORTANT**: This path goes UP from /Pages and into /Documents
// Adjust if your folder structure is different
$uploadDir = __DIR__ . "/../Documents/EmployeeFiles/$employeeId/"; 
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$filename = basename($_FILES['document']['name']);
$targetPath = $uploadDir . time() . "_" . $filename;

if (move_uploaded_file($_FILES['document']['tmp_name'], $targetPath)) {
    
    // Save path to DB (relative path)
    // **IMPORTANT**: This path must be correct for your website
    $dbPath = "/HR-Leave-Management-System/Documents/EmployeeFiles/$employeeId/" . time() . "_" . $filename;
    
    // The query is safe because $field was checked against the whitelist
    $stmt = $conn->prepare("UPDATE Employee SET `$field` = ? WHERE EmployeeID = ?");
    $stmt->bind_param("si", $dbPath, $employeeId);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['error'] = '';
    } else {
        $response['error'] = 'Database update failed: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['error'] = 'File move failed. Check directory permissions.';
}

$conn->close();
echo json_encode($response);
// --- We no longer use header() or die() ---
?>