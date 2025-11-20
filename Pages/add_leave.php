<?php
header('Content-Type: application/json');
session_start();
include 'db_connect.php';

$employeeId = $_SESSION['user_id'] ?? 0;
$leaveTypeId = intval($_POST['leave_type_id'] ?? 0);
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');
$maxDocuments = intval($_POST['max_documents'] ?? 0);

$stmt = $conn->prepare("INSERT INTO LeaveApplication (EmployeeID, LeaveTypeID, StartDate, EndDate, Reason) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $employeeId, $leaveTypeId, $startDate, $endDate, $reason);
$success = $stmt->execute();
$applicationId = $stmt->insert_id;
$stmt->close();

if (!$success) {
    echo json_encode(['success' => false, 'error' => 'Error creating leave application.']);
    exit;
}

$allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
$maxSize = 2 * 1024 * 1024; // 2MB

for ($i = 1; $i <= $maxDocuments; $i++) {
    $inputName = "leave_document_$i";
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes))
            continue;
        if ($_FILES[$inputName]['size'] > $maxSize)
            continue;
        $newName = uniqid() . '_' . basename($_FILES[$inputName]['name']);
        $targetPath = "uploads/" . $newName;
        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath)) {
            $docType = $_POST["doc_type_$i"] ?? null;
            $stmt = $conn->prepare("INSERT INTO EmployeeDocument (EmployeeID, ApplicationID, FilePath, DocType) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $employeeId, $applicationId, $targetPath, $docType);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$conn->close();
echo json_encode(['success' => true]);
?>