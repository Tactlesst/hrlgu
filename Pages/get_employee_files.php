<?php
session_start();
header('Content-Type: application/json');
include 'db_connect.php';

// Get the ID from the SESSION
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated.']);
    exit;
}
$id = intval($_SESSION['user_id']); 

$files = [];

// Get main document fields from Employee table
$sql = "SELECT 
    ResumePath, DiplomaPath, GovtIDPath, LocalCertificatePath, CivilServiceEligibility, PSABirthCertificate, PSAMarriageCertificate,
    NBIClearance, PoliceClearance, MedicalCertificate, PersonalDataSheet, ValidGovID, PRCLicense, TranscriptOfRecords,
    CertificatesOfTraining, ServiceRecord, PerformanceRating, HonorGraduateEligibility, TIN, SSS, PagIBIG, PhilHealth, OtherDocuments
    FROM Employee WHERE EmployeeID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    foreach ($row as $docType => $filePath) {
        $files[] = [
            'DocType' => $docType,
            'FilePath' => $filePath,
            'Type' => 'standard' // <-- ADD THIS FLAG
        ];
    }
}
$stmt->close();

// Get all EmployeeDocument uploads
$sql2 = "SELECT DocType, FilePath FROM EmployeeDocument WHERE EmployeeID=?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row2 = $res2->fetch_assoc()) {
    $files[] = [
        'DocType' => $row2['DocType'],
        'FilePath' => $row2['FilePath'],
        'Type' => 'extra' // <-- ADD THIS FLAG
    ];
}
$stmt2->close();

$conn->close();
echo json_encode($files);
?>