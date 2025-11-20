<?php
header('Content-Type: application/json');
include 'db_connect.php';

// --- HELPER FUNCTION: To handle individual file uploads ---
/**
 * Safely handles a file upload and returns the new file path.
 *
 * @param string $fileKey The name of the file input (e.g., 'resume').
 * @param int $employeeId The ID of the employee for naming.
 * @param string $uploadDir The directory to save the file.
 * @param object $conn The database connection (for error escaping).
 * @return string|null The new relative path (e.g., 'uploads/emp_123_resume.pdf') or null.
 * @throws Exception If the file move fails.
 */
function handleFileUpload($fileKey, $employeeId, $uploadDir, $conn)
{
    // Check if file exists, was uploaded, and has no error
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {

        $file = $_FILES[$fileKey];
        $fileName = basename($file['name']);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Create a safe, unique filename
        // e.g., emp_123_resume_1678886400.pdf
        $safeFileName = sprintf(
            "emp_%d_%s_%s.%s",
            $employeeId,
            $conn->real_escape_string($fileKey), // Use the key (e.g., 'resume') for clarity
            time(),
            $extension
        );

        $targetPath = $uploadDir . $safeFileName;

        // Move the file from temp location to your 'uploads' folder
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath; // Return the path to be saved in the DB
        } else {
            // Failed to move
            throw new Exception("Could not move uploaded file for: $fileKey");
        }
    }

    // No file was uploaded for this key, or it had an error (e.g., file too large)
    return null;
}
// --- END HELPER FUNCTION ---


// --- MAIN SCRIPT LOGIC ---
$response = [
    'success' => false,
    'error' => 'Invalid request method.'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['EmployeeID'])) {
        $response['error'] = 'Employee ID is missing.';
        echo json_encode($response);
        exit;
    }

    $employeeId = intval($_POST['EmployeeID']);
    $uploadDir = 'uploads/'; // Make sure this folder exists!

    // Arrays to build the dynamic SQL query
    $updateFields = []; // e.g., "FirstName = ?", "ResumePath = ?"
    $params = [];       // The values to bind
    $types = "";        // The type string, e.g., "ssi"

    try {
        // --- 1. Handle Non-File Fields ---
        
        // Debug: Log all POST data
        error_log('=== UPDATE EMPLOYEE DEBUG ===');
        error_log('POST Data: ' . json_encode($_POST));
        
        // Personal Information
        $firstName = $conn->real_escape_string($_POST['FirstName'] ?? '');
        $middleName = $conn->real_escape_string($_POST['MiddleName'] ?? '');
        $lastName = $conn->real_escape_string($_POST['LastName'] ?? '');
        $email = $conn->real_escape_string($_POST['Email'] ?? '');
        $phone = $conn->real_escape_string($_POST['Phone'] ?? '');
        $birthdate = $_POST['Birthdate'] ?? '';
        $dateHired = $_POST['DateHired'] ?? '';
        
        // Debug: Log extracted values
        error_log('Extracted values:');
        error_log('  Email: "' . $email . '" (isset: ' . (isset($_POST['Email']) ? 'yes' : 'no') . ')');
        error_log('  Birthdate: "' . $birthdate . '" (isset: ' . (isset($_POST['Birthdate']) ? 'yes' : 'no') . ')');
        error_log('  DateHired: "' . $dateHired . '" (isset: ' . (isset($_POST['DateHired']) ? 'yes' : 'no') . ')');
        error_log('  FirstName: "' . $firstName . '"');
        
        // Employment Information
        $status = $conn->real_escape_string($_POST['status'] ?? 'Full Time');
        $deptId = (empty($_POST['department']) || $_POST['department'] == '0') ? NULL : intval($_POST['department']);
        $posId = (empty($_POST['position']) || $_POST['position'] == '0') ? NULL : intval($_POST['position']);

        // Add personal information fields
        // Always update these fields, even if empty (allow clearing values)
        if (isset($_POST['FirstName'])) {
            $updateFields[] = "FirstName = ?";
            $params[] = $firstName;
            $types .= "s";
        }
        
        if (isset($_POST['MiddleName'])) {
            $updateFields[] = "MiddleName = ?";
            $params[] = $middleName;
            $types .= "s";
        }
        
        if (isset($_POST['LastName'])) {
            $updateFields[] = "LastName = ?";
            $params[] = $lastName;
            $types .= "s";
        }
        
        if (isset($_POST['Email'])) {
            $updateFields[] = "Email = ?";
            $params[] = $email;
            $types .= "s";
        }
        
        if (isset($_POST['Phone'])) {
            $updateFields[] = "ContactNo = ?";
            $params[] = $phone;
            $types .= "s";
        }
        
        if (isset($_POST['Birthdate'])) {
            $updateFields[] = "Birthdate = ?";
            $params[] = $birthdate;
            $types .= "s";
        }
        
        if (isset($_POST['DateHired'])) {
            $updateFields[] = "DateHired = ?";
            $params[] = $dateHired;
            $types .= "s";
        }

        // Add employment information fields
        $updateFields[] = "Status = ?";
        $params[] = $status;
        $types .= "s";

        $updateFields[] = "DepartmentID = ?";
        $params[] = $deptId;
        $types .= "i";

        $updateFields[] = "PositionID = ?";
        $params[] = $posId;
        $types .= "i";

        // --- 2. Handle ALL File Fields ---

        // This map connects your HTML form 'name' to the database 'ColumnName'
        $fileMap = [
            'resume' => 'ResumePath',
            'diploma' => 'DiplomaPath',
            'govtid' => 'GovtIDPath', // This was the name in your HTML
            'localcert' => 'LocalCertificatePath',
            'cse' => 'CivilServiceEligibility',
            'psa_birth' => 'PSABirthCertificate',
            'psa_marriage' => 'PSAMarriageCertificate',
            'nbi_clearance' => 'NBIClearance',
            'police_clearance' => 'PoliceClearance',
            'medical_cert' => 'MedicalCertificate',
            'pds' => 'PersonalDataSheet',
            'gov_id' => 'ValidGovID', // This was 'gov_id' in your HTML
            'prc_license' => 'PRCLicense',
            'tor' => 'TranscriptOfRecords',
            'cert_training' => 'CertificatesOfTraining',
            'service_record' => 'ServiceRecord',
            'performance_rating' => 'PerformanceRating',
            'honor_grad' => 'HonorGraduateEligibility',
            'tin' => 'TIN',
            'sss' => 'SSS',
            'pagibig' => 'PagIBIG',
            'philhealth' => 'PhilHealth'
            // 'other_docs' is tricky if it's 'multiple', skipping for this example
        ];

        // Loop over every file and process it
        foreach ($fileMap as $formName => $dbColumn) {
            $newPath = handleFileUpload($formName, $employeeId, $uploadDir, $conn);

            // If a new file was successfully uploaded, add it to the query
            if ($newPath !== null) {
                $updateFields[] = "$dbColumn = ?";
                $params[] = $newPath;
                $types .= "s";

                // Optional: You could also delete the old file from the server here
            }
        }

        // --- 3. Build and Execute the Final Query ---
        if (empty($updateFields)) {
            throw new Exception("No fields to update.");
        }

        $sql = "UPDATE Employee SET " . implode(", ", $updateFields) . " WHERE EmployeeID = ?";

        // Add the EmployeeID as the final param for the WHERE clause
        $params[] = $employeeId;
        $types .= "i";

        // Debug: Log the SQL and params before execution
        error_log('SQL Query: ' . $sql);
        error_log('Types: ' . $types);
        error_log('Params: ' . json_encode($params));
        error_log('Update fields count: ' . count($updateFields));

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }

        // Use the splat operator (...) to bind all params at once
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['error'] = '';
        } else {
            throw new Exception('Database execute error: ' . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        // Catch any errors (like failed file move)
        $response['error'] = $e->getMessage();
    }

    $conn->close();
}

// Send the single, final JSON response
echo json_encode($response);
?>