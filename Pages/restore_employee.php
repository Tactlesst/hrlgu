<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

include 'db_connect.php';
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

$id = isset($_POST['EmployeeID']) ? intval($_POST['EmployeeID']) : (isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid employee id']);
    exit();
}

$conn->begin_transaction();

try {
    // Fetch archived employee row
    $sel = $conn->prepare("SELECT 
        EmployeePhoto, Email, ContactNo, EmergencyContactNo, HomeAddress, Password,
        LastName, MiddleName, FirstName, Sex, DepartmentID, PositionID, Birthdate, DateHired,
        ResumePath, DiplomaPath, GovtIDPath, LocalCertificatePath, CivilServiceEligibility,
        PSABirthCertificate, PSAMarriageCertificate, NBIClearance, PoliceClearance, MedicalCertificate,
        PersonalDataSheet, ValidGovID, PRCLicense, TranscriptOfRecords, CertificatesOfTraining,
        ServiceRecord, PerformanceRating, HonorGraduateEligibility, TIN, SSS, PagIBIG, PhilHealth,
        OtherDocuments, Status
        FROM ArchivedEmployee WHERE EmployeeID = ?");
    if (!$sel)
        throw new Exception("Prepare select failed: " . $conn->error);
    $sel->bind_param('i', $id);
    $sel->execute();
    $res = $sel->get_result();
    $row = $res->fetch_assoc();
    $sel->close();

    if (!$row) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Archived employee not found']);
        exit();
    }

    // Columns for Employee (exclude DateArchived)
    $cols = [
        'EmployeePhoto',
        'Email',
        'ContactNo',
        'EmergencyContactNo',
        'HomeAddress',
        'Password',
        'LastName',
        'MiddleName',
        'FirstName',
        'Sex',
        'DepartmentID',
        'PositionID',
        'Birthdate',
        'DateHired',
        'ResumePath',
        'DiplomaPath',
        'GovtIDPath',
        'LocalCertificatePath',
        'CivilServiceEligibility',
        'PSABirthCertificate',
        'PSAMarriageCertificate',
        'NBIClearance',
        'PoliceClearance',
        'MedicalCertificate',
        'PersonalDataSheet',
        'ValidGovID',
        'PRCLicense',
        'TranscriptOfRecords',
        'CertificatesOfTraining',
        'ServiceRecord',
        'PerformanceRating',
        'HonorGraduateEligibility',
        'TIN',
        'SSS',
        'PagIBIG',
        'PhilHealth',
        'OtherDocuments',
        'Status'
    ];

    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    $colsList = implode(',', $cols);

    $insSql = "INSERT INTO Employee ({$colsList}) VALUES ({$placeholders})";
    $ins = $conn->prepare($insSql);
    if (!$ins)
        throw new Exception("Prepare insert failed: " . $conn->error);

    $values = [];
    foreach ($cols as $c) {
        $values[] = array_key_exists($c, $row) ? $row[$c] : null;
    }

    $types = str_repeat('s', count($values));
    $bind_names = array_merge([$types], $values);
    $refs = [];
    foreach ($bind_names as $k => $v) {
        $refs[$k] = &$bind_names[$k];
    }
    call_user_func_array([$ins, 'bind_param'], $refs);

    if (!$ins->execute()) {
        throw new Exception("Insert employee failed: " . $ins->error);
    }
    $ins->close();

    // Delete from ArchivedEmployee
    $del = $conn->prepare("DELETE FROM ArchivedEmployee WHERE EmployeeID = ?");
    if (!$del)
        throw new Exception("Prepare delete failed: " . $conn->error);
    $del->bind_param('i', $id);
    if (!$del->execute()) {
        throw new Exception("Delete archived failed: " . $del->error);
    }
    $del->close();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    error_log('restore_employee error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error while restoring employee']);
    exit();
}
?>