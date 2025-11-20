<?php
header('Content-Type: application/json'); // Send back JSON
include 'db_connect.php';

// --- ASSUMPTIONS ---
// 1. You get the employee ID from a session or a form
$employee_id = 123; // <-- Example: Get this from $_POST['employee_id']

// 2. You MUST know the LeaveTypeIDs for VL and SL
$vacation_leave_id = 1; // <-- IMPORTANT: Change this to your ID for 'Vacation Leave'
$sick_leave_id = 2;     // <-- IMPORTANT: Change this to your ID for 'Sick Leave'

// 3. The Constant Factor
$CF = 0.0481927;

$response = [];

try {
    // --- Step 1: Get S (MonthlySalary) ---
    $stmt_s = $conn->prepare("SELECT MonthlySalary FROM Employee WHERE EmployeeID = ?");
    if (!$stmt_s) {
        throw new Exception("SQL error (Employee): " . $conn->error);
    }
    $stmt_s->bind_param("i", $employee_id);
    $stmt_s->execute();
    $result_s = $stmt_s->get_result();

    if ($result_s->num_rows === 0) {
        throw new Exception("Employee not found.");
    }

    $emp = $result_s->fetch_assoc();
    $S = (float) $emp['MonthlySalary'];
    $stmt_s->close();

    if ($S <= 0) {
        throw new Exception("Employee salary is not set or is zero.");
    }

    // --- Step 2: Get D (Total VL and SL Credits) ---
    // This query sums up the available days for ONLY VL and SL
    $stmt_d = $conn->prepare("
        SELECT SUM(AvailableToTakeDays) AS TotalCredits 
        FROM LeaveAvailability 
        WHERE EmployeeID = ? 
        AND LeaveTypeID IN (?, ?)
    ");
    if (!$stmt_d) {
        throw new Exception("SQL error (LeaveAvailability): " . $conn->error);
    }
    $stmt_d->bind_param("iii", $employee_id, $vacation_leave_id, $sick_leave_id);
    $stmt_d->execute();
    $result_d = $stmt_d->get_result();
    $credits = $result_d->fetch_assoc();
    $D = (float) ($credits['TotalCredits'] ?? 0);
    $stmt_d->close();

    // --- Step 3: Calculate the Value ---
    $total_monetization_value = $S * $D * $CF;

    // --- Step 4: Send a Successful Response ---
    $response = [
        'success' => true,
        'salary_S' => $S,
        'total_credits_D' => $D,
        'constant_factor_CF' => $CF,
        'monetization_value' => $total_monetization_value,
        'formatted_value' => '₱' . number_format($total_monetization_value, 2)
    ];

} catch (Exception $e) {
    // Send an error response
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

$conn->close();
echo json_encode($response);
?>