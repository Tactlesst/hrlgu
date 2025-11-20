<?php
// FILE: update_leave_credit_accrual.php
// A cron job script to add credits to "parent" leave types.

// --- CONFIGURATION ---
// This is the amount of leave credit to add per interval
$creditAmount = 1.25;

// This is the interval, in MINUTES, between each accrual.
// 5 = 5 minutes
// 1440 = 1 day (24 * 60)
// 43200 = 30 days (approx)
$minutesInterval = 1440;
// --- END CONFIGURATION ---

header('Content-Type: text/plain');
include __DIR__ . '/db_connect.php';

if (!isset($conn) || !$conn) {
    error_log('DB connect failed in update_leave_credit_accrual.php');
    exit("No DB connection.\n");
}

// Enable more robust error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn->begin_transaction();

    // Statement to ADD credits and update the date
    $updateStmt = $conn->prepare(
        "UPDATE LeaveAvailability 
         SET BalanceAccruedDays = BalanceAccruedDays + ?, LastAccrualDate = ? 
         WHERE AvailabilityID = ?"
    );

    // Statement to START THE CLOCK for new records
    $startClockStmt = $conn->prepare(
        "UPDATE LeaveAvailability 
         SET LastAccrualDate = ? 
         WHERE AvailabilityID = ?"
    );

    // This query correctly finds only "parent" leave balances
    // (the ones that actually accrue, not the ones that deduct)
    $q = "SELECT la.AvailabilityID, la.LastAccrualDate
          FROM LeaveAvailability la
          JOIN LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
          WHERE (lt.DeductFromLeaveTypeID IS NULL OR lt.DeductFromLeaveTypeID = 0)
          FOR UPDATE";

    $res = $conn->query($q);

    $now = new DateTimeImmutable('now');
    $totalCreditsAdded = 0;
    $employeesUpdated = 0;
    $clocksStarted = 0;

    while ($row = $res->fetch_assoc()) {
        $availId = (int) $row['AvailabilityID'];

        if ($row['LastAccrualDate'] === null) {
            // --- LOGIC 1: START THE CLOCK ---
            // This record is new. Don't give it any credits yet.
            // Just set its start time to NOW.
            $nowStr = $now->format('Y-m-d H:i:s');
            $startClockStmt->bind_param('si', $nowStr, $availId);
            $startClockStmt->execute();
            $clocksStarted++;

        } else {
            // --- LOGIC 2: ADD CREDITS (if time has passed) ---
            $start = new DateTimeImmutable($row['LastAccrualDate']);
            
            // Get total minutes passed since last accrual
            $totalMinutes = floor(($now->getTimestamp() - $start->getTimestamp()) / 60);

            if ($totalMinutes >= $minutesInterval) {
                // Yes, at least one full interval has passed.
                
                // Calculate how many intervals have passed (the "catch-up" logic)
                $intervals = floor($totalMinutes / $minutesInterval);
                $creditsToAdd = $intervals * $creditAmount;
                
                // Calculate the new "last run" time based on the *old* time
                // This prevents "time drift"
                $minutesToAdd = $intervals * $minutesInterval;
                $newLastAccrualDate = $start->add(new DateInterval("PT{$minutesToAdd}M"));

                $newDateStr = $newLastAccrualDate->format('Y-m-d H:i:s');
                $updateStmt->bind_param(
                    'dsi',
                    $creditsToAdd,
                    $newDateStr,
                    $availId
                );
                $updateStmt->execute();

                $totalCreditsAdded += $creditsToAdd;
                $employeesUpdated++;
            }
            // else: Not enough time passed, do nothing.
        }
    }

    $res->close();
    $updateStmt->close();
    $startClockStmt->close();
    $conn->commit();

    echo "Accrual simulation complete.\n";
    echo "Total employees updated: $employeesUpdated\n";
    echo "Total credits added: $totalCreditsAdded\n";
    echo "Total new employee clocks started: $clocksStarted\n";

} catch (Exception $e) {
    // Error handling
    error_log('update_leave_credit_accrual error: ' . $e->getMessage());
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>