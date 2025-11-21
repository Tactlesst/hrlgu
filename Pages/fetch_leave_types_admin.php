<?php
include 'db_connect.php';

$leaveTypesAdmin = [];
$leaveTypes = []; // map id => row

$allowedUnitTypes = ['Leave Credit', 'Fixed Days'];
$allowedFrequencies = ['NA', 'PerYear', 'PerEvent'];

$res = $conn->query("SELECT LeaveTypeID, TypeName, UnitType, MaxDaysPerUsage, UsageFrequency, Description, PointCost, DeductFromLeaveTypeID, AllowDocuments, MaxDocuments FROM LeaveType ORDER BY LeaveTypeID DESC");

if ($res) {
    // --- FIRST LOOP: This part is correct ---
    // It reads all data into $leaveTypesAdmin and $leaveTypes
    while ($row = $res->fetch_assoc()) {
        // Normalize UnitType
        $unitRaw = trim((string) ($row['UnitType'] ?? ''));
        $matched = null;
        foreach ($allowedUnitTypes as $u) {
            if (strcasecmp($u, $unitRaw) === 0) {
                $matched = $u;
                break;
            }
        }
        if ($matched === null) {
            foreach ($allowedUnitTypes as $u) {
                if ($unitRaw !== '' && (stripos($u, $unitRaw) !== false || stripos($unitRaw, $u) !== false)) {
                    $matched = $u;
                    break;
                }
            }
        }
        if ($matched === null) $matched = 'Leave Credit';
        $row['UnitType'] = $matched;

        // Normalize UsageFrequency
        $freqRaw = trim((string) ($row['UsageFrequency'] ?? ''));
        $freqMatched = null;
        foreach ($allowedFrequencies as $f) {
            if (strcasecmp($f, $freqRaw) === 0) {
                $freqMatched = $f;
                break;
            }
        }
        if ($freqMatched === null) $freqMatched = 'NA';
        if ($row['UnitType'] === 'Leave Credit') $freqMatched = 'NA';
        $row['UsageFrequency'] = $freqMatched;

        $row['MaxDaysPerUsage'] = isset($row['MaxDaysPerUsage']) ? (int) $row['MaxDaysPerUsage'] : 0;
        $row['PointCost'] = isset($row['PointCost']) ? (float) $row['PointCost'] : 0.0;
        $row['AllowDocuments'] = isset($row['AllowDocuments']) ? (int) $row['AllowDocuments'] : 0;
        $row['MaxDocuments'] = isset($row['MaxDocuments']) ? (int) $row['MaxDocuments'] : 0;
        $row['DeductFromLeaveTypeID'] = isset($row['DeductFromLeaveTypeID']) && $row['DeductFromLeaveTypeID'] !== '' ? (int) $row['DeductFromLeaveTypeID'] : null;

        $leaveTypesAdmin[] = $row;
        $leaveTypes[(int) $row['LeaveTypeID']] = $row;
    }
    // --- End of first loop ---
}

//
// --- **THE FIX IS HERE** ---
//
// We removed the broken, empty second loop and replaced it
// with a "foreach" loop that uses the data from the first loop.
//
if (count($leaveTypesAdmin) > 0) {
    // Loop through the array we already built
    foreach ($leaveTypesAdmin as $type) {
        
        $id = (int) $type['LeaveTypeID'];
        $typeName = htmlspecialchars($type['TypeName'] ?? '', ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($type['Description'] ?? '', ENT_QUOTES, 'UTF-8');
        $unit = $type['UnitType']; // Already normalized
        $freq = $type['UsageFrequency']; // Already normalized

        // --- Create the "Rule" ---
        $rule = '—'; // Default for "Leave Credit"
        if ($unit === 'Fixed Days') {
            if ($freq === 'PerYear') $rule = 'Per Year';
            else if ($freq === 'PerEvent') $rule = 'Per Event';
            else $rule = 'Per Event'; // Default for fixed
        }

        $pointCost = number_format($type['PointCost'] ?? 0, 2);
        $maxDays = number_format($type['MaxDaysPerUsage'] ?? 0, 0);

        // --- Fix "Deducted From" logic ---
        // Use the $leaveTypes map to find the parent name
        $deductId = $type['DeductFromLeaveTypeID'];
        $deductName = '—';
        if ($deductId !== null && isset($leaveTypes[$deductId])) {
            $deductName = htmlspecialchars($leaveTypes[$deductId]['TypeName'], ENT_QUOTES, 'UTF-8');
        }

        // --- Print the 8-column HTML row ---
        echo "<tr>
                <td>{$typeName}</td>
                <td>{$desc}</td>
                <td>{$unit}</td>
                <td>{$rule}</td>
                <td style=\"text-align:right;\">{$pointCost}</td>
                <td style=\"text-align:right;\">{$maxDays}</td>
                <td>{$deductName}</td>
                <td style='display:flex;gap:8px;align-items:center;justify-content:center;'>
                    <button class=\"edit-btn\" data-id=\"{$id}\" title=\"Edit Leave Type\" style=\"background:none;border:none;padding:8px;cursor:pointer;color:#007bff;font-size:18px;transition:all 0.3s ease;\" onmouseover=\"this.style.color='#0056b3'; this.style.transform='scale(1.2)'\" onmouseout=\"this.style.color='#007bff'; this.style.transform='scale(1)'\">✏️</button>
                    <button class=\"delete-btn\" data-id=\"{$id}\" title=\"Delete Leave Type\" style=\"background:none;border:none;padding:8px;cursor:pointer;color:#dc3545;font-size:18px;transition:all 0.3s ease;\" onmouseover=\"this.style.color='#c82333'; this.style.transform='scale(1.2)'\" onmouseout=\"this.style.color='#dc3545'; this.style.transform='scale(1)'\">🗑️</button>
                </td>
              </tr>";
    }
} else {
    // No leave types found
    echo '<tr><td colspan="8" style="text-align:center;color:#666;">No leave types found</td></tr>';
}
?>