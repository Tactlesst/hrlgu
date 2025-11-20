?php
// Credits 1.25 days per month since last accrual (or DateHired) to BalanceAccruedDays
include __DIR__ . '/../Pages/db_connect.php';
if (!isset($conn) || !$conn)
exit("No DB\n");

$creditPerMonth = 1.25;
// Optionally restrict to certain leave types (Vacation/Sick)
$creditTypeNames = ['Vacation', 'Vacation Leave', 'Sick', 'Sick Leave'];

$in = implode(',', array_fill(0, count($creditTypeNames), '?'));
$stmt = $conn->prepare("SELECT LeaveTypeID FROM LeaveType WHERE TypeName IN ($in)");
$typesSig = str_repeat('s', count($creditTypeNames));
$stmt->bind_param($typesSig, ...$creditTypeNames);
$stmt->execute();
$res = $stmt->get_result();
$creditTypeIds = [];
while ($r = $res->fetch_assoc())
$creditTypeIds[] = (int) $r['LeaveTypeID'];
$stmt->close();

if (empty($creditTypeIds)) {
echo "No matching leave types for accrual.\n";
exit;
}

$ph = implode(',', array_fill(0, count($creditTypeIds), '?'));
$q = "SELECT la.AvailabilityID, la.EmployeeID, la.LeaveTypeID, la.BalanceAccruedDays, la.LastAccrualDate, e.DateHired
FROM LeaveAvailability la
JOIN Employee e ON la.EmployeeID = e.EmployeeID
WHERE la.LeaveTypeID IN ($ph) FOR UPDATE";

$conn->begin_transaction();
$stmt = $conn->prepare($q);
$typesSig = str_repeat('i', count($creditTypeIds));
$stmt->bind_param($typesSig, ...$creditTypeIds);
$stmt->execute();
$res = $stmt->get_result();

$updateStmt = $conn->prepare("UPDATE LeaveAvailability SET BalanceAccruedDays = BalanceAccruedDays + ?, LastAccrualDate
= ? WHERE AvailabilityID = ?");

$today = new DateTimeImmutable('today');
while ($row = $res->fetch_assoc()) {
$availId = (int) $row['AvailabilityID'];
$last = $row['LastAccrualDate'] ?: $row['DateHired'] ?: null;
if (!$last)
continue;
$start = new DateTimeImmutable($last);
// compute whole months difference
$diff = $start->diff($today);
$months = $diff->y * 12 + $diff->m;
if ($months <= 0) continue; $credits=round($months * $creditPerMonth, 2); if ($credits <=0) continue; $newLast=$start->
    add(new DateInterval("P{$months}M"))->format('Y-m-d');
    $updateStmt->bind_param('dsi', $credits, $newLast, $availId);
    $updateStmt->execute();
    // optional: insert log table entry
    }

    $updateStmt->close();
    $stmt->close();
    $conn->commit();
    echo "Accruals applied.\n";
    ?>