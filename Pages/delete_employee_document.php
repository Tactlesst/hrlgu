<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    die("Unauthorized");
}
include 'db_connect.php';

$employeeId = $_SESSION['user_id'];
$field = $_POST['field'] ?? '';
$allowedFields = [
    "ResumePath",
    "DiplomaPath",
    "GovtIDPath",
    "LocalCertificatePath",
    "CivilServiceEligibility",
    "PSABirthCertificate",
    "PSAMarriageCertificate",
    "NBIClearance",
    "PoliceClearance",
    "MedicalCertificate",
    "PersonalDataSheet",
    "ValidGovID",
    "PRCLicense",
    "TranscriptOfRecords",
    "CertificatesOfTraining",
    "ServiceRecord",
    "PerformanceRating",
    "HonorGraduateEligibility",
    "TIN",
    "SSS",
    "PagIBIG",
    "PhilHealth",
    "OtherDocuments"
];

if (!in_array($field, $allowedFields)) {
    die("Invalid field");
}

// Get current file path
$stmt = $conn->prepare("SELECT $field FROM Employee WHERE EmployeeID = ?");
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$stmt->bind_result($filePath);
$stmt->fetch();
$stmt->close();

// Delete file from server
if ($filePath && file_exists("../" . str_replace("/HR-Leave-Management-System/", "", $filePath))) {
    unlink("../" . str_replace("/HR-Leave-Management-System/", "", $filePath));
}

// Remove path from DB
$stmt = $conn->prepare("UPDATE Employee SET $field = '' WHERE EmployeeID = ?");
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$stmt->close();

header("Location: Employee-Dashboard.php?tab=documents");
exit;
?>