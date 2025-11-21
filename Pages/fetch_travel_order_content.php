<?php
// fetch_travel_order_content.php
header('Content-Type: text/html; charset=utf-8');
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    echo '<p style="color:red; text-align:center;">Error: No Travel ID provided.</p>';
    exit;
}

$travelId = (int)$_GET['id'];

// 1. QUERY DATABASE
$sql = "SELECT 
            t.*, 
            e.FirstName, e.LastName, e.EmployeeID,
            d.DepartmentName,
            p.PositionName
        FROM TravelOrder t
        JOIN Employee e ON t.EmployeeID = e.EmployeeID
        LEFT JOIN Department d ON e.DepartmentID = d.DepartmentID
        LEFT JOIN Position p ON e.PositionID = p.PositionID
        WHERE t.TravelID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<p style="color:red; text-align:center;">Error: Database prepare failed.</p>';
    exit;
}

$stmt->bind_param("i", $travelId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo '<p style="color:red; text-align:center;">Error: Travel Order not found.</p>';
    exit;
}

// 2. PREPARE VARIABLES
$fullName = htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']);
$position = htmlspecialchars($row['PositionName'] ?? 'N/A');
$department = htmlspecialchars($row['DepartmentName'] ?? 'N/A');
$destination = htmlspecialchars($row['Destination']);
$purpose = htmlspecialchars($row['Purpose']);
$startDate = date("F d, Y", strtotime($row['StartDate']));
$endDate = date("F d, Y", strtotime($row['EndDate']));
$dateRequested = isset($row['DateRequested']) ? date("F d, Y", strtotime($row['DateRequested'])) : date("F d, Y"); 

// ==========================================================
// LOGIC: HANDLE DIRECTIVE VS REQUEST
// ==========================================================

// Check if the DB says this was directed by someone
$hasDirectiveInDb = isset($row['DirectedBy']) && !empty($row['DirectedBy']);

if ($hasDirectiveInDb) {
    // LAYOUT 1: It is a Directive
    $isDirective = true;
    
    // FORCE PLACEHOLDER NAME
    // Even if DB says "Admin John", we display "Hon. Alexis S. Quina"
    $directedBy = "HON. ALEXIS S. QUINA"; 
} else {
    // LAYOUT 2: Employee Request
    $isDirective = false;
    $directedBy = null;
}

// Standard Signatories
$municipalMayor = "HON. ALEXIS S. QUINA";
$deptHead = "HR Staff"; 
?>

<!-- 3. HTML STRUCTURE FOR PDF -->
<style>
    .cs-pdf-container { font-family: Arial, sans-serif; color: #000; background: #fff; padding: 20px; }
    .cs-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
    .cs-header-center { text-align: center; }
    .cs-header-center h2 { margin: 2px 0; font-size: 16px; font-weight: bold; }
    .cs-header-center p { margin: 0; font-size: 12px; }
    .cs-logo { width: 70px; height: 70px; object-fit: contain; }
    
    .cs-section { border: 1px solid #000; margin-bottom: -1px; padding: 5px; display: flex; }
    .cs-section-col { flex: 1; padding: 0 5px; }
    .cs-section-title { background: #eee; font-weight: bold; font-size: 11px; padding: 3px; border: 1px solid #000; margin-bottom: -1px; }
    .cs-label { font-size: 9px; color: #444; display: block; text-transform: uppercase; }
    .cs-value { font-size: 12px; font-weight: bold; display: block; margin-bottom: 2px; }
    .cs-body-text { font-size: 12px; padding: 10px; text-align: justify; line-height: 1.5; }
    
    .cs-signature-grid { display: flex; margin-top: 40px; text-align: center; }
    .cs-sig-box { flex: 1; }
    .cs-sig-name { font-weight: bold; text-decoration: underline; display: block; margin-top: 30px; }
</style>

<div id="printableTravelArea" class="cs-pdf-container">
    
    <!-- Header -->
    <div class="cs-header">
        <img src="../Pictures/logo.png" class="cs-logo" onerror="this.style.opacity=0">
        <div class="cs-header-center">
            <p>Republic of the Philippines</p>
            <p>Province of Misamis Oriental</p>
            <h2>MUNICIPALITY OF BALINGASAG</h2>
            <h2>TRAVEL ORDER</h2>
        </div>
        <div class="cs-logo" style="width:70px;"></div>
    </div>

    <?php if ($isDirective): ?>
        <!-- === LAYOUT 1: ADMIN DIRECTIVE === -->
        <div class="cs-section-title">1. DIRECTIVE DETAILS</div>
        <div class="cs-section">
            <div class="cs-section-col" style="border-right: 1px solid #000;">
                <span class="cs-label">TO:</span>
                <span class="cs-value"><?= $fullName ?></span>
                <span class="cs-label" style="margin-top:2px; font-size:8px;"><?= $position ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">FROM:</span>
                <!-- Forces "HON. ALEXIS S. QUINA" -->
                <span class="cs-value"><?= $directedBy ?></span>
                <span class="cs-label" style="margin-top:2px; font-size:8px;">Municipal Mayor</span>
            </div>
        </div>
        <div class="cs-section">
            <div class="cs-section-col">
                <span class="cs-label">DATE ISSUED:</span>
                <span class="cs-value"><?= $dateRequested ?></span>
            </div>
        </div>

        <div class="cs-section" style="display:block; margin-top: 10px; border: 1px solid #000;">
            <span class="cs-label" style="padding:5px;">ORDER:</span>
            <div class="cs-body-text">
                You are hereby directed to proceed to <b><?= $destination ?></b> on 
                <b><?= $startDate ?> to <?= $endDate ?></b>, to attend the 
                <b><?= $purpose ?></b>.<br><br>
                Your travel is considered official in the interest of public service.
            </div>
        </div>

        <div class="cs-signature-grid">
            <div class="cs-sig-box" style="flex: 2;"></div>
            <div class="cs-sig-box">
                <!-- Forces "HON. ALEXIS S. QUINA" -->
                <span class="cs-sig-name"><?= $directedBy ?></span>
                <span class="cs-label">Municipal Mayor</span>
            </div>
        </div>

    <?php else: ?>
        <!-- === LAYOUT 2: EMPLOYEE REQUEST === -->
        <div class="cs-section">
            <div class="cs-section-col" style="border-right:1px solid #000">
                <span class="cs-label">1. DEPARTMENT</span>
                <span class="cs-value"><?= $department ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">2. NAME</span>
                <span class="cs-value"><?= $fullName ?></span>
            </div>
        </div>
        <div class="cs-section">
            <div class="cs-section-col" style="border-right:1px solid #000">
                <span class="cs-label">3. DATE FILED</span>
                <span class="cs-value"><?= $dateRequested ?></span>
            </div>
            <div class="cs-section-col" style="border-right:1px solid #000">
                <span class="cs-label">4. POSITION</span>
                <span class="cs-value"><?= $position ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">5. DESTINATION</span>
                <span class="cs-value"><?= $destination ?></span>
            </div>
        </div>

        <div class="cs-section-title" style="margin-top:5px;">6. DETAILS OF TRAVEL</div>
        <div class="cs-section">
            <div class="cs-section-col" style="border-right:1px solid #000; flex: 2;">
                <span class="cs-label">PURPOSE</span>
                <span class="cs-value" style="font-weight:normal;"><?= $purpose ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">DATES</span>
                <span class="cs-value"><?= $startDate ?> - <br><?= $endDate ?></span>
            </div>
        </div>

        <div class="cs-section-title" style="margin-top:5px;">7. APPROVAL</div>
        <div class="cs-section" style="border:none; padding: 10px 0;">
            <div class="cs-body-text" style="padding:0;">
                Permission to travel on official business is hereby requested.
            </div>
        </div>

        <div class="cs-signature-grid">
            <div class="cs-sig-box">
                <span class="cs-sig-name"><?= $deptHead ?></span>
                <span class="cs-label">Human Resource Department Office</span>
            </div>
            <div class="cs-sig-box">
                <span class="cs-label">APPROVED:</span>
                <span class="cs-sig-name"><?= $municipalMayor ?></span>
                <span class="cs-label">Municipal Mayor</span>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="cs-header">
        <img src="../Pictures/logo.png" class="cs-logo" onerror="this.style.opacity=0">
        <div class="cs-header-center">
            <p>Republic of the Philippines</p>
            <p>Province of Misamis Oriental</p>
            <h2>MUNICIPALITY OF BALINGASAG</h2>
            <h2>TRAVEL ORDER</h2>
        </div>
        <div class="cs-logo" style="width:70px;"></div>
    </div>

    <?php if ($isDirective): ?>
        <!-- === LAYOUT 1: ADMIN DIRECTIVE === -->
        <div class="cs-section-title">1. DIRECTIVE DETAILS</div>
        <div class="cs-section">
            <div class="cs-section-col" style="border-right: 1px solid #000;">
                <span class="cs-label">TO:</span>
                <span class="cs-value"><?= $fullName ?></span>
                <span class="cs-label" style="margin-top:2px; font-size:8px;"><?= $position ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">FROM:</span>
                <!-- Forces "HON. ALEXIS S. QUINA" -->
                <span class="cs-value"><?= $directedBy ?></span>
                <span class="cs-label" style="margin-top:2px; font-size:8px;">Municipal Mayor</span>
            </div>
        </div>
        <div class="cs-section">
            <div class="cs-section-col">
                <span class="cs-label">DATE ISSUED:</span>
                <span class="cs-value"><?= $dateRequested ?></span>
            </div>
        </div>

        <div class="cs-section" style="display:block; margin-top: 10px; border: 1px solid #000;">
            <span class="cs-label" style="padding:5px;">ORDER:</span>
            <div class="cs-body-text">
                You are hereby directed to proceed to <b><?= $destination ?></b> on 
                <b><?= $startDate ?> to <?= $endDate ?></b>, to attend the 
                <b><?= $purpose ?></b>.<br><br>
                Your travel is considered official in the interest of public service.
            </div>
        </div>

        <div class="cs-signature-grid">
            <div class="cs-sig-box" style="flex: 2;"></div>
            <div class="cs-sig-box">
                <!-- Forces "HON. ALEXIS S. QUINA" -->
                <span class="cs-sig-name"><?= $directedBy ?></span>
                <span class="cs-label">Municipal Mayor</span>
            </div>
        </div>

    <?php else: ?>
        <!-- === LAYOUT 2: EMPLOYEE REQUEST === -->
        <div class="cs-section">
            <div class="cs-section-col" style="border-right:1px solid #000">
                <span class="cs-label">1. DEPARTMENT</span>
                <span class="cs-value"><?= $department ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">2. NAME</span>
                <span class="cs-value"><?= $fullName ?></span>
            </div>
        </div>
        <div class="cs-section">
            <div class="cs-section-col" style="border-right:1px solid #000">
                <span class="cs-label">3. DATE FILED</span>
                <span class="cs-value"><?= $dateRequested ?></span>
            </div>
            <div class="cs-section-col" style="border-right:1px solid #000">
                <span class="cs-label">4. POSITION</span>
                <span class="cs-value"><?= $position ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">5. DESTINATION</span>
                <span class="cs-value"><?= $destination ?></span>
            </div>
        </div>

        <div class="cs-section-title" style="margin-top:5px;">6. DETAILS OF TRAVEL</div>
        <div class="cs-section">
            <div class="cs-section-col" style="border-right:1px solid #000; flex: 2;">
                <span class="cs-label">PURPOSE</span>
                <span class="cs-value" style="font-weight:normal;"><?= $purpose ?></span>
            </div>
            <div class="cs-section-col">
                <span class="cs-label">DATES</span>
                <span class="cs-value"><?= $startDate ?> - <br><?= $endDate ?></span>
            </div>
        </div>

        <div class="cs-section-title" style="margin-top:5px;">7. APPROVAL</div>
        <div class="cs-section" style="border:none; padding: 10px 0;">
            <div class="cs-body-text" style="padding:0;">
                Permission to travel on official business is hereby requested.
            </div>
        </div>

        <div class="cs-signature-grid">
            <div class="cs-sig-box">
                <span class="cs-sig-name"><?= $deptHead ?></span>
                <span class="cs-label">Human Resource Department Office</span>
            </div>
            <div class="cs-sig-box">
                <span class="cs-label">APPROVED:</span>
                <span class="cs-sig-name"><?= $municipalMayor ?></span>
                <span class="cs-label">Municipal Mayor</span>
            </div>
        </div>
    <?php endif; ?>
</div>