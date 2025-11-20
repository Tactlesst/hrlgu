<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: /hrlgu/Pages/Login.php");
    exit();
}
include 'db_connect.php';

// Fetch employee info
include 'fetch_employee_info.php';

// Fetch leave types and balances
include 'fetch_leave_balances.php';

include 'fetch_employee_leave_data.php';

if (!isset($conn)) { include 'db_connect.php'; }
$employeeId = $_SESSION['user_id'];
$currentYear = date('Y');
$fixedLeaveUsage = []; // New array to hold usage data

// This query fetches the total days used for "PerYear" leaves
$usageSql = "SELECT LeaveTypeID, SUM(DaysUsed) as TotalDaysUsed
             FROM LeaveUsageLog
             WHERE EmployeeID = ? AND YEAR(UsageDate) = ?
             GROUP BY LeaveTypeID";
$usageStmt = $conn->prepare($usageSql);
$usageStmt->bind_param("is", $employeeId, $currentYear);
$usageStmt->execute();
$usageRes = $usageStmt->get_result();

while ($row = $usageRes->fetch_assoc()) {
    // Store the usage, e.g., $fixedLeaveUsage[5] = 2 (Used 2 days of SPL)
    $fixedLeaveUsage[$row['LeaveTypeID']] = $row['TotalDaysUsed'];
}
$usageStmt->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
        <link rel="icon" type="image/x-icon" href="../Pictures/logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/Admin-Dashboard.css">
    <link rel="stylesheet" href="../CSS/Sidebar.css">
    <link rel="stylesheet" href="../CSS/Employee-Dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-section">
                    <img src="/hrlgu/Pictures/logo.png" alt="Logo" class="sidebar-logo">
                    <h2 class="sidebar-title">Admin Menu</h2>
                </div>
            </div>

            <button class="menu-btn" data-title="My Profile" data-target="profile-section">
                <i class="fas fa-user"></i>
                <span class="btn-text">My Profile</span>
            </button>

            <button class="menu-btn" data-title="My Documents" data-target="documents-section">
                <i class="fas fa-file-alt"></i>
                <span class="btn-text">My Documents</span>
            </button>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <i class="fas fa-calendar-check"></i>
                    <span class="btn-text">Manage Leave ▸</span>
                </button>
                <div class="submenu">
                    <button class="submenu-btn" data-title="Manage Your Leave" data-target="apply-leave-section">
                        <i class="fas fa-plus-circle"></i>
                        <span class="btn-text">Apply Leave</span>
                    </button>
                    <button class="submenu-btn" data-title="Leave History" data-target="leave-history-section">
                        <i class="fas fa-history"></i>
                        <span class="btn-text">Leave History</span>
                    </button>
                </div>
            </div>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <i class="fas fa-cog"></i>
                    <span class="btn-text">Settings ▸</span>
                </button>
                <div class="submenu">
                    <button class="submenu-btn" data-title="Logout" data-target="n/a">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="btn-text">Logout</span>
                    </button>
                </div>
            </div>
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1 id="page-title">Leave Management System</h1>
                <div class="top-bar-icons">
                    <i class="fas fa-bell" style="font-size: 20px; color: #0052CC; cursor: pointer;"></i>
                    <i class="fas fa-user-circle" style="font-size: 24px; color: #0052CC; cursor: pointer;"></i>
                </div>
            </div>

            <!-- Content Sections -->
            <div class="content-area">
                <!-- Profile -->
                <div id="profile-section" class="section"
                    style="margin-top:20px; padding:15px; border:1px solid #ddd; border-radius:10px; background:#fff;">
                    <div class="profile-info">
    <div class="photo-wrapper">
        <img src="/<?php echo $photoPath; ?>" alt="Profile Photo" 
             class="profile-photo" id="profilePhotoImg">
        
        <button id="changeProfilePhotoBtn" class="change-photo-btn">Change</button>
    </div>

    <div class="profile-details">
        <p><strong>Name:</strong> <?php echo $fullName; ?></p>
        <p><strong>Age:</strong> <?php echo $age; ?></p>
        <p><strong>Birthdate:</strong> <?php echo $birthdate; ?></p>
        <p><strong>Department:</strong> <?php echo $department; ?></p>
        <p><strong>Position:</strong> <?php echo $position; ?></p>
    </div>
</div>

<div id="photoModal" class="modal">
    <div class="modal-content">
        <span id="closePhotoModal" class="close-modal">&times;</span>
        <h2>Update Profile Photo</h2>
        <p>Select a new photo, then drag to crop.</p>
        
        <input type="file" id="photoUploadInput" accept="image/png, image/jpeg, image/gif">
        
        <div id="cropperContainer">
            <img id="imageToCrop" src="">
        </div>
        
        <button id="saveCropBtn" style="display:none; margin-top: 15px; padding: 10px 15px; cursor:pointer;">
            Save Photo
        </button>
    </div>
</div>

                    <!-- Analytics Section -->
                    <div class="profile-analytics"
                        style="margin-top:20px; padding:15px; border:1px solid #ddd; border-radius:10px; background:#fff;">
                        <h3>Your Leave Analytics</h3>
                        <canvas id="leaveAnalyticsChart" height="120"></canvas>

                        <!-- Simplified Totals -->
                        <div style="margin-top:20px; display:grid; grid-template-columns: repeat(2, 1fr); gap:15px;">
                            <div style="padding:10px; background:#f8f9fa; border-radius:8px; text-align:center;">
                                <strong>3</strong><br>Requests This Month
                            </div>
                            <div style="padding:10px; background:#f8f9fa; border-radius:8px; text-align:center;">
                                <strong>12</strong><br>Requests This Year
                            </div>
                            <div style="padding:10px; background:#f8f9fa; border-radius:8px; text-align:center;">
                                <strong>15</strong><br>Total Balance (Days)
                            </div>
                            <div style="padding:10px; background:#f8f9fa; border-radius:8px; text-align:center;">
                                <strong>45</strong><br>Points Spent
                            </div>
                        </div>
                    </div>


                    <!-- Chart.js -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        const ctx = document.getElementById('leaveAnalyticsChart').getContext('2d');
                        const leaveAnalyticsChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: [
                                    "This Month's Requests",
                                    "This Year's Requests",
                                    "Total Balance (Days)",
                                    "Points Spent"
                                ],
                                datasets: [{
                                    label: 'Leave Statistics',
                                    data: [
                                        3,   // This Month's Requests
                                        12,  // This Year's Requests
                                        15,  // Total Balance (Days)
                                        45   // Points Spent
                                        //?php echo $thisMonthRequests; ?>,
                                        //?php echo $thisYearRequests; ?>,
                                        //?php echo $totalBalanceDays; ?>,
                                        //?php echo $totalPointsSpent; ?>
                                    ],
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.6)',
                                        'rgba(255, 206, 86, 0.6)',
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(255, 99, 132, 0.6)'
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 99, 132, 1)'
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Employee Leave Overview',
                                        font: { size: 18 }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1 }
                                    }
                                }
                            }
                        });
                    </script>
                </div>
                <!-- Documents -->
                <div id="documents-section" class="section" style="display:none;">
                    <h2>My Documents</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Current File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="myDocumentsTableBody">
                            <!-- populated by JS (uses get_employee.php) -->
                        </tbody>
                    </table>
                </div>

                <!-- Apply Leave -->
                <div id="apply-leave-section" class="section" style="display:none;">
    <h2>Apply Leave</h2>

    <button class="open-apply-modal open-modal-btn"
        style="background:#007bff;color:#fff;border:none;padding:15px 12px;border-radius:6px;cursor:pointer;">Apply
        for Leave</button>

    <h2 style="margin-top: 20px;">My Leave Balances</h2>
    <div class="leave-balance-table">
        <table>
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Available</th>
                    <th>Earned/Credited</th>
                    <th>Taken</th>
                    <th>Planned</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through all leave types
                foreach ($leaveTypes as $typeID => $type):
                    // Only show 'Leave Credit' types that are "parents"
                    if ($type['UnitType'] == 'Leave Credit' && (empty($type['DeductFromLeaveTypeID']) || $type['DeductFromLeaveTypeID'] == 0)):
                        
                        $bal = $leaveBalances[$typeID] ?? [];
                        $balance = (int) ($bal['BalanceAccruedDays'] ?? 0);
                        $taken = (int) ($bal['TakenDays'] ?? 0);
                        $planned = (int) ($bal['PlannedDays'] ?? 0);
                        $available = $bal['AvailableToTakeDays'] ?? ($balance - $taken - $planned);
                        $availColor = $available > 0 ? 'blue' : 'red';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($type['TypeName']) ?></td>
                            <td style="font-weight:bold;color:<?= $availColor ?>;">
                                <?= number_format($available, 0) ?>
                            </td>
                            <td><?= number_format($balance, 0) ?></td>
                            <td><?= number_format($taken, 0) ?></td>
                            <td><?= number_format($planned, 0) ?></td>
                        </tr>
                    <?php
                    endif; 
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>

    <div class="fixed-leave-types-table" style="margin-top:20px;">
        <h2>Fixed Entitlement Leaves</h2>
        <table>
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Description</th>
                    <th>Entitlement</th>
                    <th>Usage This Year</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($leaveTypes as $typeID => $type):

                    if ($type['UnitType'] == 'Fixed Days'):
                        
                        // Get the rules from the LeaveType table
                        $maxDays = (int) ($type['MaxDaysPerUsage'] ?? 0);
                        $frequency = $type['UsageFrequency'] ?? 'PerEvent';
                        
                        $entitlement = "{$maxDays} days";
                        if ($frequency == 'PerYear') {
                            $entitlement .= " / Year";
                        } else {
                            $entitlement .= " / Event";
                        }

                        // --- NEW: Check Usage ---
                        $daysUsed = 0;
                        if ($frequency == 'PerYear') {
                            // Get usage from the array we loaded at the top
                            $daysUsed = (int) ($fixedLeaveUsage[$typeID] ?? 0);
                        }
                        
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($type['TypeName']) ?></td>
                            <td><?= htmlspecialchars($type['Description'] ?? '') ?></td>
                            <td><?= $entitlement ?></td>
                            
                            <td style="font-weight:bold;">
                                <?php
                                if ($frequency == 'PerYear') {
                                    echo "{$daysUsed} of {$maxDays} days used";
                                } else {
                                    echo "N/A (Per Event)";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    endif; // End 'Fixed Days' if
                endforeach; // End loop
                ?>
            </tbody>
        </table>
    </div>

    <div class="linked-leave-types-table" style="margin-top:20px;">
        <h2>Other Applyable Leaves</h2>
        <table>
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Description</th>
                    <th>Cost (Credits)</th>
                    <th>Deducts From</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($leaveTypes as $typeID => $type):
                    // Only show 'Leave Credit' types that are "children"
                    if ($type['UnitType'] == 'Leave Credit' && (!empty($type['DeductFromLeaveTypeID']) && $type['DeductFromLeaveTypeID'] != 0)):
                        
                        $parentID = $type['DeductFromLeaveTypeID'];
                        $parentName = $leaveTypes[$parentID]['TypeName'] ?? 'N/A';
                        $pointCost = (int) ($type['PointCost'] ?? 0);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($type['TypeName']) ?></td>
                            <td><?= htmlspecialchars($type['Description'] ?? '') ?></td>
                            <td><?= $pointCost ?></td>
                            <td><?= htmlspecialchars($parentName) ?></td>
                        </tr>
                    <?php
                    endif; 
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>
</div>

                <!-- Leave History -->
                <!-- 
========================================
NEW LEAVE HISTORY SECTION
========================================
-->
<div id="leave-history-section" class="section" style="display:none;">
    <h2>Leave History</h2>
    
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f4f4f4;">
                <th style="padding: 10px; border: 1px solid #ddd;">Control No.</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Leave Type</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Start Date</th>
                <th style="padding: 10px; border: 1px solid #ddd;">End Date</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody id="leaveHistoryTableBody">
            <?php
            // We must use the logged-in user's ID
            $history_employee_id = (int) $_SESSION['user_id'];
            
            $sql = "SELECT 
                        la.ApplicationID, 
                        la.ControlNumber, 
                        la.StartDate, 
                        la.EndDate, 
                        la.Status,
                        lt.TypeName
                    FROM 
                        LeaveApplication la
                    JOIN 
                        LeaveType lt ON la.LeaveTypeID = lt.LeaveTypeID
                    WHERE 
                        la.EmployeeID = ?
                    ORDER BY 
                        la.DateRequested DESC";
                        
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $history_employee_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    // Determine status color
                    $statusColor = 'blue'; // Pending
                    if ($row['Status'] == 'Approved') $statusColor = 'green';
                    if ($row['Status'] == 'Rejected' || $row['Status'] == 'Cancelled') $statusColor = 'red';

                    echo '<tr>';
                    echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['ControlNumber'] ?? 'N/A') . '</td>';
                    echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['TypeName']) . '</td>';
                    echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['StartDate']) . '</td>';
                    echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['EndDate']) . '</td>';
                    echo '<td style="padding: 10px; border: 1px solid #ddd; color: ' . $statusColor . ';"><strong>' . htmlspecialchars($row['Status']) . '</strong></td>';
                    echo '<td style="padding: 10px; border: 1px solid #ddd;">
                            <button class="view-application-btn" data-id="' . $row['ApplicationID'] . '" style="padding: 5px 10px; cursor: pointer;">View</button>
                          </td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center; padding: 20px;">You have not applied for any leave.</td></tr>';
            }
            $stmt->close();
            ?>
        </tbody>
    </table>
</div>

            </div>
        </div>

        <!-- Apply Leave Modal -->

        <div id="applyLeaveModal" class="modal">
            <div class="modal-content">
                <span class="close-modal" id="closeApplyLeaveModal">&times;</span>
                <h2>Apply Leave</h2>
                <form action="apply_leave.php" method="POST" class="apply-leave-form" enctype="multipart/form-data">

                    <label for="leave_category">1. Select Leave Category:</label>
                    <select name="leave_category" id="leave_category"
                        style="margin-bottom: 15px; width: 100%; padding: 5px;">
                        <option value="">--- Select Category ---</option>
                        <option value="credit">Credit Leave (VL, SL, etc.)</option>
                        <option value="fixed">Fixed Leave (Paternity, etc.)</option>
                        <option value="grant">Special Grants (Solo Parent, SLW, etc.)</option>
                    </select>

                    <div id="leave_form_details" style="display:none;">

                        <label for="leave_type">2. Select Leave Type:</label>
                        <select name="leave_type" id="leave_type" required>
                            <option value="" id="leave_type_placeholder">--- Select ---</option>

                            <?php
                            // This loop prints ALL leave types, but they will be hidden/shown by JavaScript
                            foreach ($leaveTypes as $typeID => $type):

                                $unitType = htmlspecialchars($type['UnitType'] ?? 'Leave Credit');
                                $dataUnitType = ($unitType == 'Fixed Days') ? 'fixed' : 'credit';

                                $allowDocs = (int) ($type['AllowDocuments'] ?? 0);
                                $maxDocs = (int) ($type['MaxDocuments'] ?? 0);
                                $fixedDays = (int) ($type['MaxDaysPerUsage'] ?? 0);
                                ?>

                                <option value="<?= $typeID ?>" data-cost="<?= htmlspecialchars($type['PointCost'] ?? 0) ?>"
                                    data-allow-docs="<?= $allowDocs ?>" data-max-docs="<?= $maxDocs ?>"
                                    data-unit-type="<?= $dataUnitType ?>" data-fixed-days="<?= $fixedDays ?>"
                                    style="display:none;">

                                    <?= htmlspecialchars($type['TypeName']) ?>
                                </option>

                            <?php endforeach; ?>
                        </select>

                        <p id="leaveCostDisplay" style="margin:5px 0; font-weight:bold; color:blue; display:none;">
                            Cost: <span id="leaveCostValue"></span> points
                        </p>

                        <label for="startDateInput">Start Date:</label>
                        <input type="date" name="start_date" id="startDateInput" required>

                        <label for="end_date">End Date:</label>
                        <input type="date" name="end_date" id="end_date">

                        <p id="weekendNote" style="color:#666;font-size:0.9em;display:none;">Note: weekends are
                            excluded; duration counts workdays only.</p>

                        <label for="reason">Reason:</label>
                        <textarea name="reason" id="reason" rows="2" required></textarea>

                        <div id="documentRequirement" style="margin-top:10px; display:none;">
                            <p style="margin:0 0 6px 0; font-weight:bold; color:#333;">Documents required for this leave
                                type:</p>
                            <input type="file" name="documents[]" id="documentsInput" multiple style="display:block;">
                            <small id="docHelp" style="color:#666;"></small>
                        </div>

                        <div style="margin-top:12px;">
                            <button type="submit"
                                style="background:#007bff;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;">Apply</button>
                            <button type="button" id="cancelApplyBtn"
                                style="margin-left:8px;padding:8px 12px;border-radius:6px;">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
<!-- 
========================================
NEW "VIEW APPLICATION DETAILS" MODAL
========================================
-->
<div id="viewLeaveDetailsModal" class="modal view-leave-modal" style="display:none;">
    <div class="modal-content" style="max-width: 800px;">
        <span class="close-modal" id="closeViewDetailsModal">&times;</span>

        <div id="printableLeaveArea">
            
            <div id="leaveDetailsContent">

    <style>
        /* Scoped styles for the CS Form printout */
        .cs-form {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 10px; /* Padding for the PDF */
        }

        /* --- THIS BLOCK IS UPDATED --- */
        .cs-form-header {
            display: flex; /* Use flexbox */
            align-items: center; /* Vertically center logo and text */
            justify-content: space-between; /* Puts logo left, text center, spacer right */
            padding-bottom: 10px;
            border-bottom: 2px solid #000; /* Added a separator line */
            margin-bottom: 5px; /* Added space below the header */
        }
        /* --- END OF UPDATE --- */

        .cs-form-header h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .cs-form-header p {
            font-size: 12px;
            margin: 2px 0;
        }
        
        /* --- NEW STYLE FOR THE LOGO --- */
        .cs-form-logo {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }
        
        /* --- NEW STYLE FOR THE HEADER TEXT --- */
        .cs-header-text {
            text-align: center;
        }

        /* --- NEW STYLE FOR THE SPACER (to keep text centered) --- */
        .cs-logo-spacer {
            width: 200px; /* Must match the logo width */
            height: 70px;
        }
        
        .cs-box {
            border: 1px solid #000;
            margin-bottom: 2px;
            padding: 4px;
        }
        .cs-flex-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 2px;
        }
        .cs-flex-col {
            display: flex;
            flex-direction: column;
        }
        .cs-flex-grow {
            flex-grow: 1;
        }
        .cs-w-50 {
            width: 50%;
        }
        .cs-w-33 {
            width: 33.33%;
        }
        .cs-box-label {
            font-size: 9px;
            color: #333;
        }
        .cs-box-value {
            font-weight: bold;
            min-height: 14px;
            display: block;
        }
        .cs-box-title {
            background-color: #eee;
            font-weight: bold;
            padding: 3px;
            margin: -4px -4px 4px -4px;
            font-size: 11px;
        }
        .cs-checkbox {
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            margin-right: 5px;
        }
        .cs-underline {
            border-bottom: 1px solid #000;
            padding: 0 5px;
        }
        .cs-credits-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 8px 0;
            font-size: 10px;
            font-family: Arial, sans-serif;
        }
        .cs-credits-table th,
        .cs-credits-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            height: 18px;
            font-weight: normal;
            color: #000;
        }
        .cs-credits-table thead th {
            font-weight: bold;
            background-color: #f4f4f4;
            font-size: 9px;
            color: #000;
        }
        .cs-credits-table tbody th {
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            background-color: #f4f4f4;
            padding-left: 5px;
            color: #000;
        }
        .cs-credits-table tbody td {
            font-weight: bold;
            text-align: center;
        }
    </style>

    <div class="cs-form">
        
        <!-- === THIS IS THE UPDATED HEADER === -->
        <div class="cs-form-header">
            <!-- 1. The Logo -->
            
            <img src="/Pictures/logo.png" 
                 alt="Balingasag Seal" 
                 class="cs-form-logo">
            
            <!-- 2. The Text -->
            <div class="cs-header-text">
                <p>Civil Service Form No. 6</p>
                <p>Revised 2020</p>
                <h2 id="form-agency-name">Municipal Goverment of Balingasag</h2>
                <p id="form-agency-address">Balingasag, Misamis Oriental</p>
                <h2>APPLICATION FOR LEAVE</h2>
            </div>
            
            <!-- 3. A Spacer (to balance the logo) -->
            <div class="cs-logo-spacer"></div>
        </div>
        <!-- === END OF UPDATED HEADER === -->


        <div class="cs-flex-row">
            <div class="cs-box cs-w-50">
                <span class="cs-box-label">1. OFFICE/DEPARTMENT</span>
                <span class="cs-box-value" id="form-department">Loading...</span>
            </div>
            <div class="cs-box cs-w-50">
                <span class="cs-box-label">2. NAME (Last, First, Middle)</span>
                <span class="cs-box-value" id="form-name">Loading...</span>
            </div>
        </div>

        <div class="cs-flex-row">
            <div class="cs-box cs-w-33">
                <span class="cs-box-label">3. DATE OF FILING</span>
                <span class="cs-box-value" id="form-date-filing">Loading...</span>
            </div>
            <div class="cs-box cs-w-33">
                <span class="cs-box-label">4. POSITION</span>
                <span class="cs-box-value" id="form-position">Loading...</span>
            </div>
            <div class="cs-box cs-w-33">
                <span class="cs-box-label">5. SALARY</span>
                <span class="cs-box-value" id="form-salary">Loading...</span>
            </div>
        </div>

        <div class="cs-box">
            <div class="cs-box-title">6. DETAILS OF APPLICATION</div>
            <div class="cs-flex-row">
                <div class="cs-w-50" style="border-right: 1px solid #000; padding-right: 4px;">
                    <span class="cs-box-label">6.A TYPE OF LEAVE TO BE AVAILED OF</span>
                    <span class="cs-box-value" id="form-leave-type"></span>
                </div>
                <div class="cs-w-50" style="padding-left: 4px;">
                    <span class="cs-box-label">6.B DETAILS OF LEAVE</span>
                    <span class="cs-box-value" id="form-leave-details"></span>
                </div>
            </div>
            <hr style="border:0; border-top:1px solid #000; margin: 2px -4px;">
            <div class="cs-flex-row">
                <div class="cs-w-50" style="border-right: 1px solid #000; padding-right: 4px;">
                    <span class="cs-box-label">6.C NUMBER OF WORKING DAYS APPLIED FOR</span>
                    <span class="cs-box-value" id="form-duration"></span>
                    <span class="cs-box-label">INCLUSIVE DATES</span>
                    <span class="cs-box-value" id="form-dates"></span>
                </div>
                <div class="cs-w-50" style="padding-left: 4px;">
                    <span class="cs-box-label">6.D COMMUTATION</span>
                    <span class="cs-box-value" id="form-commutation">
                        <span class="cs-checkbox">[ ]</span> Not Requested
                        <span class="cs-checkbox" style="margin-left: 10px;">[ ]</span> Requested
                    </span>
                </div>
            </div>
        </div>
        
        <div class="cs-box">
            <span class="cs-box-label">REASON FOR LEAVE</span>
            <span class="cs-box-value" id="form-reason" style="min-height: 40px;"></span>
        </div>

        <div class="cs-box">
             <div class="cs-box-title">7. DETAILS OF ACTION ON APPLICATION</div>
            <div class="cs-flex-row">
                <div class="cs-w-50" style="border-right: 1px solid #000; padding-right: 4px;">
                    <span class="cs-box-label">7.A CERTIFICATION OF LEAVE CREDITS</span>
                    <table class="cs-credits-table">
                        <thead>
                            <tr>
                                <th></th> <th>Vacation Leave</th>
                                <th>Sick Leave</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Total Earned</th>
                                <td id="form-vl-earned"></td>
                                <td id="form-sl-earned"></td>
                            </tr>
                            <tr>
                                <th>Less this Application</th>
                                <td id="form-vl-less"></td>
                                <td id="form-sl-less"></td>
                            </tr>
                            <tr>
                                <th>Balance</th>
                                <td id="form-vl-balance"></td>
                                <td id="form-sl-balance"></td>
                            </tr>
                        </tbody>
                    </table>
                    <span class="cs-box-value" style="text-align:center; padding-top: 10px;">(Signature)</span>
                    <span class="cs-box-label" style="text-align:center;">Authorized Officer</span>
                </div>
                <div class="cs-w-50" style="padding-left: 4px;">
                    <span class="cs-box-label">7.B RECOMMENDATION</span>
                    <span class="cs-box-value" id="form-recommendation">
                        <span class="cs-checkbox">[ ]</span> For approval
                        <br>
                        <span class="cs-checkbox">[ ]</span> For disapproval due to...
                    </span>
                </div>
            </div>
             <hr style="border:0; border-top:1px solid #000; margin: 2px -4px;">
             <div class="cs-flex-row">
                <div class="cs-w-50" style="border-right: 1px solid #000; padding-right: 4px;">
                    <span class="cs-box-label">7.C APPROVED FOR:</span>
                    <span class="cs-box-value" id="form-approval-days"></span>
                    <span class="cs-checkbox">[ ]</span> Approved
                    <span class="cs-checkbox">[ ]</span> Rejected
                </div>
                <div class="cs-w-50" style="padding-left: 4px;">
                    <span class="cs-box-label">7.D DISAPPROVED DUE TO:</span>
                    <span class="cs-box-value" id="form-disapproval-reason"></span>
                    <br>
                    <span class="cs-box-value" style="text-align:center; padding-top: 10px;">(Signature)</span>
                    <span class="cs-box-label" style="text-align:center;">Authorized Official</span>
                </div>
            </div>
        </div>
            </div> <button type="button" id="downloadPdfBtn">Download PDF</button>
        <button type="button" id="cancelLeaveDetailsBtn">Close</button>
    </div>
</div>
</body>

<script>
    // --- NEW HELPER FUNCTION ---
    /**
     * Calculates the end date of a leave period, skipping weekends.
     */
    function addWorkdays(startDateStr, durationDays) {
        if (!startDateStr || isNaN(durationDays) || durationDays <= 0) {
            return startDateStr; // Return start date if invalid
        }

        let currentDate = new Date(startDateStr + 'T00:00:00'); // Use local time
        let days = parseInt(durationDays, 10);
        let workdaysCounted = 0;

        while (currentDate.getDay() === 0 || currentDate.getDay() === 6) {
            currentDate.setDate(currentDate.getDate() + 1);
        }
        workdaysCounted = 1;

        while (workdaysCounted < days) {
            currentDate.setDate(currentDate.getDate() + 1);
            let dayOfWeek = currentDate.getDay();

            if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Not Sunday or Saturday
                workdaysCounted++;
            }
        }
        return currentDate.toISOString().split('T')[0];
    }
    // --- END NEW HELPER FUNCTION ---


    document.addEventListener("DOMContentLoaded", () => {
        // Sidebar functionality
        const buttons = document.querySelectorAll(".menu-btn");
        const pageTitle = document.getElementById("page-title");
        const sections = document.querySelectorAll(".section");

        buttons.forEach(button => {
            button.addEventListener("click", () => {
                const title = button.getAttribute("data-title");
                const target = button.getAttribute("data-target");
                if (title) pageTitle.textContent = title;
                sections.forEach(section => {
                    section.style.display = section.id === target ? "block" : "none";
                });
            });
        });

        // ... (your other sidebar button logic is fine) ...
        document.querySelectorAll(".expandable").forEach(button => {
            button.addEventListener("click", () => {
                const submenu = button.nextElementSibling;
                submenu.style.display = submenu.style.display === "block" ? "none" : "block";
            });
        });

        document.querySelectorAll(".submenu-btn").forEach(button => {
            button.addEventListener("click", () => {
                const title = button.getAttribute("data-title");
                const target = button.getAttribute("data-target");
                if (title) pageTitle.textContent = title;
                sections.forEach(section => {
                    section.style.display = section.id === target ? "block" : "none";
                });
            });
        });


        // --- START: APPLY LEAVE MODAL LOGIC ---
        // (This section is unchanged)
        const modal = document.getElementById("applyLeaveModal");
    const closeBtn = document.getElementById("closeApplyLeaveModal");
    const cancelApplyBtn = document.getElementById("cancelApplyBtn");
    const applyForm = document.querySelector('.apply-leave-form');
    const leaveCategorySelect = document.getElementById("leave_category");
    const leaveFormDetails = document.getElementById("leave_form_details");
    const leaveTypeSelect = document.getElementById("leave_type");
    const leaveTypeOptions = leaveTypeSelect.querySelectorAll("option");
    const leaveCostDisplay = document.getElementById("leaveCostDisplay");
    const leaveCostValue = document.getElementById("leaveCostValue");
    const docReqDiv = document.getElementById("documentRequirement");
    const documentsInput = document.getElementById("documentsInput");
    const docHelp = document.getElementById("docHelp");
    const startDateInput = document.getElementById('startDateInput');
    const endDateInput = document.getElementById("end_date");
    const weekendNote = document.getElementById("weekendNote");

        document.querySelectorAll('.open-apply-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!modal) return;
                if (applyForm) applyForm.reset();
                if (leaveCategorySelect) leaveCategorySelect.value = "";
                if (leaveFormDetails) leaveFormDetails.style.display = "none";
                if (leaveTypeSelect) leaveTypeSelect.value = "";
                leaveTypeOptions.forEach(opt => {
                    if (opt.value !== "") opt.style.display = "none";
                });
                if (leaveTypeSelect) {
                    leaveTypeSelect.dispatchEvent(new Event('change'));
                }
                modal.style.display = 'block';
            });
        });
        
        if (leaveCategorySelect) {
            leaveCategorySelect.addEventListener("change", () => {
                const selectedCategory = leaveCategorySelect.value;
                leaveTypeSelect.value = "";
                leaveTypeSelect.dispatchEvent(new Event('change'));
                if (selectedCategory) {
                    leaveFormDetails.style.display = "block";
                    let hasOptions = false;
                    leaveTypeOptions.forEach(opt => {
                        const unitType = opt.getAttribute('data-unit-type');
                        if (opt.value === "") {
                            opt.style.display = "block";
                        } else if (unitType === selectedCategory) {
                            opt.style.display = "block";
                            hasOptions = true;
                        } else {
                            opt.style.display = "none";
                        }
                    });
                    const placeholder = document.getElementById('leave_type_placeholder');
                    if (placeholder) {
                        placeholder.textContent = hasOptions ? "--- Select ---" : "No types available";
                    }
                } else {
                    leaveFormDetails.style.display = "none";
                }
            });
        }

        function calculateFixedEndDate() {
    console.log("--- Firing calculateFixedEndDate ---");

    const selected = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    if (!selected) {
        console.log("Function stopped: No leave type is selected.");
        return;
    }

    const unitType = selected.getAttribute("data-unit-type");
    const fixed = parseInt(selected.getAttribute("data-fixed-days") || '0', 10);
    const startDate = startDateInput.value;

    // These logs are the most important part
    console.log("Start Date:", startDate);
    console.log("unitType:", unitType);
    console.log("fixed:", fixed);

    // All conditions must be true
    if (unitType === 'fixed' && startDate && fixed > 0) {
        console.log("SUCCESS: Conditions met. Calculating end date.");
        endDateInput.value = addWorkdays(startDate, fixed);
    } else {
        console.log("STOP: Conditions were not met.");
    }
}
    
    // --- 4. The Required 'addWorkdays' Helper Function ---
    /**
     * Adds a number of workdays (Mon-Fri) to a given date.
     * @param {string} startDateString - The date in "YYYY-MM-DD" format.
     * @param {number} daysToAdd - The number of workdays to add.
     * @returns {string} The new date in "YYYY-MM-DD" format.
     */
    function addWorkdays(startDateString, daysToAdd) {
        // Parse the input string. Adding 'T00:00:00' avoids timezone issues.
        const date = new Date(startDateString + 'T00:00:00');
        let daysAdded = 0;

        // Loop until the correct number of *workdays* have been added
        while (daysAdded < daysToAdd) {
            // Move to the next day
            date.setDate(date.getDate() + 1);

            const dayOfWeek = date.getDay(); // 0=Sunday, 6=Saturday

            // Only count the day if it's NOT Saturday (6) or Sunday (0)
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                daysAdded++;
            }
        }

        // Format the date back to "YYYY-MM-DD"
        return date.toISOString().split('T')[0];
    }

        function configureDocumentInputs(allowDocs, maxDocs) {
            if (!documentsInput || !docReqDiv) return;
            if (allowDocs) {
                docReqDiv.style.display = 'block';
                if (maxDocs > 0) {
                    documentsInput.multiple = (maxDocs > 1);
                    docHelp.textContent = `You may upload up to ${maxDocs} file(s).`;
                } else {
                    documentsInput.multiple = true;
                    docHelp.textContent = `You may upload files.`;
                }
                documentsInput.required = true;
            } else {
                docReqDiv.style.display = 'none';
                documentsInput.multiple = true;
                documentsInput.required = false;
                documentsInput.value = '';
                docHelp.textContent = '';
            }
        }

        if (leaveTypeSelect) {
            leaveTypeSelect.addEventListener("change", () => {
                const selected = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
                const cost = selected ? selected.getAttribute("data-cost") : null;
                const allow = selected ? parseInt(selected.getAttribute("data-allow-docs") || '0', 10) : 0;
                const max = selected ? parseInt(selected.getAttribute("data-max-docs") || '0', 10) : 0;
                const unitType = selected ? selected.getAttribute("data-unit-type") : null;
                if (cost && cost !== "0") {
                    leaveCostValue.textContent = cost;
                    leaveCostDisplay.style.display = "block";
                } else {
                    leaveCostDisplay.style.display = "none";
                }
                updateCostDisplay();
                configureDocumentInputs(allow, max);
                if (unitType === 'fixed') {
                    weekendNote.style.display = 'block';
                    endDateInput.readOnly = true;
                    endDateInput.required = false;
                    calculateFixedEndDate();
                } else {
                    weekendNote.style.display = 'none';
                    endDateInput.readOnly = false;
                    endDateInput.required = true;
                    endDateInput.value = '';
                }
            });
        }

        if (startDateInput) {
    startDateInput.addEventListener('change', () => {
        calculateFixedEndDate(); // For fixed leaves
        updateCostDisplay();     // For credit leaves
    });
}

if (endDateInput) {
    endDateInput.addEventListener('change', () => {
        updateCostDisplay();
    });
}

        if (applyForm) {
            applyForm.addEventListener('submit', function (e) {
                const sel = leaveTypeSelect && leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
                if (!sel || sel.value === "") {
                    e.preventDefault();
                    alert('Please select a leave category and type.');
                    return false;
                }
                const allow = sel ? parseInt(sel.getAttribute('data-allow-docs') || '0', 10) : 0;
                const max = sel ? parseInt(sel.getAttribute('data-max-docs') || '0', 10) : 0;
                const filesCount = (documentsInput && documentsInput.files) ? documentsInput.files.length : 0;
                if (allow === 1 && filesCount === 0) {
                    e.preventDefault();
                    configureDocumentInputs(allow, max);
                    alert('This leave type requires at least one supporting document.');
                    if (documentsInput) documentsInput.focus();
                    return false;
                }
                if (allow === 1 && max > 0 && filesCount > max) {
                    e.preventDefault();
                    alert(`You uploaded ${filesCount} file(s) but the maximum allowed is ${max}.`);
                    if (documentsInput) documentsInput.focus();
                    return false;
                }
            });
        }

        if (closeBtn && modal) {
            closeBtn.onclick = () => { modal.style.display = "none"; };
            if (cancelApplyBtn) cancelApplyBtn.onclick = () => { modal.style.display = "none"; };
            window.onclick = (event) => {
                if (event.target === modal) modal.style.display = "none";
            };
        }
        // --- END: APPLY LEAVE MODAL LOGIC ---
        function preventWeekendSelection(event) {
        const input = event.target;
        const selectedDate = input.value;

        const [year, month, day] = selectedDate.split('-').map(Number);
        const dateObj = new Date(year, month - 1, day);

        const dayOfWeek = dateObj.getDay();

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            alert("Weekends (Saturday/Sunday) are not allowed. Please select a weekday.");
            input.value = "";
        }
    }

    if (startDateInput) {
        startDateInput.addEventListener("change", preventWeekendSelection);
    }
    if (endDateInput) {
        endDateInput.addEventListener("change", preventWeekendSelection);
    }


        // --- **START: "MY DOCUMENTS" SECTION LOGIC (FIXED)** ---
        // This section replaces the broken one from your script.

        /**
         * Helper function to convert database column names into readable, friendly names.
         */
        function formatDocType(docType) {
            const docNames = {
                "ResumePath": "Resume",
                "DiplomaPath": "Diploma",
                "GovtIDPath": "Government ID",
                "LocalCertificatePath": "Local Certificate",
                "CivilServiceEligibility": "Civil Service Eligibility",
                "PSABirthCertificate": "PSA Birth Certificate",
                "PSAMarriageCertificate": "PSA Marriage Certificate",
                "NBIClearance": "NBI Clearance",
                "PoliceClearance": "Police Clearance",
                "MedicalCertificate": "Medical Certificate",
                "PersonalDataSheet": "Personal Data Sheet",
                "ValidGovID": "Valid Government ID",
                "PRCLicense": "PRC License",
                "TranscriptOfRecords": "Transcript of Records",
                "CertificatesOfTraining": "Certificates of Training",
                "ServiceRecord": "Service Record",
                "PerformanceRating": "Performance Rating",
                "HonorGraduateEligibility": "Honor Graduate Eligibility",
                "TIN": "TIN",
                "SSS": "SSS",
                "PagIBIG": "Pag-IBIG",
                "PhilHealth": "PhilHealth",
                "OtherDocuments": "Other Documents"
            };
            return docNames[docType] || docType;
        }

        /**
 * Fetches the logged-in user's files and populates the "My Documents" table.
 * This function now links to a separate upload page.
 */
/**
 * Fetches the logged-in user's files and populates the "My Documents" table.
 * This function now shows "Upload" buttons ONLY for standard document types.
 */
function loadMyDocuments() {
    const tableBody = document.getElementById("myDocumentsTableBody");
    if (!tableBody) {
        console.error("Error: Table body 'myDocumentsTableBody' not found.");
        return;
    }

    tableBody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Loading...</td></tr>';

    // **IMPORTANT**: Make sure this fetch path is correct (e.g., /hrlgu/Pages/...)
    fetch("/Pages/get_employee_files.php") 
        .then(response => {
            if (!response.ok) throw new Error("Network response was not ok (404?)");
            return response.json();
        })
        .then(files => {
            if (files.error) {
                throw new Error(files.error);
            }
            
            if (files.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Could not load document list.</td></tr>';
                return;
            }

            let html = "";
            files.forEach(file => {
                const docTypeName = formatDocType(file.DocType);
                // **FIX: Check if the document is a "standard" type**
                const docIsStandard = (file.Type === 'standard'); 

                let fileNameCell = '';
                let actionsCell = '';

                if (file.FilePath && file.FilePath.trim() !== '') {
                    // --- A file EXISTS ---
                    const fileName = file.FilePath.split('/').pop();
                    const filePath = file.FilePath;
                    
                    fileNameCell = `<a href="${filePath}" target="_blank">${fileName}</a>`;
                    
                    // **FIX: Only show "Replace" for standard docs**
                    if (docIsStandard) {
                        actionsCell = `
                            <a href="${filePath}" target="_blank" class="action-btn-view" style="margin-right: 5px;">View</a>
                            <a href="${filePath}" download="${fileName}" class="action-btn-download" style="margin-right: 5px;">Download</a>
                            <button class="action-btn-replace" data-doctype="${file.DocType}">Replace</button>
                        `;
                    } else {
                        // This is an "extra" doc, no replacing
                        actionsCell = `
                            <a href="${filePath}" target="_blank" class="action-btn-view" style="margin-right: 5px;">View</a>
                            <a href="${filePath}" download="${fileName}" class="action-btn-download">Download</a>
                        `;
                    }
                } else {
                    // --- A file is MISSING ---
                    fileNameCell = `<span style="color: #999;">No file uploaded</span>`;

                    // **FIX: Only show "Upload" for standard docs**
                    if (docIsStandard) {
                        actionsCell = `
                            <button class="action-btn-upload" data-doctype="${file.DocType}">Upload</button>
                        `;
                    } else {
                        // This is an "extra" doc type that somehow has no file.
                        actionsCell = `<span style="color: #999;">N/A</span>`;
                    }
                }

                html += `
                    <tr>
                        <td>${docTypeName}</td>
                        <td>${fileNameCell}</td>
                        <td>${actionsCell}</td>
                    </tr>
                `;
            });
            tableBody.innerHTML = html;
        })
        .catch(error => {
            console.error("Error loading documents:", error);
            tableBody.innerHTML = `<tr><td colspan="3" style="text-align:center; color:red;">Error: ${error.message}</td></tr>`;
        });
}

// --- NEW: This code handles the "Upload" button click ---
    
    // 1. Listen for any click inside the document table body
    const docTableBody = document.getElementById("myDocumentsTableBody");
    if (docTableBody) {
        docTableBody.addEventListener('click', function(e) {
            
            // 2. Check if the click was on an "Upload" or "Replace" button
            if (e.target.classList.contains('action-btn-upload') || e.target.classList.contains('action-btn-replace')) {
                const button = e.target;
                const docType = button.dataset.doctype; // This gets "ResumePath", "DiplomaPath", etc.

                // 3. Create a hidden file input element in memory
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.style.display = 'none';

                // 4. Listen for when the user selects a file
                fileInput.addEventListener('change', function() {
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        
                        // 5. Create FormData to send the file
                        const formData = new FormData();
                        formData.append('document', file); // The file itself, name matches your PHP $_FILES
                        formData.append('field', docType); // The "field" your PHP script expects in $_POST

                        // 6. Upload the file using fetch
                        button.disabled = true;
                        button.textContent = 'Uploading...';

                        // **IMPORTANT**: Make sure this path is correct (e.g., /hrlgu/Pages/...)
                        fetch('upload_employee_document.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('File uploaded successfully!');
                                // Reload the document table to show the new file
                                loadMyDocuments(); 
                            } else {
                                alert('Error: ' + data.error);
                                button.disabled = false;
                                button.textContent = e.target.classList.contains('action-btn-upload') ? 'Upload' : 'Replace';
                            }
                        })
                        .catch(err => {
                            console.error('Upload error:', err);
                            alert('A network error occurred. Please check the console.');
                            button.disabled = false;
                            button.textContent = e.target.classList.contains('action-btn-upload') ? 'Upload' : 'Replace';
                        });
                    }
                });

                // 7. Click the hidden input to open the file directory
                document.body.appendChild(fileInput); // Add to page (required for some browsers)
                fileInput.click();
                document.body.removeChild(fileInput); // Clean up
            }
        });
    }
        
        // **FIX 3: This click listener is now correct.**
        // It finds the button and calls the fixed loadMyDocuments() function.
        const docButtons = document.querySelectorAll('button[data-target="documents-section"]');
        docButtons.forEach(button => {
            button.addEventListener('click', () => {
                // When the "My Documents" button is clicked, load the files.
                loadMyDocuments(); // No 'employeeId' needed!
            });
        });
        // --- **END: "MY DOCUMENTS" SECTION LOGIC** ---


        // --- LOGOUT BUTTON ---
        const logoutBtn = document.querySelector('.submenu-btn[data-title="Logout"]');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to logout?')) return;
                window.location.href = '/Pages/Logout.php';
            });
        }
        // --- END LOGOUT ---


        // --- AFTER REDIRECT MESSAGES ---
        (function checkQueryMessages() {
            const params = new URLSearchParams(window.location.search);
            const success = params.get('success');
            const error = params.get('error');
            const tab = params.get('tab');
            if (tab) {
                sections.forEach(section => { section.style.display = section.id === tab ? "block" : "none"; });
                if (pageTitle && document.querySelector(`.submenu-btn[data-target="${tab}"]`)) {
                    pageTitle.textContent = document.querySelector(`.submenu-btn[data-target="${tab}"]`).getAttribute('data-title') || pageTitle.textContent;
                }
            }
            if (success) {
                alert(decodeURIComponent(success));
                history.replaceState(null, '', window.location.pathname);
            } else if (error) {
                alert(decodeURIComponent(error));
                history.replaceState(null, '', window.location.pathname);
            }
        })();
        // --- END MESSAGES ---
        
        // --- **NEW**: Logic for "View Leave Details" Modal ---
    const leaveHistoryTable = document.getElementById("leaveHistoryTableBody");
    const detailsModal = document.getElementById("viewLeaveDetailsModal");
    const detailsContent = document.getElementById("leaveDetailsContent");
    const closeDetailsBtn = document.getElementById("closeViewDetailsModal");
    const cancelDetailsBtn = document.getElementById("cancelLeaveDetailsBtn");

    if (leaveHistoryTable) {
        leaveHistoryTable.addEventListener('click', function(e) {
            // Check if a 'View' button was clicked
            if (e.target.classList.contains('view-application-btn')) {
                const appId = e.target.dataset.id;
                
                // 1. Show modal and loading text
                detailsModal.style.display = 'block';

                // 2. Fetch the application details
                fetch('get_application_details.php?id=' + encodeURIComponent(appId))
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Handle error
                    document.getElementById('leaveDetailsContent').innerHTML = 
                        `<p style="color:red; text-align:center;">${data.error}</p>`;
                    return;
                }

                // === NEW POPULATION LOGIC ===
                
                // --- Box 1-5 (Employee Info) ---
                // **These MUST be sent by your PHP script now**
                document.getElementById('form-department').textContent = data.Department || 'N/A';
                document.getElementById('form-name').textContent = 
                    `${data.LastName || ''}, ${data.FirstName || ''} ${data.MiddleName || ''}`;
                document.getElementById('form-date-filing').textContent = data.DateRequestedFormatted; // e.g., 'October 25, 2025'
                document.getElementById('form-position').textContent = data.Position || 'N/A';
                document.getElementById('form-salary').textContent = data.Salary ? `₱${data.Salary}` : 'N/A';
                
                // --- Box 6 (Leave Details) ---
                document.getElementById('form-leave-type').innerHTML = `<span class="cs-checkbox">[ ]</span> ${data.TypeName}`;
                
                // Add "In Hospital" or "Abroad" details if they exist
                let details = data.Reason; // Use Reason as the default detail
                if (data.TypeName === 'Sick Leave') {
                    details = `<span class="cs-checkbox">[ ]</span> In Hospital<br>
                               <span class="cs-checkbox">[ ]</span> Out Patient: ${data.Reason}`;
                } else if (data.TypeName === 'Vacation Leave') {
                    details = `<span class="cs-checkbox">[ ]</span> Within Philippines<br>
                               <span class="cs-checkbox">[ ]</span> Abroad (Specify):`;
                }
                document.getElementById('form-leave-details').innerHTML = details;
                
                document.getElementById('form-duration').textContent = `${data.DurationDays} day(s)`;
                document.getElementById('form-dates').textContent = `${data.StartDate} to ${data.EndDate}`;

                // --- Reason Box ---
                document.getElementById('form-reason').textContent = data.Reason || 'No reason provided.';

                // --- Box 7 (Admin Action) ---
                // Clear any old content first
                document.getElementById('form-recommendation').innerHTML = 
                    `<span class="cs-checkbox">[ ]</span> For approval<br>
                     <span class="cs-checkbox">[ ]</span> For disapproval due to...`;
                document.getElementById('form-approval-days').textContent = '...';
                document.getElementById('form-disapproval-reason').textContent = '...';
                
                if (data.Status === 'Approved') {
                    document.getElementById('form-recommendation').innerHTML = 
                        `<span class="cs-checkbox">[ ]</span> For approval`;
                    document.getElementById('form-approval-days').textContent = `${data.DurationDays} day(s) with pay`;
                } else if (data.Status === 'Rejected') {
                    document.getElementById('form-recommendation').innerHTML = 
                        `<span class="cs-checkbox">[ ]</span> For disapproval due to: ${data.RejectionReason || ''}`;
                    document.getElementById('form-disapproval-reason').textContent = data.RejectionReason || 'See 7.B';
                }

                // Hide the simple text and show the form
                // (We no longer need to do this, as the simple text is gone)
            })
            .catch(err => {
                console.error('Error fetching leave details:', err);
                document.getElementById('printableLeaveArea').innerHTML = '<p style="color:red; text-align:center;">A network error occurred.</p>';
            });
            }
        });
    }

    // Close button logic
    if (closeDetailsBtn) {
        closeDetailsBtn.onclick = () => { detailsModal.style.display = 'none'; };
    }
    if (cancelDetailsBtn) {
        cancelDetailsBtn.onclick = () => { detailsModal.style.display = 'none'; };
    }
    // Also close if clicking outside the content
    window.addEventListener('click', (event) => {
        if (event.target == detailsModal) {
            detailsModal.style.display = 'none';
        }
    });
    // --- **END NEW** ---
    
    // --- NEW: Profile Photo Cropper Logic ---
    
    // Get all the modal elements
    const photoModal = document.getElementById('photoModal');
    const changeBtn = document.getElementById('changeProfilePhotoBtn');
    const fileInput = document.getElementById('photoUploadInput');
    const cropperContainer = document.getElementById('cropperContainer');
    const imageToCrop = document.getElementById('imageToCrop');
    const saveBtn = document.getElementById('saveCropBtn');
    let cropper; // This will hold the cropper.js object

    // Open the modal
    changeBtn.addEventListener('click', () => {
        photoModal.style.display = 'block';
    });

    // Close the modal
    closeBtn.addEventListener('click', () => {
        photoModal.style.display = 'none';
        if (cropper) {
            cropper.destroy(); // Important to reset
        }
        cropperContainer.style.display = 'none';
        saveBtn.style.display = 'none';
        fileInput.value = ''; // Reset file input
    });

    // When a user selects a file...
    fileInput.addEventListener('change', (e) => {
        const files = e.target.files;
        if (files && files.length > 0) {
            // Read the file and show it
            const reader = new FileReader();
            reader.onload = (event) => {
                imageToCrop.src = event.target.result;
                cropperContainer.style.display = 'block';
                saveBtn.style.display = 'inline-block';

                // Destroy old cropper if it exists
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize Cropper.js
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1 / 1,  // 1:1 for a square profile pic
                    viewMode: 1,         // Restricts the crop box
                    background: false,
                });
            };
            reader.readAsDataURL(files[0]);
        }
    });

    // When the "Save" button is clicked...
    saveBtn.addEventListener('click', () => {
        if (!cropper) return;
        
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        // Get the cropped image data as a canvas
        const canvas = cropper.getCroppedCanvas({
            width: 300,  // Save as a 300x300px image
            height: 300,
            imageSmoothingQuality: 'high',
        });

        // Convert canvas to a Blob (a file-like object)
        canvas.toBlob((blob) => {
            // Create FormData to send to PHP
            const formData = new FormData();
            // This 'croppedImage' MUST match the $_FILES key in your PHP
            formData.append('croppedImage', blob, 'profile.png'); 

            // Upload using fetch
            // **IMPORTANT**: Adjust this path to your PHP file (e.g., /hrlgu/Pages/...)
            fetch('/Pages/upload_profile_photo.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success! Update the profile pic on the page
                    // We add a random query to bust the cache
                    const newSrc = '/' + data.newPath + '?t=' + new Date().getTime();
                    document.getElementById('profilePhotoImg').src = newSrc;
                    
                    // Close the modal
                    closeBtn.click(); 
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(err => {
                console.error('Upload Error:', err);
                alert('A network error occurred. Please try again.');
            })
            .finally(() => {
                // Reset the save button
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Photo';
            });

        }, 'image/png');
    });
    // --- END: Profile Photo Logic ---
    const downloadBtn = document.getElementById("downloadPdfBtn");

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            
            // 1. Get the element to print (the parent wrapper)
            const elementToPrint = document.getElementById("printableLeaveArea");

            // 2. Try to get a good filename (e.g., from Control #)
            let fileName = 'LeaveApplication.pdf'; // Default name
            try {
                // This looks for the Control Number to name the file
                const controlNum = document.getElementById('form-control-number-span').textContent;
                if (controlNum && controlNum !== 'Loading...') {
                    fileName = `Leave-Application-${controlNum}.pdf`;
                }
            } catch (e) { /* Caching errors just in case */ }
            
            // 3. Set the options for the PDF
            const options = {
                margin:       10, // 10mm margin
                filename:     fileName,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true }, // Higher scale for better quality
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // 4. Run the html2pdf library
            // It grabs "elementToPrint" and saves it
            html2pdf().set(options).from(elementToPrint).save();
        });
    }
    
    function countWorkdays(startDate, endDate) {
    // Create dates in a way that avoids timezone issues
    const start = new Date(startDate + 'T00:00:00');
    const end = new Date(endDate + 'T00:00:00');

    // Check for invalid dates or end before start
    if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) {
        return 0;
    }

    let days = 0;
    const d = new Date(start); // Clone start date

    // Loop until 'd' passes the 'end' date
    while (d <= end) {
        const dayOfWeek = d.getDay(); // 0 = Sunday, 6 = Saturday
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            days++; // Only count weekdays
        }
        d.setDate(d.getDate() + 1);
    }
    return days;
}

/**
 * Calculates and shows the total leave cost (Cost * Days).
 */
function updateCostDisplay() {
    const selected = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    if (!selected) {
        leaveCostDisplay.style.display = 'none';
        return;
    }

    // Get the cost per day from the <option>
    const costPerDay = parseFloat(selected.getAttribute("data-cost") || '0');
    const unitType = selected.getAttribute("data-unit-type");

    if (costPerDay === 0) {
        leaveCostDisplay.style.display = 'none';
        return;
    }

    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    // Only 'credit' types (VL, SL) have a calculated cost
    if (unitType === 'credit' && startDate && endDate) {
        const workdays = countWorkdays(startDate, endDate);
        
        if (workdays > 0) {
            const totalCost = workdays * costPerDay;
            // Use .toFixed(3) to match your 3-decimal-place database
            leaveCostValue.textContent = `${totalCost.toFixed(3)} (for ${workdays} days)`;
        } else {
            // Show per-day if dates are invalid (e.g., end before start)
            leaveCostValue.textContent = `${costPerDay.toFixed(3)} (per day)`;
        }
    } else {
        // For 'fixed' types or if dates are missing, just show the per-day cost
        leaveCostValue.textContent = `${costPerDay.toFixed(3)} (per day)`;
    }

    // Show the <p> tag
    leaveCostDisplay.style.display = 'block';
}

function countWorkdays(startDate, endDate) {
    // Create dates in a way that avoids timezone issues
    const start = new Date(startDate + 'T00:00:00');
    const end = new Date(endDate + 'T00:00:00');

    // Check for invalid dates or end before start
    if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) {
        return 0;
    }

    let days = 0;
    const d = new Date(start); // Clone start date

    // Loop until 'd' passes the 'end' date
    while (d <= end) {
        const dayOfWeek = d.getDay(); // 0 = Sunday, 6 = Saturday
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            days++; // Only count weekdays
        }
        d.setDate(d.getDate() + 1);
    }
    return days;
}

/**
 * Calculates and shows the total leave cost (Cost * Days).
 */
function updateCostDisplay() {
    const selected = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    if (!selected) {
        leaveCostDisplay.style.display = 'none';
        return;
    }

    // Get the cost per day from the <option>
    const costPerDay = parseFloat(selected.getAttribute("data-cost") || '0');
    const unitType = selected.getAttribute("data-unit-type");

    if (costPerDay === 0) {
        leaveCostDisplay.style.display = 'none';
        return;
    }

    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    // Only 'credit' types (VL, SL) have a calculated cost
    if (unitType === 'credit' && startDate && endDate) {
        const workdays = countWorkdays(startDate, endDate);
        
        if (workdays > 0) {
            const totalCost = workdays * costPerDay;
            // Use .toFixed(3) to match your 3-decimal-place database
            leaveCostValue.textContent = `${totalCost.toFixed(3)} (for ${workdays} days)`;
        } else {
            // Show per-day if dates are invalid (e.g., end before start)
            leaveCostValue.textContent = `${costPerDay.toFixed(3)} (per day)`;
        }
    } else {
        // For 'fixed' types or if dates are missing, just show the per-day cost
        leaveCostValue.textContent = `${costPerDay.toFixed(3)} (per day)`;
    }

    // Show the <p> tag
    leaveCostDisplay.style.display = 'block';
}

    });

// ============================================
// SIDEBAR MENU FUNCTIONALITY (HRMO Theme)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const menuButtons = document.querySelectorAll('.menu-btn');
    const expandableButtons = document.querySelectorAll('.menu-btn.expandable');

    // Mobile menu toggle
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('open');
        });
    }

    // Close sidebar when overlay is clicked
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('open');
        });
    }

    // Expandable menu items
    expandableButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.classList.toggle('open');
                this.classList.toggle('active');
            }
        });
    });

    // Menu button click handlers
    menuButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('expandable')) {
                const target = this.getAttribute('data-target');
                if (target && target !== 'n/a') {
                    // Remove active class from all menu buttons
                    menuButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Hide all sections
                    const sections = document.querySelectorAll('.section');
                    sections.forEach(section => section.style.display = 'none');
                    
                    // Show target section
                    const targetSection = document.getElementById(target);
                    if (targetSection) {
                        targetSection.style.display = 'block';
                        
                        // Update page title
                        const pageTitle = document.getElementById('page-title');
                        if (pageTitle) {
                            pageTitle.textContent = this.getAttribute('data-title') || 'Dashboard';
                        }
                    }
                    
                    // Close mobile menu
                    if (window.innerWidth <= 600) {
                        sidebar.classList.remove('open');
                        sidebarOverlay.classList.remove('open');
                    }
                } else if (target === 'n/a') {
                    // Handle logout
                    window.location.href = '/hrlgu/Pages/Logout.php';
                }
            }
        });
    });

    // Handle submenu button clicks
    const submenuButtons = document.querySelectorAll('.submenu-btn');
    submenuButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            if (target && target !== 'n/a') {
                // Remove active class from all buttons
                menuButtons.forEach(btn => btn.classList.remove('active'));
                submenuButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Hide all sections
                const sections = document.querySelectorAll('.section');
                sections.forEach(section => section.style.display = 'none');
                
                // Show target section
                const targetSection = document.getElementById(target);
                if (targetSection) {
                    targetSection.style.display = 'block';
                    
                    // Update page title
                    const pageTitle = document.getElementById('page-title');
                    if (pageTitle) {
                        pageTitle.textContent = this.getAttribute('data-title') || 'Dashboard';
                    }
                }
                
                // Close mobile menu
                if (window.innerWidth <= 600) {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('open');
                }
            } else if (target === 'n/a') {
                // Handle logout
                window.location.href = '/hrlgu/Pages/Logout.php';
            }
        });
    });

    // Set initial active state
    const firstMenuBtn = document.querySelector('.menu-btn:not(.expandable)');
    if (firstMenuBtn) {
        firstMenuBtn.classList.add('active');
    }
});

</script>

</html>