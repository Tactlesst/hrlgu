<?php
// filepath: d:\Xampp\htdocs\HR-Leave-Management-System\Pages\get_employee_stats.php
header('Content-Type: application/json');
include 'db_connect.php';

$employee_id = intval($_GET['id'] ?? 0);

if ($employee_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid Employee ID.']);
    exit;
}

// --- !! IMPORTANT: SET THESE TO MATCH YOUR DB !! ---
// You must set these IDs for the monetization calculation to work.
$VACATION_LEAVE_ID = 1; // Change to your LeaveTypeID for 'Vacation Leave'
$SICK_LEAVE_ID = 2;     // Change to your LeaveTypeID for 'Sick Leave'
$MONETIZATION_CF = 0.0481927; // The constant factor you provided
// ---

$response = [
    'success' => false,
    'monetization' => [
        'formattedValue' => 'N/A'
    ],
    'balances' => [],
    'chartData' => [
        'total' => 0,
        'approved' => 0,
        'rejected' => 0,
        'pending' => 0
    ]
];

try {

    // === 1. GET MONETIZATION VALUE (S * D * CF) ===

    // Get S (Salary)
    $stmt_s = $conn->prepare("SELECT MonthlySalary FROM Employee WHERE EmployeeID = ?");
    $stmt_s->bind_param("i", $employee_id);
    $stmt_s->execute();
    $result_s = $stmt_s->get_result();

    if ($result_s->num_rows > 0) {
        $emp = $result_s->fetch_assoc();
        $S = (float) $emp['MonthlySalary'];

        if ($S > 0) {
            // Get D (Total VL and SL Credits)
            $stmt_d = $conn->prepare("
                SELECT SUM(AvailableToTakeDays) AS TotalCredits 
                FROM LeaveAvailability 
                WHERE EmployeeID = ? 
                AND LeaveTypeID IN (?, ?)
            ");
            $stmt_d->bind_param("iii", $employee_id, $VACATION_LEAVE_ID, $SICK_LEAVE_ID);
            $stmt_d->execute();
            $result_d = $stmt_d->get_result();
            $credits = $result_d->fetch_assoc();
            $D = (float) ($credits['TotalCredits'] ?? 0);
            $stmt_d->close();

            // Calculate Value
            $total_monetization_value = $S * $D * $MONETIZATION_CF;

            $response['monetization'] = [
                'value' => $total_monetization_value,
                'formattedValue' => '₱' . number_format($total_monetization_value, 2),
                'debug_S' => $S,
                'debug_D' => $D,
                'debug_CF' => $MONETIZATION_CF
            ];
        } else {
            $response['monetization']['formattedValue'] = 'Salary not set';
        }
    } else {
        $response['monetization']['formattedValue'] = 'Employee not found';
    }
    $stmt_s->close();


    // === 2. GET LEAVE BALANCES TABLE ===
    // This query correctly gets only "parent" leave types that are credit-based
    $sql_bal = "
        SELECT 
            lt.TypeName, 
            la.AvailableToTakeDays, 
            la.BalanceAccruedDays,
            la.TakenDays, 
            la.PlannedDays
        FROM 
            LeaveAvailability la
        JOIN 
            LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
        WHERE 
            la.EmployeeID = ?
            AND lt.UnitType = 'Leave Credit'
            AND (lt.DeductFromLeaveTypeID IS NULL OR lt.DeductFromLeaveTypeID = 0) 
        ORDER BY 
            lt.TypeName
    ";

    $stmt_bal = $conn->prepare($sql_bal);
    $stmt_bal->bind_param("i", $employee_id);
    $stmt_bal->execute();
    $res_bal = $stmt_bal->get_result();

    $balances = [];
    while ($row = $res_bal->fetch_assoc()) {
        $balances[] = [
            'typeName' => $row['TypeName'],
            // Your DB uses INT, so we cast to int
            'available' => (int) $row['AvailableToTakeDays'],
            'earned' => (int) $row['BalanceAccruedDays'],
            'taken' => (int) $row['TakenDays'],
            'planned' => (int) $row['PlannedDays']
        ];
    }
    $response['balances'] = $balances;
    $stmt_bal->close();


    // === 3. GET CHART DATA (Leave Applications) ===
    $sql_chart = "SELECT Status, COUNT(*) as cnt FROM LeaveApplication WHERE EmployeeID = ? GROUP BY Status";
    $stmt_chart = $conn->prepare($sql_chart);
    $stmt_chart->bind_param("i", $employee_id);
    $stmt_chart->execute();
    $res_chart = $stmt_chart->get_result();

    while ($row = $res_chart->fetch_assoc()) {
        $status = strtolower($row['Status']); // 'Approved' -> 'approved'
        if (array_key_exists($status, $response['chartData'])) {
            $response['chartData'][$status] = (int) $row['cnt'];
        }
        $response['chartData']['total'] += (int) $row['cnt'];
    }
    $stmt_chart->close();

    // All steps successful
    $response['success'] = true;

} catch (Exception $e) {
    $response['error'] = 'A server error occurred: ' . $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>