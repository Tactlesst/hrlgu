<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Use JSON for errors, just like the end of the script
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit();
}

include 'db_connect.php';

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

// Allowed file types and max size (2MB)
$allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
$maxSize = 2 * 1024 * 1024; // 2MB

// --- This helper function is not used in the main logic,
// but it's part of your file.
function uploadFile($inputName, $uploadDir = "uploads/")
{
    global $allowedTypes, $maxSize;
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        // Don't echo HTML/JS in a JSON-based script.
        // This should be handled by client-side validation.
        return null; 
    }
    if ($_FILES[$inputName]['size'] > $maxSize) {
        return null;
    }
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);
    $filename = uniqid() . '_' . basename($_FILES[$inputName]['name']);
    $targetPath = $uploadDir . $filename;
    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    return null;
}

function uploadMultipleFiles($inputName, $uploadDir = "uploads/")
{
    global $allowedTypes, $maxSize;
    $paths = [];
    if (!isset($_FILES[$inputName]))
        return $paths;
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);

    $tmpNames = $_FILES[$inputName]['tmp_name'];
    // If only one file is uploaded, convert to array for uniform handling
    if (!is_array($tmpNames)) {
        $tmpNames = [$tmpNames];
        $_FILES[$inputName]['name'] = [$_FILES[$inputName]['name']];
        $_FILES[$inputName]['error'] = [$_FILES[$inputName]['error']];
        $_FILES[$inputName]['size'] = [$_FILES[$inputName]['size']];
    }

    foreach ($tmpNames as $key => $tmpName) {
        if ($_FILES[$inputName]['error'][$key] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES[$inputName]['name'][$key], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedTypes))
                continue;
            if ($_FILES[$inputName]['size'][$key] > $maxSize)
                continue;
            $filename = uniqid() . '_' . basename($_FILES[$inputName]['name'][$key]);
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $paths[] = $targetPath;
            }
        }
    }
    return $paths;
}

// --- Set header to JSON ---
// This entire script should return JSON, not JS alerts
header('Content-Type: application/json');

// Validate required fields
$required = [
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'password',
    'sex',
    'department',
    'position',
    'birthdate',
    'date_hired',
    'status'
];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
        exit();
    }
}

// --- Note on file validation ---
// Requiring this many files on *creation* is brittle.
// This was in your original script, but consider making them optional.
$fileRequired = [
    'resume',
    'govtid',
    'localcert',
    'psa_birth',
    'nbi_clearance',
    'police_clearance',
    'pds',
    'gov_id'
];
foreach ($fileRequired as $fileField) {
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => "Please upload required file: $fileField"]);
        exit();
    }
}

// Prepare data
$firstName = trim($_POST['first_name']);
$middleName = trim($_POST['middle_name']);
$lastName = trim($_POST['last_name']);
$email = trim($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$contactNo = trim($_POST['contact_no']);
$emergencyContactNo = trim($_POST['emergency_contact_no']);
$sex = $_POST['sex'];
$departmentID = intval($_POST['department']);
$positionID = intval($_POST['position']);
$birthdate = $_POST['birthdate'];
$dateHired = $_POST['date_hired'];
$status = $_POST['status'];

// File uploads (single)
$resume = uploadFile('resume');
$diploma = uploadFile('diploma');
$govtid = uploadFile('govtid');
$localcert = uploadFile('localcert');
$cse = uploadFile('cse');
$psa_birth = uploadFile('psa_birth');
$psa_marriage = uploadFile('psa_marriage');
$nbi_clearance = uploadFile('nbi_clearance');
$police_clearance = uploadFile('police_clearance');
$medical_cert = uploadFile('medical_cert');
$pds = uploadFile('pds');
$gov_id = uploadFile('gov_id');
$prc_license = uploadFile('prc_license');
$tor = uploadFile('tor');
$cert_training = uploadFile('cert_training');
$service_record = uploadFile('service_record');
$performance_rating = uploadFile('performance_rating');
$honor_grad = uploadFile('honor_grad');
$tin = uploadFile('tin');
$sss = uploadFile('sss');
$pagibig = uploadFile('pagibig');
$philhealth = uploadFile('philhealth');

// File uploads (multiple for other_docs)
$other_docs_arr = uploadMultipleFiles('other_docs');
$other_docs = !empty($other_docs_arr) ? json_encode($other_docs_arr) : null;

$defaultPhoto = "Pictures/DefaultPicture.jpg";

// --- Main Employee Insert ---
$stmt = $conn->prepare(
    "INSERT INTO Employee (
        EmployeePhoto,
        FirstName, MiddleName, LastName, Email, Password, ContactNo, EmergencyContactNo, Sex,
        DepartmentID, PositionID, Birthdate, DateHired,
        ResumePath, DiplomaPath, GovtIDPath, LocalCertificatePath, CivilServiceEligibility, PSABirthCertificate,
        PSAMarriageCertificate, NBIClearance, PoliceClearance, MedicalCertificate, PersonalDataSheet,
        ValidGovID, PRCLicense, TranscriptOfRecords, CertificatesOfTraining, ServiceRecord,
        PerformanceRating, HonorGraduateEligibility, TIN, SSS, PagIBIG, PhilHealth, OtherDocuments,
        Status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database error (prepare): ' . $conn->error]);
    exit();
}

// Note: Your original bind_param string was wrong. It was "s...i" + 27 "s".
// It should be "s...ii" + 26 "s", for 37 total params.
$bind_types = "sssssssssiissssssssssssssssssssssssss"; // 1 s, 8 s, 2 i, 26 s

$stmt->bind_param(
    $bind_types,
    $defaultPhoto,         // 1 (s)
    $firstName,            // 2 (s)
    $middleName,           // 3 (s)
    $lastName,             // 4 (s)
    $email,                // 5 (s)
    $password,             // 6 (s)
    $contactNo,            // 7 (s)
    $emergencyContactNo,   // 8 (s)
    $sex,                  // 9 (s)
    $departmentID,         // 10 (i)
    $positionID,           // 11 (i)
    $birthdate,            // 12 (s)
    $dateHired,            // 13 (s)
    $resume,               // 14 (s)
    $diploma,              // 15 (s)
    $govtid,               // 16 (s)
    $localcert,            // 17 (s)
    $cse,                  // 18 (s)
    $psa_birth,            // 19 (s)
    $psa_marriage,         // 20 (s)
    $nbi_clearance,        // 21 (s)
    $police_clearance,     // 22 (s)
    $medical_cert,         // 23 (s)
    $pds,                  // 24 (s)
    $gov_id,               // 25 (s)
    $prc_license,          // 26 (s)
    $tor,                  // 27 (s)
    $cert_training,        // 28 (s)
    $service_record,       // 29 (s)
    $performance_rating,   // 30 (s)
    $honor_grad,           // 31 (s)
    $tin,                  // 32 (s)
    $sss,                  // 33 (s)
    $pagibig,              // 34 (s)
    $philhealth,           // 35 (s)
    $other_docs,           // 36 (s)
    $status                // 37 (s)
);

$success = $stmt->execute();
$errorMsg = $stmt->error; // Get error before closing
$newEmployeeId = $conn->insert_id; // Get the new employee's ID
$stmt->close();

// --- START: CORRECTED LEAVE AVAILABILITY BLOCK ---
if ($success) {
    try {
        $conn->begin_transaction();

        // Get all "parent" leave types that should get a balance
        $ltRes = $conn->query(
            "SELECT LeaveTypeID, 
                    COALESCE(DefaultAllowanceDays, 0.000) AS DefaultAllowance, 
                    COALESCE(DefaultBalanceDays, 0.000) AS DefaultBalance 
             FROM LeaveType
             WHERE (DeductFromLeaveTypeID IS NULL OR DeductFromLeaveTypeID = 0)"
        );

        if ($ltRes) {
            $insLA = $conn->prepare("
                INSERT IGNORE INTO LeaveAvailability
                    (EmployeeID, LeaveTypeID, AllowanceAnnualDays, BalanceAccruedDays, LastAccrualDate, TakenDays, PlannedDays)
                VALUES (?, ?, ?, ?, ?, 0.000, 0.000)
            ");

            // Use the hire date (or today) as the "start clock" date/time.
            $lastAccrual = $dateHired ? $dateHired . ' 00:00:00' : date('Y-m-d H:i:s');

            while ($lt = $ltRes->fetch_assoc()) {
                $leaveTypeId = (int) $lt['LeaveTypeID'];
                
                // **THE FIX**
                // Use floatval() to preserve decimals like 1.25
                $allowDefault = (float) $lt['DefaultAllowance'];
                $balanceDefault = (float) $lt['DefaultBalance'];

                // **THE FIX**
                // Use 'd' (double) for the decimal values
                $insLA->bind_param('iidds', $newEmployeeId, $leaveTypeId, $allowDefault, $balanceDefault, $lastAccrual);
                $insLA->execute();
            }
            $insLA->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        // Log this error properly instead of just failing silently
        error_log('LeaveAvailability init failed for employee ' . $newEmployeeId . ': ' . $e->getMessage());
        // Send a more specific error
        echo json_encode(['success' => false, 'error' => 'Employee added, but failed to set up leave balances.']);
        $conn->close();
        exit();
    }

    // Success response
    echo json_encode(['success' => true, 'employeeId' => $newEmployeeId]);

} else {
    // Error response
    echo json_encode(['success' => false, 'error' => 'Error adding employee: ' . htmlspecialchars($errorMsg)]);
}
// --- END: CORRECTED LEAVE AVAILABILITY BLOCK ---

$conn->close();
?>