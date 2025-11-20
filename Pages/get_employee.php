<?php
// filepath: d:\Xampp\htdocs\HR-Leave-Management-System\Pages\get_employee.php
header('Content-Type: application/json');
include 'db_connect.php';

$id = intval($_GET['id'] ?? 0);
$sql = "SELECT e.EmployeeID, e.FirstName, e.MiddleName, e.LastName, e.ContactNo, e.Email, e.Status, e.DateHired, e.Birthdate, e.EmployeePhoto,
        e.DepartmentID, e.PositionID,
        e.ResumePath, e.DiplomaPath, e.GovtIDPath, e.LocalCertificatePath, e.CivilServiceEligibility, e.PSABirthCertificate, e.PSAMarriageCertificate,
        e.NBIClearance, e.PoliceClearance, e.MedicalCertificate, e.PersonalDataSheet, e.ValidGovID, e.PRCLicense, e.TranscriptOfRecords,
        e.CertificatesOfTraining, e.ServiceRecord, e.PerformanceRating, e.HonorGraduateEligibility, e.TIN, e.SSS, e.PagIBIG, e.PhilHealth, e.OtherDocuments,
        d.DepartmentName, p.PositionName,
        TIMESTAMPDIFF(YEAR, e.Birthdate, CURDATE()) AS Age
        FROM Employee e
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN `Position` p ON e.PositionID = p.PositionID
        WHERE e.EmployeeID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();
$stmt->close();
$conn->close();
echo json_encode($data);
?>