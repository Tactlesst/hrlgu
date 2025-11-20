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
    // Fetch employee row
    $sel = $conn->prepare("SELECT 
        EmployeePhoto, Email, ContactNo, EmergencyContactNo, HomeAddress, Password,
        LastName, MiddleName, FirstName, Sex, DepartmentID, PositionID, Birthdate, DateHired,
        ResumePath, DiplomaPath, GovtIDPath, LocalCertificatePath, CivilServiceEligibility,
        PSABirthCertificate, PSAMarriageCertificate, NBIClearance, PoliceClearance, MedicalCertificate,
        PersonalDataSheet, ValidGovID, PRCLicense, TranscriptOfRecords, CertificatesOfTraining,
        ServiceRecord, PerformanceRating, HonorGraduateEligibility, TIN, SSS, PagIBIG, PhilHealth,
        OtherDocuments, Status
        FROM Employee WHERE EmployeeID = ?");
    if (!$sel)
        throw new Exception("Prepare select failed: " . $conn->error);
    $sel->bind_param('i', $id);
    $sel->execute();
    $res = $sel->get_result();
    $row = $res->fetch_assoc();
    $sel->close();

    if (!$row) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Employee not found']);
        exit();
    }

    // Prepare insert into ArchivedEmployee (DateArchived = NOW())
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
        'DateArchived',
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

    $insSql = "INSERT INTO ArchivedEmployee ({$colsList}) VALUES ({$placeholders})";
    $ins = $conn->prepare($insSql);
    if (!$ins)
        throw new Exception("Prepare insert failed: " . $conn->error);

    // Build values in same order. Use DateArchived = NOW()
    $values = [];
    foreach ($cols as $c) {
        if ($c === 'DateArchived') {
            $values[] = date('Y-m-d H:i:s'); // store timestamp
        } else {
            $values[] = array_key_exists($c, $row) ? $row[$c] : null;
        }
    }

    // bind all as strings (MySQL will coerce)
    $types = str_repeat('s', count($values));
    $bind_names = array_merge([$types], $values);
    // bind_param requires references
    $refs = [];
    foreach ($bind_names as $k => $v) {
        $refs[$k] = &$bind_names[$k];
    }
    call_user_func_array([$ins, 'bind_param'], $refs);

    if (!$ins->execute()) {
        throw new Exception("Insert archived failed: " . $ins->error);
    }
    $ins->close();

    // Delete original employee (cascade will remove related rows per schema)
    $del = $conn->prepare("DELETE FROM Employee WHERE EmployeeID = ?");
    if (!$del)
        throw new Exception("Prepare delete failed: " . $conn->error);
    $del->bind_param('i', $id);
    if (!$del->execute()) {
        throw new Exception("Delete failed: " . $del->error);
    }
    $del->close();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    error_log('archive_employee error: ' . $e->getMessage());
    http_response_code(500);
    // Return details for debugging (remove details in production)
    echo json_encode([
        'success' => false,
        'error' => 'Server error while deleting employee',
        'details' => $e->getMessage()
    ]);
    exit();
}
?>