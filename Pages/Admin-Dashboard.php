<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not logged in as admin
    echo "<script>alert('You must log in as admin to access this page.'); window.location.href='/hrlgu/Pages/Login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../Pictures/logo.ico">
    <link rel="stylesheet" href="../CSS/Admin-Dashboard.css">
    <link rel="stylesheet" href="../CSS/Sidebar.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-section">
                    <img src="/hrlgu/Pictures/logo.png" alt="Logo" class="sidebar-logo">
                    <h2 class="sidebar-title">Admin Menu</h2>
                </div>
            </div>

            <button class="menu-btn" data-title="Dashboard" data-target="dashboard-section">
                <img src="/hrlgu/Pictures/icons/dashboard.png" alt="Dashboard" class="icon">
                <span class="btn-text">Dashboard</span>
            </button>

            <button class="menu-btn" data-title="View Employees" data-target="employees-section">
                <img src="/hrlgu/Pictures/icons/view-employee.png" alt="Employees" class="icon">
                <span class="btn-text">View Employees</span>
            </button>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <img src="/hrlgu/Pictures/icons/leave.png" alt="Leave" class="icon">
                    <span class="btn-text">History ▸</span>
                </button>
                <div class="submenu">
                    <button class="menu-btn" data-title="History" data-target="history-leave-section">
                        <img src="/hrlgu/Pictures/icons/history.png" alt="History-leave"
                            class="icon">
                        <span class="btn-text">History Record Leaves</span>
                    </button>
                    <button class="menu-btn" data-title="History" data-target="travel-history-section">
                        <img src="/hrlgu/Pictures/icons/history.png" alt="History-travel"
                            class="icon">
                        <span class="btn-text">History Travel Order</span>
                    </button>
                    <button class="menu-btn" data-title="History" data-target="archive-section">
                        <img src="/hrlgu/Pictures/icons/history.png" alt="History-employee"
                            class="icon">
                        <span class="btn-text">Employee Archived</span>
                    </button>
                </div>
            </div>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <img src="/hrlgu/Pictures/icons/leave.png" alt="Leave" class="icon">
                    <span class="btn-text">Leave Management ▸</span>
                </button>
                <div class="submenu">
                    <button class="submenu-btn" data-title="Leave Management" data-target="manage-leave-section">
                        <img src="/hrlgu/Pictures/icons/manageleave.png" alt="Manage Leave"
                            class="icon">
                        <span class="btn-text">Manage Leave</span>
                    </button>
                    <button class="submenu-btn" data-title="Leave Management" data-target="plan-section">
                        <img src="/hrlgu/Pictures/icons/planner.png" alt="Planner" class="icon">
                        <span class="btn-text">Plan for Leave</span>
                    </button>
                    <button class="submenu-btn" data-title="Leave Management" data-target="request-section">
                        <img src="/hrlgu/Pictures/icons/request.png" alt="Request" class="icon">
                        <span class="btn-text">Request</span>
                    </button>

                    <button class="submenu-btn" data-title="Leave Management" data-target="calendar-section">
                        <img src="/hrlgu/Pictures/icons/calendar.png" alt="Calendar" class="icon">
                        <span class="btn-text">See Calendar</span>
                    </button>
                </div>
            </div>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <img src="/hrlgu/Pictures/icons/leave.png" alt="Travel-Order" class="icon">
                    <span class="btn-text">Travel Order Management ▸</span>
                </button>
                <div class="submenu">
                    <button class="submenu-btn" data-title="Travel Order Management"
                        data-target="manage-travel-section">
                        <img src="/hrlgu/Pictures/icons/travel.png" alt="Travel" class="icon">
                        <span class="btn-text">Manage Travel Order</span>
                    </button>
                    <button class="submenu-btn" data-title="Travel Order Management" data-target="ongoing-travel-section">
                        <img src="/hrlgu/Pictures/icons/planner.png" alt="Planner" class="icon">
                        <span class="btn-text">Ongoing/Upcoming Travels</span>
                    </button>
                    <button class="submenu-btn" data-title="Travel Order Management" data-target="travel-history-section">
                        <img src="/hrlgu/Pictures/icons/history.png" alt="History" class="icon">
                        <span class="btn-text">Travel History</span>
                    </button>
                </div>
            </div>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <img src="/hrlgu/Pictures/icons/department-position.png" alt="Department"
                        class="icon">
                    <span class="btn-text">Department and Position ▸</span>
                </button>
                <div class="submenu">
                    <button class="submenu-btn" data-title="Manage Department" data-target="manage-department-section">
                        <img src="/hrlgu/Pictures/icons/department.png" alt="Department"
                            class="icon">
                        <span class="btn-text">Manage Department</span>
                    </button>
                    <button class="submenu-btn" data-title="Position" data-target="manage-position-section">
                        <img src="/hrlgu/Pictures/icons/position.png" alt="Position" class="icon">
                        <span class="btn-text">Manage Position</span>
                    </button>
                </div>
            </div>

            <div class="menu-group">
                <button class="menu-btn expandable">
                    <img src="/hrlgu/Pictures/icons/setting.png" alt="Settings" class="icon">
                    <span class="btn-text">Settings ▸</span>
                </button>
                <div class="submenu">
                    <button class="submenu-btn" data-title="Logout" data-target="n/a" onclick="handleLogout(event)">
                        <img src="/hrlgu/Pictures/icons/logout.png" alt="Logout" class="icon">
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
                <!-- Dashboard -->
                <div id="dashboard-section" class="section">
                    <h2>Admin Dashboard</h2>

                    <!-- Summary Boxes with Badges (Moved to Top) -->
                    <div style="display: flex; gap: 15px; margin: 20px 0; flex-wrap: wrap;">
                        <div style="flex:1; min-width: 150px; padding:20px; background:#f2f2f2; text-align:center; border-radius:10px; position: relative;">
                            <span style="position: absolute; top: -10px; right: 10px; background: #ff9800; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">PENDING</span>
                            <h3 id="requestedBox">0</h3>
                            <p>Requested Leave</p>
                        </div>
                        <div style="flex:1; min-width: 150px; padding:20px; background:#d4edda; text-align:center; border-radius:10px; position: relative;">
                            <span style="position: absolute; top: -10px; right: 10px; background: #28a745; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">APPROVED</span>
                            <h3 id="approvedBox">0</h3>
                            <p>Approved Leave</p>
                        </div>
                        <div style="flex:1; min-width: 150px; padding:20px; background:#f8d7da; text-align:center; border-radius:10px; position: relative;">
                            <span style="position: absolute; top: -10px; right: 10px; background: #dc3545; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">REJECTED</span>
                            <h3 id="rejectedBox">0</h3>
                            <p>Rejected Leave</p>
                        </div>
                        <div style="flex:1; min-width: 150px; padding:20px; background:#e3f2fd; text-align:center; border-radius:10px; position: relative;">
                            <span style="position: absolute; top: -10px; right: 10px; background: #2196F3; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">SICK</span>
                            <h3 id="sickLeaveBox">0</h3>
                            <p>Sick Leave</p>
                        </div>
                        <div style="flex:1; min-width: 150px; padding:20px; background:#f3e5f5; text-align:center; border-radius:10px; position: relative;">
                            <span style="position: absolute; top: -10px; right: 10px; background: #9c27b0; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">VACATION</span>
                            <h3 id="vacationLeaveBox">0</h3>
                            <p>Vacation Leave</p>
                        </div>
                    </div>

                    <!-- Analytic Graph -->
                    <div style="max-width: 100%; margin: 20px 0;">
                        <canvas id="statusChart" height="50"></canvas>
                    </div>

                    <!-- Pie Chart -->
                    <div style="max-width: 350px; margin: 20px auto;">
                        <canvas id="typeChart" height="40"></canvas>
                    </div>
                </div>


                <!-- View Employees -->
                <div id="employees-section" class="section" style="display:none;">
                    <h2>Employees</h2>
                    <button class="add-btn">+ Add Employee</button>
                    <input class="searchbar" type="text" id="searchEmployeeInput" placeholder="Search for Employees">
                    <button class="search-btn" onclick="searchEmployee()">Search</button>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Birthdate</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Date Hired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include 'employee_table.php'; ?>
                        </tbody>
                    </table>
                </div>



                <!-- History Record Leaves -->
                <div id="history-leave-section" class="section" style="display:none;">
    <h2>History Record Leaves</h2>
    
    <input class="searchbar" type="text" id="historySearchInput" placeholder="Search by name, ID, or leave type...">
    <button class="search-btn" id="historySearchBtn">Search</button>

    <table>
        <thead>
            <tr>
                <th>Control No.</th>
                <th>Name</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Date Started</th>
                <th>Date Ended</th>
                <th>Status</th>
                <th>Documents</th>
            </tr>
        </thead>
        <tbody id="historyTableBody">
            <tr><td colspan="8">Search to see history records.</td></tr>
        </tbody>
    </table>
</div>

                <div id="history-travel-section" class="section" style="display:none;">
                    <h2>Travel Order History</h2>
                    <input class="searchbar" type="text" id="searchTravelInput" placeholder="Search for Employees">
                    <button class="search-btn" onclick="searchEmployee()">Search</button>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Travel Order</th>
                                <th>Allowance</th>
                                <th>Date Started</th>
                                <th>Date Ended</th>
                                <th>Documentation</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- Employee Archive -->
                <div id="archive-section" class="section" style="display:none;">
                    <h2>Archived Employees</h2>
                    <input class="searchbar" type="text" id="searchArchiveInput" placeholder="Search for Employees">
                    <button class="search-btn" onclick="searchEmployee()">Search</button>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Birthdate</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Date Archived</th>
                                <th>Date Hired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include 'archived_employee_table.php'; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Applications -->
                <div id="request-section" class="section" style="display:none;">
                    <h2>Pending Application</h2>

                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Leave Type</th>
                                <th>Points Cost</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'fetch_employee_application.php';
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Settings -->
                <div id="settings-section" class="section" style="display:none;">
                    <h2>Settings</h2>
                    <p>Settings options will be displayed here.</p>
                </div>

                <!-- Manage Leave Section -->
                <div id="manage-leave-section" class="section" style="display:none;">
                    <div class="leave-header" style="display:flex;align-items:center;gap:10px;">
                        <img src="/hrlgu/Pictures/logo.png" alt="icon" style="height:28px;">
                        <h3 style="margin:0;">Leave Types</h3>
                        <button id="openCreateLeaveTypeModal" class="open-modal-btn" style="margin-left:auto;">+ Create
                            Leave Type</button>
                    </div>

                    <table>
    <thead>
        <tr>
            <th>Leave Type</th>
            <th>Description</th>
            <th>Policy Type</th>
            <th>Usage Rule</th> <th style="text-align:right;">Points Cost</th>
            <th style="text-align:right;">Max Days</th>
            <th>Deducted From</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // This script will now correctly print all the 8-column 
            // table rows (<tr>...</tr>) by itself.
            include 'fetch_leave_types_admin.php';
        ?>
        
        </tbody>
</table>
                </div>

                <!-- Manage Travel Order Section -->
                <div id="manage-travel-section" class="section" style="display:none;">
                    <div class="travel-header" style="display:flex;align-items:center;gap:10px;">
                        <img src="/hrlgu/Pictures/logo.png" alt="icon" style="height:28px;">
                        <h3 style="margin:0;">Manage Travel Orders</h3>
                        <button id="openAssignTravelBtn" class="open-modal-btn" style="margin-left:auto;" onclick="openAssignTravelModal()">+ Assign Travel</button>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Control #</th>
                                <th>Employee</th>
                                <th>Destination</th>
                                <th>Purpose</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="manage-travel-tbody">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Ongoing/Upcoming Travels Section -->
                <div id="ongoing-travel-section" class="section" style="display:none;">
                    <div class="travel-header" style="display:flex;align-items:center;gap:10px;">
                        <img src="/hrlgu/Pictures/logo.png" alt="icon" style="height:28px;">
                        <h3 style="margin:0;">Ongoing/Upcoming Travels</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Control #</th>
                                <th>Employee</th>
                                <th>Destination</th>
                                <th>Purpose</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ongoing-travel-tbody">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Travel History Section -->
                <div id="travel-history-section" class="section" style="display:none;">
                    <div class="travel-header" style="display:flex;align-items:center;gap:10px;">
                        <img src="/hrlgu/Pictures/logo.png" alt="icon" style="height:28px;">
                        <h3 style="margin:0;">Travel History</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Control #</th>
                                <th>Employee</th>
                                <th>Destination</th>
                                <th>Purpose</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="travel-history-tbody">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div id="manage-department-section" class="section" style="display:none;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                        <div style="background: #0052CC; padding: 12px 16px; border-radius: 10px;">
                            <img src="/hrlgu/Pictures/icons/department.png" alt="Department" style="height: 28px; width: 28px; filter: brightness(0) invert(1);">
                        </div>
                        <h2 style="margin: 0; font-size: 28px; font-weight: 700; color: #333;">Departments</h2>
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                        <button class="add-btn" onclick="openAddDepartmentForm()" style="background: #0052CC; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">+ Add Department</button>
                        <input class="searchbar" type="text" id="searchDepartmentInput" placeholder="Search departments..." style="flex: 1; min-width: 250px; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <button class="search-btn" onclick="searchDepartment()" style="background: #f0f0f0; color: #333; padding: 12px 24px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Search</button>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                        <thead>
                            <tr style="background: #0052CC; color: white;">
                                <th style="padding: 16px; text-align: left; font-weight: 600;">ID</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600;">Department Name</th>
                                <th style="padding: 16px; text-align: center; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include 'department_table.php'; ?>
                        </tbody>
                    </table>
                </div>

                <div id="manage-position-section" class="section" style="display:none;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                        <div style="background: #0052CC; padding: 12px 16px; border-radius: 10px;">
                            <img src="/hrlgu/Pictures/icons/position.png" alt="Position" style="height: 28px; width: 28px; filter: brightness(0) invert(1);">
                        </div>
                        <h2 style="margin: 0; font-size: 28px; font-weight: 700; color: #333;">Positions</h2>
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                        <button class="add-btn" onclick="openAddPositionForm()" style="background: #0052CC; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">+ Add Position</button>
                        <input class="searchbar" type="text" id="searchPositionInput" placeholder="Search positions..." style="flex: 1; min-width: 250px; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#f5576c'; this.style.boxShadow='0 0 0 3px rgba(245, 87, 108, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <button class="search-btn" onclick="searchPosition()" style="background: #f0f0f0; color: #333; padding: 12px 24px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Search</button>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                        <thead>
                            <tr style="background: #0052CC; color: white;">
                                <th style="padding: 16px; text-align: left; font-weight: 600;">ID</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600;">Position Name</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600;">Department</th>
                                <th style="padding: 16px; text-align: center; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include 'position_table.php'; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Employee Profile Modal -->
    <div id="viewEmployeeModal" class="modal">
        <div class="modal-content" style="max-width:700px;">
            <span class="close" id="closeViewEmployeeModal">&times;</span>

            <div id="employeeProfileTop" style="display:flex;align-items:center;gap:20px;">
                <img id="employeeProfilePhoto" src="" alt="Profile Photo"
                    style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:2px solid #ccc;">
                <div>
                    <h2 id="viewEmployeeName"></h2>
                    <p><b>Age:</b> <span id="viewEmployeeAge"></span></p>
                    <p><b>Contact:</b> <span id="viewEmployeeContact"></span></p>
                    <p><b>Email:</b> <span id="viewEmployeeEmail"></span></p>
                    <p><b>Department:</b> <span id="viewEmployeeDept"></span></p>
                    <p><b>Position:</b> <span id="viewEmployeePosition"></span></p>
                    <p><b>Status:</b> <span id="viewEmployeeStatus"></span></p>
                    <p><b>Date Hired:</b> <span id="viewEmployeeDateHired"></span></p>
                </div>
            </div>
            <hr>

            <div style="display:flex;gap:20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                <button id="employeeStatsBtn" class="modal-tab-btn active" style="flex:1;">Statistics</button>
                <button id="employeeFilesBtn" class="modal-tab-btn" style="flex:1;">Files</button>
            </div>

            <div id="employeeStatsSection" class="modal-tab-content" style="margin-top:20px;">

                <div style="background-color: #f4f4f4; padding: 15px; border-radius: 8px; text-align: center;">
                    <h3 style="margin-top: 0;">Total Leave Monetization Value</h3>
                    <p style="margin: 5px 0 10px 0;">Estimated value of all earned VL/SL credits.</p>
                    <h2 id="employeeMonetizationValue" style="color:#28a745; margin: 0;">
                        Loading...
                    </h2>
                </div>

                <h3 style="margin-top: 20px;">Leave Balances</h3>
                <p style="font-size: 0.9em; color: #666;">Showing balances for leave types that have their own credit
                    pool.</p>
                <table style="width:100%;border-collapse:collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f9f9f9;">
                            <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Leave Type</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Available</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Earned/Credited</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Taken</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Planned</th>
                        </tr>
                    </thead>
                    <tbody id="employeeBalanceTableBody">
                        <tr>
                            <td colspan="5"
                                style="padding: 20px; text-align: center; color: #888; border: 1px solid #ddd;">
                                Loading leave balances...
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h3 style="margin-top: 20px;">Leave Application History</h3>
                <div style="width: 100%; max-width: 500px; margin: 20px auto 0 auto;">
                    <canvas id="employeeLeaveChart"></canvas>
                </div>
            </div>
            <div id="employeeFilesSection" class="modal-tab-content" style="margin-top:20px;display:none;">
                <h3>Employee Files</h3>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody id="employeeFilesTableBody">
                        <tr>
                            <td colspan="2" style="padding: 20px; text-align: center; color: #888;">
                                Loading files...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Employee Wizard Modal -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content" style="max-width: 800px; padding: 0; border-radius: 16px; overflow: hidden;">
            <!-- Wizard Header -->
            <div style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); padding: 30px; color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: 600;">Add New Employee</h2>
                    <span class="close" style="color: white; font-size: 28px; cursor: pointer;">&times;</span>
                </div>
                <!-- Progress Steps -->
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div class="wizard-step active" data-step="1" style="flex: 1; text-align: center; padding: 10px; background: rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer; font-weight: 600;">Step 1: Personal</div>
                    <div style="color: rgba(255,255,255,0.5);">→</div>
                    <div class="wizard-step" data-step="2" style="flex: 1; text-align: center; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 8px; cursor: pointer; font-weight: 600;">Step 2: Employment</div>
                    <div style="color: rgba(255,255,255,0.5);">→</div>
                    <div class="wizard-step" data-step="3" style="flex: 1; text-align: center; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 8px; cursor: pointer; font-weight: 600;">Step 3: Documents</div>
                </div>
            </div>

            <!-- Wizard Form -->
            <form id="addEmployeeWizardForm" action="add_employee.php" method="POST" enctype="multipart/form-data" novalidate style="padding: 30px;">
                <!-- Step 1: Personal Information -->
                <div class="wizard-content active" data-step="1" style="display: block;">
                    <h3 style="color: #0052CC; margin-top: 0; margin-bottom: 20px;">Personal Information</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">First Name <span style="color: #dc3545;">*</span></label>
                            <input type="text" name="first_name" maxlength="50" required pattern="[A-Za-z\s\-]+" placeholder="Enter first name" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Middle Name</label>
                            <input type="text" name="middle_name" maxlength="50" placeholder="Enter middle name" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Last Name <span style="color: #dc3545;">*</span></label>
                            <input type="text" name="last_name" maxlength="50" required pattern="[A-Za-z\s\-]+" placeholder="Enter last name" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Sex</label>
                            <select name="sex" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Email <span style="color: #dc3545;">*</span></label>
                            <input type="email" name="email" required placeholder="Enter email" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Password <span style="color: #dc3545;">*</span></label>
                            <input type="password" name="password" required placeholder="Enter password" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Contact No <span style="color: #dc3545;">*</span></label>
                            <input type="text" name="contact_no" pattern="^09\d{9}$" required maxlength="11" placeholder="09123456789" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;" inputmode="numeric">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Emergency Contact <span style="color: #dc3545;">*</span></label>
                            <input type="text" name="emergency_contact_no" pattern="^09\d{9}$" required maxlength="11" placeholder="09123456789" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;" inputmode="numeric">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Birthdate <span style="color: #dc3545;">*</span></label>
                            <input type="date" name="birthdate" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Home Address <span style="color: #dc3545;">*</span></label>
                            <input type="text" name="homeaddress" required placeholder="Enter address" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Employment Information -->
                <div class="wizard-content" data-step="2" style="display: none;">
                    <h3 style="color: #0052CC; margin-top: 0; margin-bottom: 20px;">Employment Information</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Department <span style="color: #dc3545;">*</span></label>
                            <select name="department" id="department-select-wizard" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                                <option value="">Select Department</option>
                                <?php
                                include 'db_connect.php';
                                $departments = [];
                                $deptResult = $conn->query("SELECT DepartmentID, DepartmentName FROM Department");
                                while ($row = $deptResult->fetch_assoc()) {
                                    $departments[] = $row;
                                }
                                foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['DepartmentID'] ?>">
                                        <?= htmlspecialchars($dept['DepartmentName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Position <span style="color: #dc3545;">*</span></label>
                            <select name="position" id="position-select-wizard" required disabled style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                                <option value="">Select Position</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Date Hired <span style="color: #dc3545;">*</span></label>
                            <input type="date" name="date_hired" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Status <span style="color: #dc3545;">*</span></label>
                            <select name="status" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                                <option value="">Select Status</option>
                                <option value="Full Time">Full Time</option>
                                <option value="Part Time">Part Time</option>
                                <option value="Contract Worker">Contract Worker</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Required Documents -->
                <div class="wizard-content" data-step="3" style="display: none;">
                    <h3 style="color: #0052CC; margin-top: 0; margin-bottom: 20px;">Required Documents</h3>
                    <p style="color: #666; font-size: 13px; margin-bottom: 20px;">⚠️ Make sure files are readable and in proper format</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Resume <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="resume" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Government ID <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="govtid" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Local Certificate <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="localcert" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">PSA Birth Certificate <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="psa_birth" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">NBI Clearance <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="nbi_clearance" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Police Clearance <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="police_clearance" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Personal Data Sheet <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="pds" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Valid Government ID <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="gov_id" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Diploma</label>
                            <input type="file" name="diploma" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                        <div>
                            <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Medical Certificate</label>
                            <input type="file" name="medical_cert" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div style="display: flex; gap: 12px; justify-content: space-between; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    <button type="button" id="prevBtn" onclick="changeStep(-1)" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; display: none;">← Previous</button>
                    <div style="flex: 1;"></div>
                    <button type="button" id="nextBtn" onclick="changeStep(1)" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px;">Next →</button>
                    <button type="submit" id="submitBtn" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; display: none;">✓ Submit</button>
                </div>
            </form>
        </div>
    </div>
    <div id="editEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Edit Employee</h3>
            <form id="editEmployeeForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editEmployeeId" name="EmployeeID">

                <!-- Read-only Info -->
                <fieldset>
                    <legend>Basic Information</legend>
                    <div class="form-row">
                        <div>
                            <label>ID</label>
                            <input type="text" id="editEmployeeIDLabel" disabled>
                        </div>
                        <div>
                            <label>Full Name</label>
                            <input type="text" id="editFullName" disabled>
                        </div>
                    </div>
                </fieldset>

                <!-- Editable Department & Position -->
                <fieldset>
                    <legend>Work Information</legend>
                    <div class="form-row">
                        <div>
                            <label>Department</label>
                            <select name="department" id="editDepartmentSelect" required>
                                <option value="">Select Department</option>
                                <?php
                                include 'db_connect.php';
                                $deptResult = $conn->query("SELECT DepartmentID, DepartmentName FROM Department");
                                while ($row = $deptResult->fetch_assoc()) {
                                    echo '<option value="' . $row['DepartmentID'] . '">' . htmlspecialchars($row['DepartmentName']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label>Position</label>
                            <select name="position" id="editPositionSelect" required disabled>
                                <option value="">Select Position</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <!-- Documents -->
                <fieldset>
                    <legend>Required Documents</legend>
                    <legend>Make sure the file or picture is readable.</legend>

                    <div class="form-row">
                        <div>
                            <label id="resumeLabel">Resume *Required*</label>
                            <input type="file" name="resume">
                            <span id="resumeCurrent"></span>
                        </div>
                        <div>
                            <label id="diplomaLabel">Diploma</label>
                            <input type="file" name="diploma">
                            <span id="diplomaCurrent"></span>
                        </div>
                        <div>
                            <label id="govtIdLabel">Government ID *Required*</label>
                            <input type="file" name="govtid">
                            <span id="govtIdCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="localCertLabel">Local Certificate *Required*</label>
                            <input type="file" name="localcert">
                            <span id="localCertCurrent"></span>
                        </div>
                        <div>
                            <label id="cseLabel">Civil Service Eligibility</label>
                            <input type="file" name="cse">
                            <span id="cseCurrent"></span>
                        </div>
                        <div>
                            <label id="psaBirthLabel">PSA Birth Certificate *Required*</label>
                            <input type="file" name="psa_birth">
                            <span id="psaBirthCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="psaMarriageLabel">PSA Marriage Certificate</label>
                            <input type="file" name="psa_marriage">
                            <span id="psaMarriageCurrent"></span>
                        </div>
                        <div>
                            <label id="nbiClearanceLabel">NBI Clearance *Required*</label>
                            <input type="file" name="nbi_clearance">
                            <span id="nbiClearanceCurrent"></span>
                        </div>
                        <div>
                            <label id="policeClearanceLabel">Police Clearance *Required*</label>
                            <input type="file" name="police_clearance">
                            <span id="policeClearanceCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="medicalCertLabel">Medical Certificate</label>
                            <input type="file" name="medical_cert">
                            <span id="medicalCertCurrent"></span>
                        </div>
                        <div>
                            <label id="pdsLabel">Personal Data Sheet *Required*</label>
                            <input type="file" name="pds">
                            <span id="pdsCurrent"></span>
                        </div>
                        <div>
                            <label id="govIdLabel">Valid Government ID *Required*</label>
                            <input type="file" name="gov_id">
                            <span id="govIdCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="prcLicenseLabel">PRC License</label>
                            <input type="file" name="prc_license">
                            <span id="prcLicenseCurrent"></span>
                        </div>
                        <div>
                            <label id="torLabel">Transcript of Records</label>
                            <input type="file" name="tor">
                            <span id="torCurrent"></span>
                        </div>
                        <div>
                            <label id="certTrainingLabel">Certificates of Training</label>
                            <input type="file" name="cert_training">
                            <span id="certTrainingCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="serviceRecordLabel">Service Record</label>
                            <input type="file" name="service_record">
                            <span id="serviceRecordCurrent"></span>
                        </div>
                        <div>
                            <label id="performanceRatingLabel">Performance Rating</label>
                            <input type="file" name="performance_rating">
                            <span id="performanceRatingCurrent"></span>
                        </div>
                        <div>
                            <label id="honorGradLabel">Honor Graduate Eligibility</label>
                            <input type="file" name="honor_grad">
                            <span id="honorGradCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="tinLabel">TIN</label>
                            <input type="file" name="tin">
                            <span id="tinCurrent"></span>
                        </div>
                        <div>
                            <label id="sssLabel">SSS</label>
                            <input type="file" name="sss">
                            <span id="sssCurrent"></span>
                        </div>
                        <div>
                            <label id="pagibigLabel">Pag-IBIG</label>
                            <input type="file" name="pagibig">
                            <span id="pagibigCurrent"></span>
                        </div>
                        <div>
                            <label id="philhealthLabel">PhilHealth</label>
                            <input type="file" name="philhealth">
                            <span id="philhealthCurrent"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label id="otherDocsLabel">Other Documents</label>
                            <input type="file" name="other_docs" multiple>
                            <span id="otherDocsCurrent"></span>
                        </div>
                    </div>
                </fieldset>

                <!-- Editable Status -->
                <fieldset>
                    <legend>Status</legend>
                    <div class="form-row">
                        <div>
                            <label>Status</label>
                            <select name="status" id="editStatus">
                                <option value="Full Time">Full Time</option>
                                <option value="Part Time">Part Time</option>
                                <option value="Contract Worker">Contract Worker</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="deleteEmployeeForm" method="post" action="delete_employee.php">
                <input type="hidden" id="deleteEmployeeId" name="EmployeeID">
                <p>Are you sure you want to delete this employee?</p>
                <button type="submit">Yes, Delete</button>
            </form>
        </div>
    </div>

    <div id="leaveTypeModal" class="modal">
    <div class="modal-content" style="max-width: 700px; padding: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 82, 204, 0.15);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h2 id="leaveTypeModalTitle" style="margin: 0; font-size: 24px; font-weight: 600;">Create Leave Type</h2>
            <span class="close-modal" id="closeLeaveTypeModal" style="color: white; font-size: 28px; cursor: pointer; font-weight: 300;">&times;</span>
        </div>

        <!-- Modal Body -->
        <form id="leaveTypeForm" style="padding: 30px;">
            <input type="hidden" name="LeaveTypeID" id="LeaveTypeID" value="">
            
            <!-- Type Name -->
            <div style="margin-bottom: 20px;">
                <label for="TypeName" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Type Name <span style="color: #dc3545;">*</span></label>
                <input type="text" id="TypeName" name="TypeName" required placeholder="e.g., Sick Leave" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
            </div>

            <!-- Leave Policy Type -->
            <div style="margin-bottom: 20px;">
                <label for="UnitType" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Leave Policy Type</label>
                <select id="UnitType" name="UnitType" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; background-color: white; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    <option value="Leave Credit" selected>Leave Credit (Balance-based)</option>
                    <option value="Fixed Days">Fixed Days (Entitlement-based)</option>
                </select>
            </div>

            <!-- Fixed Days Config -->
            <div id="fixedDaysConfigRow" style="display:none; margin-bottom: 20px;">
                <div style="margin-bottom: 15px;">
                    <label for="FixedDays" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Max Entitlement Days</label>
                    <input type="number" id="FixedDays" name="FixedDays" min="1" max="365" value="7" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>

                <div id="usageFrequencyRow">
                    <label for="UsageFrequency" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Usage Frequency</label>
                    <select id="UsageFrequency" name="UsageFrequency" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; background-color: white; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <option value="PerEvent">Per Event (e.g., Paternity, Calamity)</option>
                        <option value="PerYear" selected>Per Year (e.g., Special Privilege Leave)</option>
                    </select>
                </div>
            </div>

            <!-- Credit Leave Config -->
            <div id="creditLeaveConfigRow" style="margin-bottom: 20px;">
                <div id="pointCostRow" style="margin-bottom: 15px;">
                    <label for="PointCost" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Point Cost</label>
                    <input type="number" id="PointCost" name="PointCost" min="0" step="1" value="0" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>
                <div id="deductFromRow">
                    <label for="DeductFromLeaveTypeID" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Deduct from Balance of:</label>
                    <select id="DeductFromLeaveTypeID" name="DeductFromLeaveTypeID" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; background-color: white; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <option value="" selected>New Balance</option>
                        <?php
                        if (isset($leaveTypes) && is_array($leaveTypes)) {
                            foreach ($leaveTypes as $typeID => $type) {
                                if ($type['UnitType'] == 'Leave Credit') {
                                    echo '<option value="' . htmlspecialchars($typeID) . '">' . htmlspecialchars($type['TypeName']) . '</option>';
                                }
                            }
                        }
                        ?>
                        <option>None</option>
                    </select>
                    <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">e.g., Make "Emergency Leave" deduct from the "Vacation Leave" balance.</small>
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label for="Description" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Description</label>
                <textarea id="Description" name="Description" rows="3" placeholder="Enter leave type description" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; font-family: inherit; transition: all 0.3s ease; resize: vertical;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"></textarea>
            </div>

            <!-- Allow Documents -->
            <div style="margin-bottom: 20px;">
                <label for="AllowDocuments" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Allow Documents</label>
                <select id="AllowDocuments" name="AllowDocuments" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; background-color: white; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    <option value="0" selected>No</option>
                    <option value="1">Yes</option>
                </select>
            </div>

            <!-- Max Documents -->
            <div id="maxDocumentsRow" style="display:none; margin-bottom: 20px;">
                <label for="MaxDocuments" style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px;">Max Documents (1-10)</label>
                <input type="number" id="MaxDocuments" name="MaxDocuments" min="1" max="10" value="1" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
            </div>
        </form>

        <!-- Modal Footer -->
        <div style="display: flex; gap: 12px; justify-content: flex-end; padding: 20px 30px; border-top: 1px solid #e0e0e0; background: rgba(0, 82, 204, 0.02);">
            <button type="button" id="cancelLeaveTypeBtn" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'; this.style.borderColor='#999'" onmouseout="this.style.background='#f0f0f0'; this.style.borderColor='#e0e0e0'">Cancel</button>
            <button type="button" id="saveLeaveTypeBtn" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">Save</button>
        </div>
    </div>
</div>
    <!-- Add Department Modal -->
    <div id="addDepartmentModal" class="modal" style="animation: fadeIn 0.3s ease;">
        <div class="modal-content" style="max-width: 500px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
            <div style="background: #0052CC; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">Add Department</h2>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Create a new department</p>
                </div>
                <span class="close" id="closeAddDepartmentModal" style="color: white; font-size: 32px; cursor: pointer; background: rgba(255, 255, 255, 0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</span>
            </div>
            <form id="addDepartmentForm" method="POST" action="add_department.php" style="padding: 30px;">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">🏢 Department Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" name="department_name" required pattern="^[A-Za-z ]+$" title="Only letters and spaces are allowed" placeholder="e.g., Human Resources" style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 12px; border-top: 1px solid #e8e8e8;">
                    <button type="button" onclick="document.getElementById('closeAddDepartmentModal').click()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Cancel</button>
                    <button type="submit" style="background: #0052CC; color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">✓ Add Department</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Department Modal -->
    <div id="editDepartmentModal" class="modal" style="animation: fadeIn 0.3s ease;">
        <div class="modal-content" style="max-width: 500px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
            <div style="background: #0052CC; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">Edit Department</h2>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Update department information</p>
                </div>
                <span class="close" id="closeEditDepartmentModal" style="color: white; font-size: 32px; cursor: pointer; background: rgba(255, 255, 255, 0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</span>
            </div>
            <form id="editDepartmentForm" method="POST" action="edit_department.php" style="padding: 30px;">
                <input type="hidden" name="department_id" id="editDepartmentId">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">🏢 Department Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" name="department_name" id="editDepartmentName" required pattern="^[A-Za-z ]+$" title="Only letters and spaces are allowed" placeholder="e.g., Human Resources" style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 12px; border-top: 1px solid #e8e8e8;">
                    <button type="button" onclick="document.getElementById('closeEditDepartmentModal').click()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Cancel</button>
                    <button type="submit" style="background: #0052CC; color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">✓ Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Department Modal -->
    <div id="deleteDepartmentModal" class="modal" style="animation: fadeIn 0.3s ease;">
        <div class="modal-content" style="max-width: 450px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
            <div style="background: #dc3545; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">Delete Department</h2>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">This action cannot be undone</p>
                </div>
                <span class="close" id="closeDeleteDepartmentModal" style="color: white; font-size: 32px; cursor: pointer; background: rgba(255, 255, 255, 0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</span>
            </div>
            <form id="deleteDepartmentForm" method="POST" action="delete_department.php" style="padding: 30px;">
                <input type="hidden" name="department_id" id="deleteDepartmentId">
                <p style="font-size: 16px; color: #333; margin-bottom: 24px;">Are you sure you want to delete this department? This action cannot be undone.</p>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 12px; border-top: 1px solid #e8e8e8;">
                    <button type="button" onclick="document.getElementById('closeDeleteDepartmentModal').click()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Cancel</button>
                    <button type="submit" style="background: #dc3545; color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(220, 53, 69, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(220, 53, 69, 0.3)'">🗑️ Delete Department</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Position Modal -->
    <div id="addPositionModal" class="modal" style="animation: fadeIn 0.3s ease;">
        <div class="modal-content" style="max-width: 500px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
            <div style="background: #0052CC; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">Add Position</h2>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Create a new position</p>
                </div>
                <span class="close" id="closeAddPositionModal" style="color: white; font-size: 32px; cursor: pointer; background: rgba(255, 255, 255, 0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</span>
            </div>
            <form id="addPositionForm" method="POST" action="add_position.php" style="padding: 30px;">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">🏢 Department <span style="color: #dc3545;">*</span></label>
                    <select name="department_id" id="addPositionDepartment" required style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; background-color: white; transition: all 0.3s ease; cursor: pointer;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <option value="">-- Select Department --</option>
                        <?php
                        include 'db_connect.php';
                        $deptResult = $conn->query("SELECT DepartmentID, DepartmentName FROM Department");
                        while ($row = $deptResult->fetch_assoc()) {
                            echo '<option value="' . $row['DepartmentID'] . '">' . htmlspecialchars($row['DepartmentName']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">👔 Position Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" name="position_name" required pattern="^[A-Za-z ]+$" title="Only letters and spaces are allowed" placeholder="e.g., Manager, Developer" style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 12px; border-top: 1px solid #e8e8e8;">
                    <button type="button" onclick="document.getElementById('closeAddPositionModal').click()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Cancel</button>
                    <button type="submit" style="background: #0052CC; color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">✓ Add Position</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Position Modal -->
    <div id="editPositionModal" class="modal" style="animation: fadeIn 0.3s ease;">
        <div class="modal-content" style="max-width: 500px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
            <div style="background: #0052CC; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">Edit Position</h2>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Update position information</p>
                </div>
                <span class="close" id="closeEditPositionModal" style="color: white; font-size: 32px; cursor: pointer; background: rgba(255, 255, 255, 0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</span>
            </div>
            <form id="editPositionForm" method="POST" action="edit_position.php" style="padding: 30px;">
                <input type="hidden" name="position_id" id="editPositionId">
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">🏢 Department <span style="color: #dc3545;">*</span></label>
                    <select name="department_id" id="editPositionDepartment" required style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; background-color: white; transition: all 0.3s ease; cursor: pointer;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <option value="">-- Select Department --</option>
                        <?php
                        $deptResult = $conn->query("SELECT DepartmentID, DepartmentName FROM Department");
                        while ($row = $deptResult->fetch_assoc()) {
                            echo '<option value="' . $row['DepartmentID'] . '">' . htmlspecialchars($row['DepartmentName']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">👔 Position Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" name="position_name" id="editPositionName" required pattern="^[A-Za-z ]+$" title="Only letters and spaces are allowed" placeholder="e.g., Manager, Developer" style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 12px; border-top: 1px solid #e8e8e8;">
                    <button type="button" onclick="document.getElementById('closeEditPositionModal').click()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Cancel</button>
                    <button type="submit" style="background: #0052CC; color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">✓ Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Position Modal -->
    <div id="deletePositionModal" class="modal" style="animation: fadeIn 0.3s ease;">
        <div class="modal-content" style="max-width: 450px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
            <div style="background: #dc3545; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700;">Delete Position</h2>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">This action cannot be undone</p>
                </div>
                <span class="close" id="closeDeletePositionModal" style="color: white; font-size: 32px; cursor: pointer; background: rgba(255, 255, 255, 0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</span>
            </div>
            <form id="deletePositionForm" method="POST" action="delete_position.php" style="padding: 30px;">
                <input type="hidden" name="position_id" id="deletePositionId">
                <p style="font-size: 16px; color: #333; margin-bottom: 24px;">Are you sure you want to delete this position? This action cannot be undone.</p>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 12px; border-top: 1px solid #e8e8e8;">
                    <button type="button" onclick="document.getElementById('closeDeletePositionModal').click()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'" onmouseout="this.style.background='#f0f0f0'">Cancel</button>
                    <button type="submit" style="background: #dc3545; color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(220, 53, 69, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(220, 53, 69, 0.3)'">🗑️ Delete Position</button>
                </div>
            </form>
        </div>
    </div>
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
            background-color: #ffffff;
            font-size: 9px;
            color: #000;
        }
        .cs-credits-table tbody th {
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            background-color: #ffffff;
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
            
            <img src="/hrlgu/Pictures/logo.png" 
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

<!-- View Travel Order Details Modal -->
<div id="viewtravelDetailsModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 900px; padding: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 82, 204, 0.15);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); padding: 20px 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 600;">Travel Order Details</h2>
            <span class="close-view-modal" id="closeTravelDetailsModal" style="color: white; font-size: 28px; cursor: pointer; font-weight: 300;">&times;</span>
        </div>

        <!-- Modal Body - Content Loaded via AJAX -->
        <div id="travelDetailsContent" style="padding: 30px; max-height: 70vh; overflow-y: auto;">
            <p style="text-align: center; color: #999;">Loading travel order details...</p>
        </div>

        <!-- Modal Footer -->
        <div style="display: flex; gap: 12px; justify-content: flex-end; padding: 20px 30px; border-top: 1px solid #e0e0e0; background: rgba(0, 82, 204, 0.02);">
            <button type="button" id="downloadTravelPdfBtn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(40, 167, 69, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(40, 167, 69, 0.3)'">
                <i class="fas fa-download"></i> Download PDF
            </button>
            <button type="button" id="cancelTravelDetailsBtn" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'; this.style.borderColor='#999'" onmouseout="this.style.background='#f0f0f0'; this.style.borderColor='#e0e0e0'">Close</button>
        </div>
    </div>
</div>

<!-- Assign Travel Modal -->
<div id="assignTravelModal" style="display: none !important; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 9999; justify-content: center; align-items: center; animation: fadeIn 0.3s ease;">
    <div style="background: white; width: 95%; max-width: 650px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: slideUp 0.3s ease;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 26px; font-weight: 700;">Assign Travel Order</h2>
                <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Create a new travel directive for an employee</p>
            </div>
            <button type="button" onclick="closeAssignTravelModal()" style="background: rgba(255, 255, 255, 0.2); border: none; color: white; font-size: 32px; cursor: pointer; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</button>
        </div>

        <!-- Form -->
        <form id="assignTravelForm" style="padding: 30px;">
            <!-- Employee -->
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">👤 Select Employee <span style="color: #dc3545;">*</span></label>
                <select id="assignTravelEmployee" required style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; background-color: white; transition: all 0.3s ease; cursor: pointer;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    <option value="">-- Select an Employee --</option>
                </select>
            </div>

            <!-- Destination -->
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">📍 Destination <span style="color: #dc3545;">*</span></label>
                <input type="text" id="assignTravelDestination" required placeholder="e.g., Manila, Cebu, Bangkok" style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
            </div>

            <!-- Purpose -->
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">📝 Purpose <span style="color: #dc3545;">*</span></label>
                <textarea id="assignTravelPurpose" required placeholder="Enter the purpose of travel (e.g., Conference, Training, Meeting)" rows="3" style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; font-family: inherit; resize: vertical; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"></textarea>
            </div>

            <!-- Dates Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <!-- Start Date -->
                <div style="margin-bottom: 22px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">📅 Start Date <span style="color: #dc3545;">*</span></label>
                    <input type="date" id="assignTravelStartDate" required style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>

                <!-- End Date -->
                <div style="margin-bottom: 22px;">
                    <label style="display: block; font-weight: 700; color: #0052CC; margin-bottom: 8px; font-size: 15px;">📅 End Date <span style="color: #dc3545;">*</span></label>
                    <input type="date" id="assignTravelEndDate" required style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                </div>
            </div>
        </form>

        <!-- Footer -->
        <div style="display: flex; gap: 12px; justify-content: flex-end; padding: 24px 30px; border-top: 1px solid #e8e8e8; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
            <button type="button" onclick="closeAssignTravelModal()" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'; this.style.borderColor='#999'" onmouseout="this.style.background='#f0f0f0'; this.style.borderColor='#e0e0e0'">Cancel</button>
            <button type="button" onclick="submitAssignTravel()" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">✓ Assign Travel</button>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ===== ASSIGN TRAVEL MODAL FUNCTIONS =====
    // Close Assign Travel modal
    function closeAssignTravelModal() {
        const modal = document.getElementById('assignTravelModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Open Assign Travel modal
    function openAssignTravelModal() {
        console.log('Opening assign travel modal');
        const modal = document.getElementById('assignTravelModal');
        const form = document.getElementById('assignTravelForm');
        
        console.log('Modal element:', modal);
        
        if (form) form.reset();
        if (typeof loadEmployeesForAssignTravel === 'function') {
            loadEmployeesForAssignTravel();
        }
        
        if (modal) {
            // Remove all display-related styles and set flex
            modal.style.removeProperty('display');
            modal.style.display = 'flex !important';
            modal.style.visibility = 'visible';
            modal.style.opacity = '1';
            console.log('Modal display set to flex');
            console.log('Modal computed style:', window.getComputedStyle(modal).display);
        } else {
            console.error('Modal not found!');
        }
    }

    // Submit Assign Travel form
    function submitAssignTravel() {
        const employeeId = document.getElementById('assignTravelEmployee').value;
        const destination = document.getElementById('assignTravelDestination').value;
        const purpose = document.getElementById('assignTravelPurpose').value;
        const startDate = document.getElementById('assignTravelStartDate').value;
        const endDate = document.getElementById('assignTravelEndDate').value;

        // Validation
        if (!employeeId || !destination || !purpose || !startDate || !endDate) {
            Swal.fire('Error', 'Please fill in all fields', 'error');
            return;
        }

        // Submit
        const formData = new FormData();
        formData.append('employee_id', employeeId);
        formData.append('destination', destination);
        formData.append('purpose', purpose);
        formData.append('start_date', startDate);
        formData.append('end_date', endDate);

        fetch('assign_travel.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success!', 'Travel order assigned successfully.', 'success').then(() => {
                    closeAssignTravelModal();
                    if (typeof loadManageTravelOrders === 'function') {
                        loadManageTravelOrders();
                    }
                });
            } else {
                Swal.fire('Error', data.error || 'Failed to assign travel', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'An error occurred', 'error');
        });
    }

    // ===== GLOBAL SEARCH FUNCTIONS (defined before DOMContentLoaded) =====
    // These must be defined early so they can be called from HTML onclick handlers
    
    function searchEmployee() {
        const searchInput = document.getElementById('searchEmployeeInput');
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#employees-section tbody tr');
        
        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            if (nameCell) {
                const name = nameCell.textContent.toLowerCase();
                if (searchTerm === '' || name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    function searchDepartment() {
        const searchInput = document.getElementById('searchDepartmentInput');
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#manage-department-section tbody tr');
        
        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            if (nameCell) {
                const name = nameCell.textContent.toLowerCase();
                if (searchTerm === '' || name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    function searchPosition() {
        const searchInput = document.getElementById('searchPositionInput');
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#manage-position-section tbody tr');
        
        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            if (nameCell) {
                const name = nameCell.textContent.toLowerCase();
                if (searchTerm === '' || name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Function to filter positions by selected department
    function filterPositionsByDepartment() {
        const departmentSelect = document.getElementById('department-select-wizard');
        const positionSelect = document.getElementById('position-select-wizard');
        
        if (!departmentSelect || !positionSelect) return;
        
        const deptId = departmentSelect.value;
        
        if (deptId) {
            fetch(`get_positions.php?department_id=${deptId}`)
                .then(res => res.json())
                .then(data => {
                    positionSelect.innerHTML = '<option value="">Select Position</option>';
                    data.forEach(pos => {
                        const option = document.createElement('option');
                        option.value = pos.PositionID;
                        option.textContent = pos.PositionName;
                        positionSelect.appendChild(option);
                    });
                    positionSelect.disabled = false;
                })
                .catch(err => {
                    console.error('Error fetching positions:', err);
                    positionSelect.innerHTML = '<option value="">Error loading positions</option>';
                });
        } else {
            positionSelect.disabled = true;
            positionSelect.innerHTML = '<option value="">Select Position</option>';
        }
    }

    // Handle department change to load positions
    document.addEventListener('change', function(e) {
        if (e.target.id === 'department-select-wizard') {
            filterPositionsByDepartment();
        }
    });

    // ===== LOGOUT HANDLER =====
    function handleLogout(event) {
        event.preventDefault();
        event.stopPropagation();
        
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to logout page
                window.location.href = '/hrlgu/Pages/Logout.php';
            }
        });
    }

    // ===== GLOBAL MODAL CLOSE FUNCTIONS =====
    // These must be in global scope to be called from onclick handlers
    
    function closeEmployeeModal() {
        const overlay = document.getElementById('employeeModalOverlay');
        if (overlay) {
            overlay.remove();
        }
    }

    function closeLeaveDetailsModal() {
        const detailsModal = document.getElementById('viewLeaveDetailsModal');
        if (detailsModal) {
            detailsModal.style.display = 'none';
        }
    }

    // ===== VIEW LEAVE APPLICATION FROM HISTORY =====
    function viewLeaveApplicationFromHistory(appId) {
        const detailsModal = document.getElementById('viewLeaveDetailsModal');
        if (!detailsModal) {
            console.error('Leave details modal not found');
            return;
        }
        
        // Show modal
        detailsModal.style.display = 'block';
        
        // Set loading state
        document.getElementById('form-department').textContent = 'Loading...';
        document.getElementById('form-name').textContent = 'Loading...';
        document.getElementById('form-date-filing').textContent = 'Loading...';
        document.getElementById('form-position').textContent = 'Loading...';
        document.getElementById('form-salary').textContent = 'Loading...';
        document.getElementById('form-leave-type').innerHTML = '';
        document.getElementById('form-leave-details').innerHTML = '';
        document.getElementById('form-duration').textContent = '';
        document.getElementById('form-dates').textContent = '';
        document.getElementById('form-reason').textContent = '';
        document.getElementById('form-approval-days').textContent = '';
        document.getElementById('form-disapproval-reason').textContent = '';
        document.getElementById('form-recommendation').innerHTML = 
            `<span class="cs-checkbox">[ ]</span> For approval<br>
             <span class="cs-checkbox">[ ]</span> For disapproval due to...`;
        
        // Fetch application details
        fetch('get_application_details_admin.php?id=' + encodeURIComponent(appId))
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    detailsModal.style.display = 'none';
                    return;
                }
                
                // Populate form fields
                document.getElementById('form-department').textContent = data.Department || 'N/A';
                document.getElementById('form-name').textContent = 
                    `${data.LastName || ''}, ${data.FirstName || ''} ${data.MiddleName || ''}`;
                document.getElementById('form-date-filing').textContent = data.DateRequestedFormatted || 'N/A';
                document.getElementById('form-position').textContent = data.Position || 'N/A';
                document.getElementById('form-salary').textContent = data.Salary ? `₱${parseFloat(data.Salary).toLocaleString()}` : 'N/A';
                
                // Leave type
                document.getElementById('form-leave-type').innerHTML = 
                    `<span class="cs-checkbox">[X]</span> ${data.TypeName}`;
                
                // Leave details
                let details = data.Reason;
                if (data.TypeName === 'Sick Leave') {
                    details = `<span class="cs-checkbox">[ ]</span> In Hospital<br>
                             <span class="cs-checkbox">[X]</span> Out Patient: ${data.Reason}`;
                } else if (data.TypeName === 'Vacation Leave') {
                    details = `<span class="cs-checkbox">[X]</span> Within Philippines<br>
                             <span class="cs-checkbox">[ ]</span> Abroad (Specify):`;
                }
                document.getElementById('form-leave-details').innerHTML = details;
                
                // Duration and dates
                document.getElementById('form-duration').textContent = `${data.DurationDays} day(s)`;
                document.getElementById('form-dates').textContent = `${data.StartDate} to ${data.EndDate}`;
                
                // Reason
                document.getElementById('form-reason').textContent = data.Reason || 'No reason provided.';
                
                // Status
                if (data.Status === 'Approved') {
                    document.getElementById('form-recommendation').innerHTML = 
                        `<span class="cs-checkbox">[X]</span> For approval`;
                    document.getElementById('form-approval-days').textContent = `${data.DurationDays} day(s) with pay`;
                } else if (data.Status === 'Rejected') {
                    document.getElementById('form-recommendation').innerHTML = 
                        `<span class="cs-checkbox">[X]</span> For disapproval due to: ${data.RejectionReason || ''}`;
                    document.getElementById('form-disapproval-reason').textContent = data.RejectionReason || 'See 7.B';
                } else {
                    document.getElementById('form-recommendation').innerHTML = 
                        `<span class="cs-checkbox">[ ]</span> For approval<br>
                         <span class="cs-checkbox">[ ]</span> For disapproval due to...`;
                }
            })
            .catch(err => {
                console.error('Error fetching leave details:', err);
                alert('A network error occurred.');
                detailsModal.style.display = 'none';
            });
    }

    // ===== WIZARD NAVIGATION VARIABLES AND FUNCTIONS =====
    let currentStep = 1;
    const totalSteps = 3;

    function changeStep(direction) {
        const form = document.getElementById('addEmployeeWizardForm');
        const currentContent = form.querySelector(`.wizard-content[data-step="${currentStep}"]`);
        
        // Validate current step before moving forward
        if (direction === 1) {
            const inputs = currentContent.querySelectorAll('input[required], select[required]');
            let emptyFields = [];
            let invalidEmail = false;
            let invalidPhoneFields = [];
            
            for (let input of inputs) {
                const value = input.value ? input.value.trim() : '';
                
                if (!value) {
                    emptyFields.push(input.name || input.id || 'Unknown field');
                }
                
                // Email validation
                if (input.type === 'email' && value && !value.includes('@')) {
                    invalidEmail = true;
                }
                
                // Contact number validation (must be exactly 11 digits)
                if ((input.name === 'contact_no' || input.name === 'emergency_contact_no') && value) {
                    const phoneDigits = value.replace(/\D/g, '');
                    if (phoneDigits.length !== 11) {
                        const fieldLabel = input.name === 'contact_no' ? 'Contact No' : 'Emergency Contact No';
                        invalidPhoneFields.push(fieldLabel + ' (must be 11 digits, got ' + phoneDigits.length + ')');
                    }
                }
            }
            
            if (emptyFields.length > 0) {
                Swal.fire({
                    title: 'Missing Fields',
                    html: '<strong>Please fill in all required fields:</strong><br><br>' + emptyFields.map(f => '• ' + f).join('<br>'),
                    icon: 'warning',
                    confirmButtonColor: '#0052CC',
                    confirmButtonText: 'OK'
                });
                console.log('Empty fields:', emptyFields);
                return;
            }
            
            if (invalidEmail) {
                Swal.fire({
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address (e.g., user@example.com)',
                    icon: 'warning',
                    confirmButtonColor: '#0052CC',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            if (invalidPhoneFields.length > 0) {
                Swal.fire({
                    title: 'Invalid Phone Number',
                    html: '<strong>Invalid phone number format:</strong><br><br>' + invalidPhoneFields.map(f => '• ' + f).join('<br>'),
                    icon: 'warning',
                    confirmButtonColor: '#0052CC',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }

        currentStep += direction;

        if (currentStep < 1) currentStep = 1;
        if (currentStep > totalSteps) currentStep = totalSteps;

        updateWizardDisplay();
    }

    function updateWizardDisplay() {
        // Hide all content
        document.querySelectorAll('.wizard-content').forEach(el => {
            el.style.display = 'none';
        });

        // Show current step
        const currentContent = document.querySelector(`.wizard-content[data-step="${currentStep}"]`);
        if (currentContent) {
            currentContent.style.display = 'block';
        }

        // Update step indicators
        document.querySelectorAll('.wizard-step').forEach(step => {
            const stepNum = parseInt(step.getAttribute('data-step'));
            if (stepNum === currentStep) {
                step.style.background = 'rgba(255,255,255,0.2)';
            } else if (stepNum < currentStep) {
                step.style.background = 'rgba(76, 175, 80, 0.3)';
            } else {
                step.style.background = 'rgba(255,255,255,0.1)';
            }
        });

        // Update button visibility
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (currentStep === 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        } else if (currentStep === totalSteps) {
            prevBtn.style.display = 'block';
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
        } else {
            prevBtn.style.display = 'block';
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Clear wizard position dropdown on page load - only populate when department is selected
        const wizardPosSelect = document.getElementById('position-select-wizard');
        if (wizardPosSelect) {
            wizardPosSelect.innerHTML = '<option value="">Select Position</option>';
            wizardPosSelect.disabled = true;
        }
        // FIRST: Hide all modals on page load
        document.querySelectorAll(".modal").forEach(modal => {
            modal.style.display = "none";
        });
        
        // Remove any dynamically created overlays
        document.querySelectorAll('[id$="ModalOverlay"]').forEach(overlay => {
            overlay.remove();
        });
        
        console.log('Page loaded - all modals hidden');
        
        // Initialize: Show dashboard section by default on page load
        const dashboardSection = document.getElementById('dashboard-section');
        if (dashboardSection) {
            dashboardSection.style.display = 'block';
        }
        
        // Set dashboard as active menu item
        const dashboardBtn = document.querySelector('[data-target="dashboard-section"]');
        if (dashboardBtn) {
            document.querySelectorAll('.menu-btn.active, .submenu-btn.active').forEach(el => el.classList.remove('active'));
            dashboardBtn.classList.add('active');
        }
        
        // Load employees table by default
        if (typeof loadEmployees === 'function') {
            loadEmployees();
        }
        
        // ===== DEPARTMENT EDIT/DELETE EVENT LISTENERS =====
        document.addEventListener('click', function(e) {
            // Edit Department
            if (e.target.classList.contains('edit-department-btn')) {
                const deptId = e.target.getAttribute('data-id');
                const deptName = e.target.getAttribute('data-name');
                document.getElementById('editDepartmentId').value = deptId;
                document.getElementById('editDepartmentName').value = deptName;
                document.getElementById('editDepartmentModal').style.display = 'flex';
            }
            
            // Delete Department
            if (e.target.classList.contains('delete-department-btn')) {
                const deptId = e.target.getAttribute('data-id');
                document.getElementById('deleteDepartmentId').value = deptId;
                document.getElementById('deleteDepartmentModal').style.display = 'flex';
            }
            
            // Edit Position
            if (e.target.classList.contains('edit-position-btn')) {
                const posId = e.target.getAttribute('data-id');
                const posName = e.target.getAttribute('data-name');
                const deptId = e.target.getAttribute('data-dept');
                document.getElementById('editPositionId').value = posId;
                document.getElementById('editPositionName').value = posName;
                document.getElementById('editPositionDepartment').value = deptId;
                document.getElementById('editPositionModal').style.display = 'flex';
            }
            
            // Delete Position
            if (e.target.classList.contains('delete-position-btn')) {
                const posId = e.target.getAttribute('data-id');
                document.getElementById('deletePositionId').value = posId;
                document.getElementById('deletePositionModal').style.display = 'flex';
            }
        });
        
        // Dashboard data
        fetch('fetch_dashboard_data.php')
            .then(res => res.json())
            .then(data => {
                try {
                    document.getElementById('requestedBox').innerText = (data.statusData && data.statusData.Pending) ?? 0;
                    document.getElementById('approvedBox').innerText = (data.statusData && data.statusData.Approved) ?? 0;
                    document.getElementById('rejectedBox').innerText = (data.statusData && data.statusData.Rejected) ?? 0;

                    // Status Chart (Bar)
                    if (document.getElementById('statusChart')) {
                        new Chart(document.getElementById('statusChart'), {
                            type: 'bar',
                            data: {
                                labels: ['Requested', 'Approved', 'Rejected'],
                                datasets: [{
                                    label: 'Leave Applications',
                                    data: [
                                        (data.statusData && data.statusData.Pending) ?? 0,
                                        (data.statusData && data.statusData.Approved) ?? 0,
                                        (data.statusData && data.statusData.Rejected) ?? 0
                                    ],
                                    backgroundColor: ['#ffc107', '#28a745', '#dc3545']
                                }]
                            }
                        });
                    }

                    // Leave Type Chart (Pie)
                    if (document.getElementById('typeChart')) {
                        new Chart(document.getElementById('typeChart'), {
                            type: 'pie',
                            data: {
                                labels: data.typeLabels || [],
                                datasets: [{
                                    data: data.typeCounts || [],
                                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1']
                                }]
                            }
                        });
                    }
                } catch (e) {
                    console.error('Dashboard render error', e);
                }
            })
            .catch(err => console.error('Failed to load dashboard data', err));

        // Utility
        function closeAllModals() {
            // Close all modals
            document.querySelectorAll(".modal").forEach(modal => {
                modal.style.display = "none";
            });
            
            // Also close any dynamically created overlays
            document.querySelectorAll('[id$="ModalOverlay"]').forEach(overlay => {
                overlay.remove();
            });
            
            // Remove any alert/confirm dialogs
            document.querySelectorAll('[role="alertdialog"], [role="dialog"]').forEach(dialog => {
                if (dialog.style.display !== 'none') {
                    dialog.style.display = 'none';
                }
            });
            
            console.log('All modals closed');
        }

        // Sidebar menu toggles (expand/collapse)
        document.querySelectorAll('.menu-btn.expandable').forEach(btn => {
            btn.addEventListener('click', function () {
                const submenu = this.nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    submenu.classList.toggle('open');
                    const arrow = this.querySelector('.btn-text');
                    if (arrow) {
                        arrow.textContent = arrow.textContent.includes('▸')
                            ? arrow.textContent.replace('▸', '▼')
                            : arrow.textContent.replace('▼', '▸');
                    }
                }
            });
        });

        // Navigation (main + submenu)
        document.querySelectorAll('.menu-btn:not(.expandable), .submenu-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const pageTitle = this.getAttribute('data-title');
                
                // Skip logout - it's handled by handleLogout() function
                if (pageTitle === 'Logout' || targetId === 'n/a') {
                    return;
                }
                
                // Regular navigation
                document.querySelectorAll('.menu-btn.active, .submenu-btn.active').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                if (!targetId) return;
                document.querySelectorAll('.section').forEach(section => section.style.display = 'none');
                const targetSection = document.getElementById(targetId);
                if (targetSection) targetSection.style.display = 'block';
                if (pageTitle) {
                    const titleEl = document.getElementById('page-title');
                    if (titleEl) titleEl.textContent = pageTitle;
                }
            });
        });

        // Open Add Employee modal
        const addEmployeeBtn = document.querySelector(".add-btn");
        if (addEmployeeBtn) {
            addEmployeeBtn.addEventListener("click", () => {
                closeAllModals();
                const modal = document.getElementById("addEmployeeModal");
                if (modal) modal.style.display = "block";
            });
        }

        // Close modal buttons (X)
        document.querySelectorAll(".modal .close, .modal .close-modal").forEach(btn => {
            btn.addEventListener("click", function () {
                const modal = this.closest(".modal");
                if (modal) modal.style.display = "none";
            });
        });

        // Close modal when clicking outside
        window.addEventListener("click", (e) => {
            document.querySelectorAll(".modal").forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
            
            // Close dynamic overlays when clicking outside
            if (e.target.id && e.target.id.endsWith('ModalOverlay')) {
                e.target.remove();
            }
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllModals();
            }
        });

        // Department -> Position dependent select
        const deptSelect = document.getElementById('department-select');
        const posSelect = document.getElementById('position-select');
        if (deptSelect && posSelect) {
            deptSelect.addEventListener('change', function () {
                const deptId = this.value;
                posSelect.innerHTML = '<option value="">Select Position</option>';
                posSelect.disabled = true;
                if (deptId) {
                    fetch('get_positions.php?department_id=' + encodeURIComponent(deptId))
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(pos => {
                                const opt = document.createElement('option');
                                opt.value = pos.PositionID;
                                opt.textContent = pos.PositionName;
                                posSelect.appendChild(opt);
                            });
                            posSelect.disabled = false;
                        })
                        .catch(err => console.error("Error fetching positions:", err));
                }
            });
        }

        const editDept = document.getElementById('editDepartmentSelect');
        if (editDept) {
            editDept.addEventListener('change', function () {
                loadPositionsForDepartment(this.value, 'editPositionSelect');
            });
        }

        // functions referenced from markup/buttons
        window.openAddDepartmentForm = function () {
            closeAllModals();
            const modal = document.getElementById('addDepartmentModal');
            if (modal) {
                const input = modal.querySelector('input[name="department_name"]');
                if (input) input.value = '';
                modal.style.display = 'block';
            }
        };

        window.openAddPositionForm = function () {
            closeAllModals();
            const modal = document.getElementById('addPositionModal');
            if (modal) {
                const dept = modal.querySelector('#addPositionDepartment');
                if (dept) dept.value = '';
                const input = modal.querySelector('input[name="position_name"]');
                if (input) input.value = '';
                modal.style.display = 'block';
            }
        };

        // open edit employee modal (used in employees table)
        window.openEditEmployeeModal = function (employeeId) {
            if (!employeeId) return;
            closeAllModals();
            fetch('get_employee.php?id=' + encodeURIComponent(employeeId))
                .then(res => res.json())
                .then(data => {
                    if (!data) return alert('Employee not found');
                    const idInput = document.getElementById('editEmployeeId');
                    const idLabel = document.getElementById('editEmployeeIDLabel');
                    const fullName = document.getElementById('editFullName');
                    const deptSelect = document.getElementById('editDepartmentSelect');
                    const posSelectId = 'editPositionSelect';

                    if (idInput) idInput.value = data.EmployeeID || employeeId;
                    if (idLabel) idLabel.value = data.EmployeeNumber || (data.EmployeeID || '');
                    if (fullName) fullName.value = `${data.FirstName || ''} ${data.MiddleName || ''} ${data.LastName || ''}`.trim();

                    // set department and load positions (will also set selected position)
                    if (deptSelect) {
                        const deptId = data.DepartmentID || '';
                        deptSelect.value = deptId;
                        loadPositionsForDepartment(deptId, posSelectId, data.PositionID || '');
                    }

                    // populate editable fields if present
                    const statusEl = document.getElementById('editStatus');
                    if (statusEl && typeof data.Status !== 'undefined') statusEl.value = data.Status;

                    // populate current file name spans (if returned from get_employee.php)
                    const fileMap = {
                        'ResumePath': 'resumeCurrent',
                        'DiplomaPath': 'diplomaCurrent',
                        'GovtIDPath': 'govtIdCurrent',
                        'LocalCertificatePath': 'localCertCurrent',
                        'CivilServiceEligibility': 'cseCurrent',
                        'PSABirthCertificate': 'psaBirthCurrent',
                        'PSAMarriageCertificate': 'psaMarriageCurrent',
                        'NBIClearance': 'nbiClearanceCurrent',
                        'PoliceClearance': 'policeClearanceCurrent',
                        'MedicalCertificate': 'medicalCertCurrent',
                        'PersonalDataSheet': 'pdsCurrent',
                        'ValidGovID': 'govIdCurrent',
                        'PRCLicense': 'prcLicenseCurrent',
                        'TranscriptOfRecords': 'torCurrent',
                        'CertificatesOfTraining': 'certTrainingCurrent',
                        'ServiceRecord': 'serviceRecordCurrent',
                        'PerformanceRating': 'performanceRatingCurrent',
                        'HonorGraduateEligibility': 'honorGradCurrent',
                        'TIN': 'tinCurrent',
                        'SSS': 'sssCurrent',
                        'PagIBIG': 'pagibigCurrent',
                        'PhilHealth': 'philhealthCurrent',
                        'OtherDocuments': 'otherDocsCurrent'
                    };
                    Object.keys(fileMap).forEach(key => {
                        const spanId = fileMap[key];
                        const el = document.getElementById(spanId);
                        if (!el) return;
                        const val = data[key] || data[key.replace(/([A-Z])/g, '_$1').toLowerCase()] || data[key.toLowerCase()] || '';
                        el.textContent = val ? (val.indexOf('/') !== -1 ? val.split('/').pop() : val) : '';
                    });

                    // show modal
                    const modal = document.getElementById('editEmployeeModal');
                    if (modal) modal.style.display = 'block';
                })
                .catch(err => {
                    console.error('Failed to load employee for edit:', err);
                    alert('Error loading employee data.');
                });
        };

        // Load employee stats (attempts endpoint, shows fallback message if unavailable)
        function loadEmployeeStats(employeeId) {
            const container = document.getElementById('employeeStatsSection');
            if (!container) return;
            container.innerHTML = '<p>Loading statistics...</p>';
            fetch('get_employee_stats.php?id=' + encodeURIComponent(employeeId))
                .then(res => {
                    if (!res.ok) throw new Error('No stats endpoint');
                    return res.json();
                })
                .then(stats => {
                    // expected keys: thisMonth, thisYear, totalBalanceDays, totalBalanceHours, totalPointsSpent
                    container.innerHTML = `
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                    <div><strong>This Month Requests:</strong> ${stats.thisMonth ?? stats.thisMonthRequests ?? 0}</div>
                    <div><strong>This Year Requests:</strong> ${stats.thisYear ?? stats.thisYearRequests ?? 0}</div>
                    <div><strong>Total Balance (Days):</strong> ${stats.totalBalanceDays ?? 0}</div>
                    <div><strong>Total Points Spent:</strong> ${stats.totalPointsSpent ?? 0}</div>
                </div>
            `;
                })
                .catch(() => {
                    // graceful fallback: brief message (backend may not have endpoint)
                    container.innerHTML = '<p>Statistics not available.</p>';
                });
        }

        function loadPositionsForDepartment(departmentId, targetSelectId, selectedPositionId) {
            const target = document.getElementById(targetSelectId);
            if (!target) return;
            target.innerHTML = '<option value="">Select Position</option>';
            target.disabled = true;
            if (!departmentId) return;
            fetch('get_positions.php?department_id=' + encodeURIComponent(departmentId))
                .then(res => res.json())
                .then(data => {
                    data.forEach(pos => {
                        const opt = document.createElement('option');
                        opt.value = pos.PositionID;
                        opt.textContent = pos.PositionName;
                        target.appendChild(opt);
                    });
                    if (selectedPositionId) target.value = selectedPositionId;
                    target.disabled = false;
                })
                .catch(err => console.error('Error loading positions for department:', err));
        }

        // Load employee files into view modal
        function loadEmployeeFiles(employeeId) {
            const tbody = document.getElementById('employeeFilesTableBody');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="2">Loading...</td></tr>';
            fetch('get_employee_files.php?id=' + encodeURIComponent(employeeId))
                .then(res => res.json())
                .then(files => {
                    if (!Array.isArray(files) || files.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="2">No files found.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = '';
                    files.forEach(f => {
                        const tr = document.createElement('tr');
                        const tdType = document.createElement('td');
                        tdType.textContent = f.DocType || f.docType || '';
                        const tdFile = document.createElement('td');
                        const link = document.createElement('a');
                        link.href = f.FilePath || f.FilePath || '#';
                        link.target = '_blank';
                        link.rel = 'noopener';
                        link.textContent = (f.FilePath || f.FilePath || '').split('/').pop() || 'View';
                        tdFile.appendChild(link);
                        tr.appendChild(tdType);
                        tr.appendChild(tdFile);
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error('Error loading employee files', err);
                    tbody.innerHTML = '<tr><td colspan="2">Error loading files.</td></tr>';
                });
        }

        // Wire view modal tabs (Stats / Files)
        // --- Tab Button Click Logic ---
        const employeeStatsBtn = document.getElementById('employeeStatsBtn');
        const employeeFilesBtn = document.getElementById('employeeFilesBtn');
        const statsSection = document.getElementById('employeeStatsSection');
        const filesSection = document.getElementById('employeeFilesSection');

        if (employeeStatsBtn) {
            employeeStatsBtn.addEventListener('click', function () {
                // Show/Hide Content
                if (statsSection) statsSection.style.display = 'block';
                if (filesSection) filesSection.style.display = 'none';

                // Update Active Class
                employeeStatsBtn.classList.add('active');
                if (employeeFilesBtn) employeeFilesBtn.classList.remove('active');
            });
        }
        if (employeeFilesBtn) {
            employeeFilesBtn.addEventListener('click', function () {
                // Show/Hide Content
                if (statsSection) statsSection.style.display = 'none';
                if (filesSection) filesSection.style.display = 'block';

                // Update Active Class
                employeeFilesBtn.classList.add('active');
                if (employeeStatsBtn) employeeStatsBtn.classList.remove('active');
            });
        }

        // When opening view modal earlier, set dataset.currentId for later use
        // (update the view-employee handler to set it; below is a safe hook to patch if missing)
        (function ensureViewModalHook() {
            const orig = null;
            // find any existing click handler on employeesTbody — we already set data in that handler.
            // No-op here; view handler already calls loadEmployeeStats(employeeId) and shows modal.
        })();

        // Edit employee form submit handler
        const editEmployeeForm = document.getElementById('editEmployeeForm');
        if (editEmployeeForm) {
            editEmployeeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                // Ensure employee id included
                const id = document.getElementById('editEmployeeId')?.value;
                if (id) formData.set('EmployeeID', id);
                fetch('update_employee.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.success) {
                            const modal = document.getElementById('editEmployeeModal');
                            if (modal) modal.style.display = 'none';
                            updateEmployeeTable();
                        } else {
                            alert(resp.error || 'Error saving employee.');
                        }
                    })
                    .catch(err => {
                        console.error('Edit employee failed', err);
                        alert('Network or server error while saving employee.');
                    });
            });
        }

        // Employees table event delegation
        const employeesTbody = document.querySelector('#employees-section tbody');
        if (employeesTbody) {
            employeesTbody.addEventListener('click', function (e) {
                const btn = e.target.closest('button');
                if (!btn) return;

                if (btn.classList.contains('view-employee-btn')) {
                    const employeeId = btn.dataset.id;
                    closeAllModals(); // Assumes this function exists

                    fetch("get_employee.php?id=" + encodeURIComponent(employeeId))
                        .then(res => res.json())
                        .then(data => {
                            if (!data) return alert('Employee not found');

                            const viewModal = document.getElementById("viewEmployeeModal");
                            // **FIX 1: Store the current employee's ID on the modal**
                            if (viewModal) viewModal.dataset.currentId = employeeId;

                            const photoEl = document.getElementById("employeeProfilePhoto");
                            if (photoEl) photoEl.src = data.EmployeePhoto || "//Pictures/DefaultPicture.jpg";

                            const setText = (id, val) => {
                                const el = document.getElementById(id);
                                if (el) el.textContent = val || '';
                            };

                            setText("viewEmployeeName", `${data.FirstName || ''} ${data.MiddleName || ''} ${data.LastName || ''}`.trim());
                            setText("viewEmployeeAge", data.Age || '');
                            setText("viewEmployeeContact", data.ContactNo || '');
                            setText("viewEmployeeEmail", data.Email || '');
                            setText("viewEmployeeDept", data.DepartmentName || '');
                            setText("viewEmployeePosition", data.PositionName || '');
                            setText("viewEmployeeStatus", data.Status || '');
                            setText("viewEmployeeDateHired", data.DateHired || '');

                            // **FIX 2: Load ALL data when the modal opens**
                            loadEmployeeStats(employeeId);
                            loadEmployeeFiles(employeeId); // <-- THIS IS THE FIX

                            // **FIX 3: Reset tabs to the default view**
                            const statsSection = document.getElementById("employeeStatsSection");
                            const filesSection = document.getElementById("employeeFilesSection");
                            if (statsSection) statsSection.style.display = "block";
                            if (filesSection) filesSection.style.display = "none";

                            // Also reset the tab buttons
                            const statsBtn = document.getElementById('employeeStatsBtn');
                            const filesBtn = document.getElementById('employeeFilesBtn');
                            if (statsBtn) statsBtn.classList.add('active');
                            if (filesBtn) filesBtn.classList.remove('active');

                            // Show the modal
                            if (viewModal) viewModal.style.display = "block";
                        })
                        .catch(err => {
                            console.error('Error loading employee profile', err);
                            alert("Error loading employee profile.");
                        });
                }

                if (btn.classList.contains('edit-employee-btn')) {
                    openEditEmployeeModal(btn.dataset.id);
                }

                if (btn.classList.contains('delete-employee-btn')) {
                    const employeeId = btn.dataset.id;
                    closeAllModals();
                    const modal = document.getElementById('deleteEmployeeModal');
                    if (modal) modal.style.display = 'block';
                    const input = document.getElementById('deleteEmployeeId');
                    if (input) input.value = employeeId;
                }
            });
        }

        // Generic update functions
        window.updateEmployeeTable = function () {
            fetch('employee_table.php').then(res => res.text()).then(html => {
                const tbody = document.querySelector('#employees-section tbody');
                if (tbody) tbody.innerHTML = html;
            }).catch(err => console.error('Failed to update employee table', err));
        };
        window.updateDepartmentTable = function () {
            fetch('department_table.php').then(res => res.text()).then(html => {
                const tbody = document.querySelector('#manage-department-section tbody');
                if (tbody) tbody.innerHTML = html;
            }).catch(err => console.error('Failed to update department table', err));
        };
        window.updatePositionTable = function () {
            fetch('position_table.php').then(res => res.text()).then(html => {
                const tbody = document.querySelector('#manage-position-section tbody');
                if (tbody) tbody.innerHTML = html;
            }).catch(err => console.error('Failed to update position table', err));
        };
        window.updateLeaveTypeTable = function () {
            fetch('fetch_leave_types_admin.php').then(res => res.text()).then(html => {
                const tbody = document.querySelector('#manage-leave-section tbody');
                if (tbody) {
                    tbody.innerHTML = html;
                    // Re-attach event listeners to new buttons
                    if (typeof attachLeaveTypeButtonListeners === 'function') {
                        attachLeaveTypeButtonListeners();
                    }
                }
            }).catch(err => console.error('Failed to update leave type table', err));
        };

        // Leave type inputs helper
        window.showLeaveDocumentInputs = function (max) {
            const area = document.getElementById('leaveDocumentsArea');
            if (!area) return;
            area.innerHTML = '';
            for (let i = 1; i <= (max || 0); i++) {
                const label = document.createElement('label');
                label.textContent = `Document ${i}:`;
                const input = document.createElement('input');
                input.type = 'file';
                input.name = `leave_document_${i}`;
                input.accept = '.pdf,.jpg,.jpeg,.png,.doc,.docx';
                area.appendChild(label);
                area.appendChild(input);
                area.appendChild(document.createElement('br'));
            }
        };

        // Form handlers (add/edit/delete etc.)
        const addDepartmentForm = document.getElementById('addDepartmentForm');
        if (addDepartmentForm) {
            addDepartmentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('add_department.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('addDepartmentModal');
                            if (modal) modal.style.display = 'none';
                            updateDepartmentTable();
                            document.querySelectorAll('.section').forEach(s => s.style.display = s.id === 'manage-department-section' ? 'block' : 'none');
                            document.querySelectorAll('.submenu-btn').forEach(btn => btn.classList.toggle('active', btn.getAttribute('data-target') === 'manage-department-section'));
                        } else {
                            alert(data.error || 'Error adding department.');
                        }
                    })
                    .catch(err => console.error('Add department failed', err));
            });
        }

        const editDepartmentForm = document.getElementById('editDepartmentForm');
        if (editDepartmentForm) {
            editDepartmentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('edit_department.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('editDepartmentModal');
                            if (modal) modal.style.display = 'none';
                            updateDepartmentTable();
                        } else {
                            alert(data.error || 'Error editing department.');
                        }
                    })
                    .catch(err => console.error('Edit department failed', err));
            });
        }

        const deleteDepartmentForm = document.getElementById('deleteDepartmentForm');
        if (deleteDepartmentForm) {
            deleteDepartmentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('delete_department.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('deleteDepartmentModal');
                            if (modal) modal.style.display = 'none';
                            updateDepartmentTable();
                        } else {
                            alert(data.error || 'Error deleting department.');
                        }
                    })
                    .catch(err => console.error('Delete department failed', err));
            });
        }

        const addPositionForm = document.getElementById('addPositionForm');
        if (addPositionForm) {
            addPositionForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('add_position.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('addPositionModal');
                            if (modal) modal.style.display = 'none';
                            updatePositionTable();
                        } else {
                            alert(data.error || 'Error adding position.');
                        }
                    })
                    .catch(err => console.error('Add position failed', err));
            });
        }

        const editPositionForm = document.getElementById('editPositionForm');
        if (editPositionForm) {
            editPositionForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('edit_position.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('editPositionModal');
                            if (modal) modal.style.display = 'none';
                            updatePositionTable();
                        } else {
                            alert(data.error || 'Error editing position.');
                        }
                    })
                    .catch(err => console.error('Edit position failed', err));
            });
        }

        const deletePositionForm = document.getElementById('deletePositionForm');
        if (deletePositionForm) {
            deletePositionForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('delete_position.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('deletePositionModal');
                            if (modal) modal.style.display = 'none';
                            updatePositionTable();
                        } else {
                            alert(data.error || 'Error deleting position.');
                        }
                    })
                    .catch(err => console.error('Delete position failed', err));
            });
        }

        const leaveTypeForm = document.getElementById('leaveTypeForm');
        if (leaveTypeForm) {
            leaveTypeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = this.getAttribute('action') || this.action || 'create_leave_type.php';
                fetch(action, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('leaveTypeModal');
                            if (modal) modal.style.display = 'none';
                            updateLeaveTypeTable();
                        } else {
                            alert(data.error || 'Error saving leave type.');
                        }
                    })
                    .catch(err => console.error('Save leave type failed', err));
            });
        }

        const addEmployeeForm = document.getElementById('addEmployeeForm');
        if (addEmployeeForm) {
            addEmployeeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('add_employee.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('addEmployeeModal');
                            if (modal) modal.style.display = 'none';
                            updateEmployeeTable();
                        } else {
                            alert(data.error || 'Error adding employee.');
                        }
                    })
                    .catch(err => console.error('Add employee failed', err));
            });
        }

        // Wizard Form Submit Handler
        const addEmployeeWizardForm = document.getElementById('addEmployeeWizardForm');
        if (addEmployeeWizardForm) {
            addEmployeeWizardForm.addEventListener('submit', function (e) {
                e.preventDefault();
                
                // Show loading alert
                Swal.fire({
                    title: 'Adding Employee...',
                    html: 'Please wait while we process your request.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const formData = new FormData(this);
                fetch('add_employee.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Show success alert
                            Swal.fire({
                                title: 'Success!',
                                text: 'Employee has been added successfully.',
                                icon: 'success',
                                confirmButtonColor: '#0052CC',
                                confirmButtonText: 'Go to Dashboard'
                            }).then(() => {
                                // Close modal
                                const modal = document.getElementById('addEmployeeModal');
                                if (modal) modal.style.display = 'none';
                                updateEmployeeTable();
                                
                                // Navigate to admin dashboard
                                const dashboardSection = document.getElementById('dashboard-section');
                                if (dashboardSection) {
                                    // Hide all sections
                                    document.querySelectorAll('.section').forEach(section => {
                                        section.style.display = 'none';
                                    });
                                    // Show dashboard
                                    dashboardSection.style.display = 'block';
                                    
                                    // Update page title
                                    document.title = 'Admin Dashboard';
                                    
                                    // Update active menu item
                                    document.querySelectorAll('.menu-btn.active, .submenu-btn.active').forEach(el => {
                                        el.classList.remove('active');
                                    });
                                    const dashboardBtn = document.querySelector('[data-target="dashboard-section"]');
                                    if (dashboardBtn) dashboardBtn.classList.add('active');
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.error || 'Failed to add employee.',
                                icon: 'error',
                                confirmButtonColor: '#0052CC'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Add employee failed', err);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while adding the employee.',
                            icon: 'error',
                            confirmButtonColor: '#0052CC'
                        });
                    });
            });
        }

        const deleteEmployeeForm = document.getElementById('deleteEmployeeForm');
        if (deleteEmployeeForm) {
            deleteEmployeeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('delete_employee.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = document.getElementById('deleteEmployeeModal');
                            if (modal) modal.style.display = 'none';
                            updateEmployeeTable();
                        } else {
                            alert(data.error || 'Error deleting employee.');
                        }
                    })
                    .catch(err => console.error('Delete employee failed', err));
            });
        }


        (function wireArchiveRestore() {
            // Update archived table fragment
            window.updateArchivedTable = function () {
                fetch('archived_employee_table.php').then(res => res.text()).then(html => {
                    const tbody = document.querySelector('#archive-section tbody');
                    if (tbody) tbody.innerHTML = html;
                }).catch(err => console.error('Failed to update archived employee table', err));
            };

            // Restore an archived employee back to Employee
            document.querySelector('#archive-section tbody')?.addEventListener('click', function (e) {
                const restoreBtn = e.target.closest('.restore-employee-btn');
                if (!restoreBtn) return;
                const btn = e.target.closest('.restore-employee-btn');
                const id = restoreBtn.dataset.id;
                if (!id) return;
                if (!confirm('Restore this employee?')) return;
                fetch('restore_employee.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'EmployeeID=' + encodeURIComponent(id)
                })
                    .then(r => r.json())
                    .then(resp => {
                        if (resp.success) {
                            updateEmployeeTable();
                            updateArchivedTable();
                        } else {
                            alert(resp.error || 'Failed to restore employee');
                        }
                    })
                    .catch(() => alert('Network error'));
            }, false);

            // Use delegation that checks for restore or delete in archive table
            document.querySelector('#archive-section tbody')?.addEventListener('click', function (e) {
                const restoreBtn = e.target.closest('.restore-employee-btn');
                if (restoreBtn) {
                    const id = restoreBtn.dataset.id;
                    if (!id) return;
                    Swal.fire({
                        title: 'Restore Employee?',
                        text: 'Are you sure you want to restore this employee?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Restore',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Restoring...',
                                html: 'Please wait while we restore the employee.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            fetch('restore_employee.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'EmployeeID=' + encodeURIComponent(id)
                            })
                                .then(r => r.json())
                                .then(resp => {
                                    if (resp.success) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: 'Employee has been restored successfully.',
                                            icon: 'success',
                                            confirmButtonColor: '#28a745',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            updateEmployeeTable();
                                            updateArchivedTable();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: resp.error || 'Failed to restore employee',
                                            icon: 'error',
                                            confirmButtonColor: '#dc3545',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Network error occurred',
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545',
                                        confirmButtonText: 'OK'
                                    });
                                });
                        }
                    });
                    return;
                }

                const deleteBtn = e.target.closest('.delete-employee-btn');
                if (deleteBtn) {
                    const id = deleteBtn.dataset.id;
                    if (!id) return;
                    Swal.fire({
                        title: 'Delete Employee?',
                        text: 'Are you sure you want to permanently delete this archived employee? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                html: 'Please wait while we delete the employee.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            fetch('delete_employee.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'EmployeeID=' + encodeURIComponent(id)
                            })
                                .then(r => r.json())
                                .then(resp => {
                                    if (resp.success) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Employee has been permanently deleted.',
                                            icon: 'success',
                                            confirmButtonColor: '#28a745',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            updateArchivedTable();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: resp.error || 'Failed to delete archived employee',
                                            icon: 'error',
                                            confirmButtonColor: '#dc3545',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Network error occurred',
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545',
                                        confirmButtonText: 'OK'
                                    });
                                });
                        }
                    });
                }
            }, false);

        })();

        // Debugging click logs
        document.addEventListener("click", function (e) {
            // Comment out in production if verbose
            console.log("Clicked element:", e.target);
            if (e.target && e.target.dataset) {
                if (e.target.classList.contains("edit-employee-btn")) {
                    console.log("Edit button clicked for ID:", e.target.dataset.id);
                }
                if (e.target.classList.contains("delete-employee-btn")) {
                    console.log("Delete button clicked for ID:", e.target.dataset.id);
                }
                if (e.target.classList.contains("view-employee-btn")) {
                    console.log("View button clicked for ID:", e.target.dataset.id);
                }
            }
        });

        // --- Get Modal Elements (defined once) ---
        const leaveTypeModal = document.getElementById("leaveTypeModal");
        const modalTitle = document.getElementById("leaveTypeModalTitle");
        const form = document.getElementById("leaveTypeForm");
        const saveBtn = document.getElementById("saveLeaveTypeBtn");

        // Form Fields
        const leaveTypeIdInput = document.getElementById("LeaveTypeID");
        const typeNameInput = document.getElementById("TypeName");
        const descriptionInput = document.getElementById("Description");
        const pointCostInput = document.getElementById("PointCost");

        // --- Place this in your main script ---

// (Assume these are defined in your script's scope)
const unitTypeSelect = document.getElementById("UnitType");
const fixedDaysInput = document.getElementById("FixedDays");
const creditLeaveConfigRow = document.getElementById("creditLeaveConfigRow");
const fixedDaysConfigRow = document.getElementById("fixedDaysConfigRow");
const allowDocsSelect = document.getElementById("AllowDocuments");
const maxDocsInput = document.getElementById("MaxDocuments");
const maxDocumentsRow = document.getElementById("maxDocumentsRow");

// (These are the new variables you need to define)
const usageFrequencyRow = document.getElementById("usageFrequencyRow");
const usageFrequencySelect = document.getElementById("UsageFrequency");

/**
 * Shows/hides form rows based on the selected "Leave Policy Type"
 */
function updateConditionalRows() {
    if (!unitTypeSelect || !creditLeaveConfigRow || !fixedDaysConfigRow) return;

    const selectedType = unitTypeSelect.value;

    if (selectedType === 'Fixed Days') {
        // Show "Fixed Days" options
        creditLeaveConfigRow.style.display = "none";
        fixedDaysConfigRow.style.display = "block";
        // usageFrequencyRow is *inside* fixedDaysConfigRow, so it's handled.
        // Or, if you put it outside, show it here:
        // if (usageFrequencyRow) usageFrequencyRow.style.display = "block";
    } else {
        // Show "Leave Credit" options
        creditLeaveConfigRow.style.display = "block";
        fixedDaysConfigRow.style.display = "none";
        // if (usageFrequencyRow) usageFrequencyRow.style.display = "none";
    }
}

/**
 * Shows/hides the "Max Documents" input
 */
function updateMaxDocsRow() {
    if (!allowDocsSelect || !maxDocumentsRow) return;
    
    maxDocumentsRow.style.display = (allowDocsSelect.value === "1") ? "block" : "none";
}

// --- Attach the Event Listeners ---
if (unitTypeSelect) {
    unitTypeSelect.addEventListener("change", updateConditionalRows);
}
if (allowDocsSelect) {
    allowDocsSelect.addEventListener("change", updateMaxDocsRow);
}

// --- Your "Open Create Modal" function, now with the new field reset ---
const openCreateLeaveTypeBtn = document.getElementById("openCreateLeaveTypeModal");
if (openCreateLeaveTypeBtn) {
    openCreateLeaveTypeBtn.addEventListener("click", function () {
        
        // (Assuming 'modalTitle', 'form', etc. are defined)
        if (modalTitle) modalTitle.innerText = "Create Leave Type";
        if (form) {
            form.action = "create_leave_type.php";
            form.reset();
        }

        if (leaveTypeIdInput) leaveTypeIdInput.value = "";
        if (unitTypeSelect) unitTypeSelect.value = "Leave Credit";
        if (allowDocsSelect) allowDocsSelect.value = "0";
        if (pointCostInput) pointCostInput.value = 0;
        if (fixedDaysInput) fixedDaysInput.value = 7;
        if (maxDocsInput) maxDocsInput.value = 1;
        
        // --- NEW: Reset the UsageFrequency dropdown ---
        if (usageFrequencySelect) usageFrequencySelect.value = "PerYear";
        // --- End New ---

        if (saveBtn) saveBtn.innerText = "Create";

        // Trigger change events to hide conditional rows
        updateConditionalRows();
        updateMaxDocsRow();

        if (leaveTypeModal) leaveTypeModal.style.display = "block";
    });
}

        // --- Function to attach event listeners to leave type action buttons ---
        function attachLeaveTypeButtonListeners() {
            // Edit button listeners
            document.querySelectorAll(".edit-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    const id = this.dataset.id;
                    if (!id) {
                        console.error('No leave type ID found');
                        return;
                    }

                    // Fetch leave type details from server
                    fetch('get_leave_type.php?id=' + encodeURIComponent(id))
                        .then(res => res.json())
                        .then(data => {
                            if (data.error) {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.error || 'Failed to load leave type details',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }

                            if (modalTitle) modalTitle.innerText = "Edit Leave Type";
                            if (form) form.action = "save_leave_type.php";
                            if (saveBtn) saveBtn.innerText = "Save";

                            // Populate all fields from fetched data
                            if (leaveTypeIdInput) leaveTypeIdInput.value = data.LeaveTypeID || "";
                            if (typeNameInput) typeNameInput.value = data.TypeName || "";
                            if (descriptionInput) descriptionInput.value = data.Description || "";
                            if (pointCostInput) pointCostInput.value = data.PointCost || 0;
                            if (unitTypeSelect) unitTypeSelect.value = data.UnitType || "Leave Credit";
                            if (fixedDaysInput) fixedDaysInput.value = data.MaxDaysPerUsage || 7;
                            if (allowDocsSelect) allowDocsSelect.value = data.AllowDocuments || "0";
                            if (maxDocsInput) maxDocsInput.value = data.MaxDocuments || 1;

                            // Trigger change events to show/hide rows based on populated data
                            updateConditionalRows();
                            updateMaxDocsRow();

                            if (leaveTypeModal) leaveTypeModal.style.display = "block";
                        })
                        .catch(err => {
                            console.error('Failed to fetch leave type', err);
                            Swal.fire({
                                title: 'Error',
                                text: 'Network error while loading leave type details',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        });
                });
            });

            // Delete button listeners
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    const id = this.dataset.id;
                    Swal.fire({
                        title: 'Delete Leave Type?',
                        text: 'Are you sure you want to delete this leave type? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                html: 'Please wait while we delete the leave type.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            fetch("delete_leave_type.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body: "id=" + encodeURIComponent(id)
                            })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Leave type has been deleted successfully.',
                                            icon: 'success',
                                            confirmButtonColor: '#28a745',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            if (typeof updateLeaveTypeTable === 'function') {
                                                updateLeaveTypeTable();
                                            } else {
                                                location.reload();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: data.error || "Error deleting leave type.",
                                            icon: 'error',
                                            confirmButtonColor: '#dc3545',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error('Delete leave type failed', err);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Network error occurred',
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545',
                                        confirmButtonText: 'OK'
                                    });
                                });
                        }
                    });
                });
            });
        }

        if (saveBtn && form) {
            saveBtn.addEventListener('click', function () {
                try {
                    // ensure action is set
                    const action = form.getAttribute('action') || form.action || 'create_leave_type.php';
                    const fd = new FormData(form);

                    fetch(action, { method: 'POST', body: fd })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.success) {
                                // close modal and refresh table
                                if (leaveTypeModal) leaveTypeModal.style.display = 'none';
                                if (typeof updateLeaveTypeTable === 'function') updateLeaveTypeTable();
                            } else {
                                alert((data && data.error) ? data.error : 'Error saving leave type.');
                                console.error('Save leave type response:', data);
                            }
                        })
                        .catch(err => {
                            console.error('Save leave type failed', err);
                            alert('Network or server error while saving leave type.');
                        });
                } catch (e) {
                    console.error('Save leave type handler error', e);
                }
            });
        }

        // --- Close Modal ---
        const closeLeaveTypeModalBtn = document.getElementById("closeLeaveTypeModal");
        const cancelLeaveTypeBtn = document.getElementById("cancelLeaveTypeBtn");

        function closeTheModal() {
            if (leaveTypeModal) leaveTypeModal.style.display = "none";
        }

        if (closeLeaveTypeModalBtn) {
            closeLeaveTypeModalBtn.addEventListener("click", closeTheModal);
        }
        if (cancelLeaveTypeBtn) {
            cancelLeaveTypeBtn.addEventListener("click", closeTheModal);
        }

        // --- Attach listeners on page load ---
        attachLeaveTypeButtonListeners();

        // --- Other Admin Area Leave Select (Unchanged) ---
        const leaveTypeSelect = document.getElementById('leaveTypeSelect');
        if (leaveTypeSelect) {
            leaveTypeSelect.addEventListener('change', function () {
                const leaveTypeId = this.value;
                fetch('get_leave_type.php?id=' + encodeURIComponent(leaveTypeId))
                    .then(res => res.json())
                    .then(data => {
                        if (data && parseInt(data.AllowDocuments) === 1) {
                            if (typeof showLeaveDocumentInputs === 'function') {
                                showLeaveDocumentInputs(parseInt(data.MaxDocuments) || 0);
                            }
                        } else {
                            const area = document.getElementById('leaveDocumentsArea');
                            if (area) area.innerHTML = '';
                        }
                    })
                    .catch(err => console.error('Failed to fetch leave type', err));
            });
        }

        // Initial call to set correct state
        updateConditionalRows(); // **UPDATED**
        updateMaxDocsRow();

        // --- Get Modal Elements ---
    const detailsModal = document.getElementById("viewLeaveDetailsModal");
    const closeDetailsBtn = document.getElementById("closeViewDetailsModal");
    const cancelDetailsBtn = document.getElementById("cancelLeaveDetailsBtn");

    // --- Get Table Body ---
    const requestTbody = document.querySelector('#request-section tbody');

if (requestTbody) {
    requestTbody.addEventListener('click', function (e) {
        
        const approveBtn = e.target.closest('.approve-app-btn');
        const rejectBtn = e.target.closest('.reject-app-btn');
        const viewBtn = e.target.closest('.view-application-btn'); // Added view button

        // --- Logic for APPROVE button ---
        if (approveBtn) {
            const id = approveBtn.dataset.id;
            if (!confirm('Approve this application?')) return;

            // Find the row relative to the button
            const row = approveBtn.closest('tr');
            if (row) row.style.opacity = '0.5';

            // --- THIS IS THE FIX ---
            // Use FormData to reliably send the POST data
            const formData = new FormData();
            formData.append('id', id);

            fetch('approve_application.php', {
                method: 'POST',
                body: formData // No headers needed; fetch does it for you
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    if (row) row.remove(); // Remove the row
                } else {
                    alert(resp.error || 'Failed to approve');
                    if (row) row.style.opacity = '1';
                }
            })
            .catch(() => {
                alert('Network error');
                if (row) row.style.opacity = '1';
            });
            return;
        }

        // --- Logic for REJECT button ---
        if (rejectBtn) {
            const id = rejectBtn.dataset.id;
            if (!confirm('Reject this application?')) return;

            const row = rejectBtn.closest('tr');
            if (row) row.style.opacity = '0.5';

            // --- THIS IS THE FIX ---
            const formData = new FormData();
            formData.append('id', id);

            fetch('reject_application.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    if (row) row.remove();
                } else {
                    alert(resp.error || 'Failed to reject');
                    if (row) row.style.opacity = '1';
                }
            })
            .catch(() => {
                alert('Network error');
                if (row) row.style.opacity = '1';
            });
            return;
        }

            // --- Logic for VIEW button ---
            if (viewBtn) {
                const appId = viewBtn.dataset.id;
                
                // 1. Show modal
                detailsModal.style.display = 'block';
                
                // 2. Set loading text
                document.getElementById('form-department').textContent = 'Loading...';
                document.getElementById('form-name').textContent = 'Loading...';
                document.getElementById('form-date-filing').textContent = 'Loading...';
                document.getElementById('form-position').textContent = 'Loading...';
                document.getElementById('form-salary').textContent = 'Loading...';
                // ... (clear other fields) ...
                document.getElementById('form-leave-type').innerHTML = '';
                document.getElementById('form-leave-details').innerHTML = '';
                document.getElementById('form-duration').textContent = '';
                document.getElementById('form-dates').textContent = '';
                document.getElementById('form-reason').textContent = '';
                document.getElementById('form-approval-days').textContent = '';
                document.getElementById('form-disapproval-reason').textContent = '';
                document.getElementById('form-recommendation').innerHTML = 
                    `<span class="cs-checkbox">[ ]</span> For approval<br>
                     <span class="cs-checkbox">[ ]</span> For disapproval due to...`;

                // 3. Fetch the application details
                fetch('get_application_details_admin.php?id=' + encodeURIComponent(appId))
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            detailsModal.style.display = 'none';
                            return;
                        }

                        // 4. Populate the CS Form
                        // Box 1-5 (Employee Info)
                        document.getElementById('form-department').textContent = data.Department || 'N/A';
                        document.getElementById('form-name').textContent = 
                            `${data.LastName || ''}, ${data.FirstName || ''} ${data.MiddleName || ''}`;
                        document.getElementById('form-date-filing').textContent = data.DateRequestedFormatted;
                        document.getElementById('form-position').textContent = data.Position || 'N/A';
                        document.getElementById('form-salary').textContent = data.Salary ? `₱${parseFloat(data.Salary).toLocaleString()}` : 'N/A';
                        
                        // Box 6 (Leave Details)
                        document.getElementById('form-leave-type').innerHTML = 
                            `<span class="cs-checkbox">[X]</span> ${data.TypeName}`;
                        
                        let details = data.Reason;
                        if (data.TypeName === 'Sick Leave') {
                            details = `<span class="cs-checkbox">[ ]</span> In Hospital<br>
                                     <span class="cs-checkbox">[X]</span> Out Patient: ${data.Reason}`;
                        } else if (data.TypeName === 'Vacation Leave') {
                            details = `<span class="cs-checkbox">[X]</span> Within Philippines<br>
                                     <span class="cs-checkbox">[ ]</span> Abroad (Specify):`;
                        }
                        document.getElementById('form-leave-details').innerHTML = details;
                        
                        document.getElementById('form-duration').textContent = `${data.DurationDays} day(s)`;
                        document.getElementById('form-dates').textContent = `${data.StartDate} to ${data.EndDate}`;

                        // Reason Box
                        document.getElementById('form-reason').textContent = data.Reason || 'No reason provided.';

                        // Box 7 (Admin Action) - This shows the *current* status
                        if (data.Status === 'Approved') {
                            document.getElementById('form-recommendation').innerHTML = 
                                `<span class="cs-checkbox">[X]</span> For approval`;
                            document.getElementById('form-approval-days').textContent = `${data.DurationDays} day(s) with pay`;
                        } else if (data.Status === 'Rejected') {
                            document.getElementById('form-recommendation').innerHTML = 
                                `<span class="cs-checkbox">[X]</span> For disapproval due to: ${data.RejectionReason || ''}`;
                            document.getElementById('form-disapproval-reason').textContent = data.RejectionReason || 'See 7.B';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching leave details:', err);
                        alert('A network error occurred.');
                        detailsModal.style.display = 'none';
                    });
            }
        });
    }

    // --- Modal Close Logic ---
    if (closeDetailsBtn) {
        closeDetailsBtn.onclick = () => { detailsModal.style.display = 'none'; }
    }
    if (cancelDetailsBtn) {
        cancelDetailsBtn.onclick = () => { detailsModal.style.display = 'none'; }
    }
    window.onclick = (event) => {
        if (event.target === detailsModal) {
            detailsModal.style.display = "none";
        }
    };

    
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
    
    // --- LEAVE HISTORY SEARCH ---
// (Add this inside your DOMContentLoaded listener)


const historySearchInput = document.getElementById('historySearchInput');
const historySearchBtn = document.getElementById('historySearchBtn');
const historyTableBody = document.getElementById('historyTableBody');

if (historySearchBtn) {
    // 1. Trigger search on button click
    historySearchBtn.addEventListener('click', fetchLeaveHistory);

    // 2. Trigger search on pressing "Enter"
    historySearchInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            fetchLeaveHistory();
        }
    });
}

function fetchLeaveHistory() {
    const query = historySearchInput.value.trim();
    historyTableBody.innerHTML = '<tr><td colspan="8">Searching...</td></tr>';

    // 3. Call your new PHP script
    fetch(`get_leave_history.php?search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            populateHistoryTable(data);
        })
        .catch(error => {
            console.error('Error fetching history:', error);
            historyTableBody.innerHTML = '<tr><td colspan="8">An error occurred.</td></tr>';
        });
}

function populateHistoryTable(historyItems) {
    // This variable must match the ID from Step 1
    const historyTableBody = document.getElementById('historyTableBody');
    
    // 1. Check if the table body exists
    if (!historyTableBody) {
        console.error("JavaScript Error: Cannot find the element #historyTableBody.");
        return;
    }

    // 2. Clear the table
    historyTableBody.innerHTML = '';

    if (!historyItems || historyItems.length === 0) {
        historyTableBody.innerHTML = '<tr><td colspan="8">No records found.</td></tr>';
        return;
    }

    // 3. Build and insert a new row for each item
    historyItems.forEach(item => {
        
        // --- Create a link for documents ONLY if DocumentCount > 0 ---
        let docLink = 'N/A';
        if (item.DocumentCount > 0) {
            docLink = `<a href="view_documents.php?app_id=${item.ApplicationID}" target="_blank">
                           View (${item.DocumentCount})
                       </a>`;
        }

        const row = `
            <tr>
                <td>${item.ControlNumber || item.ApplicationID}</td>
                <td>${item.FirstName} ${item.LastName}</td>
                <td>${item.DepartmentName}</td>
                <td>${item.TypeName}</td>
                <td>${item.StartDate}</td>
                <td>${item.EndDate}</td>
                <td>
                    <span class="status-badge status-${item.Status.toLowerCase()}">
                        ${item.Status}
                    </span>
                </td>
                <td>
                <button class="view-history-btn" data-id="${item.ApplicationID}">View</button>
                </td>
            </tr>
        `;
        historyTableBody.innerHTML += row;
    });
    
    // Add event listener for View buttons in history table
    document.querySelectorAll('.view-history-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.getAttribute('data-id');
            viewLeaveApplicationFromHistory(appId);
        });
    });
}

fetchLeaveHistory();

    // ===== ACTION ICONS EVENT LISTENERS =====
    
    // Edit Employee Icon Click Handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-employee-btn')) {
            const employeeId = e.target.getAttribute('data-id');
            editEmployee(employeeId);
        }
    });

    // View Employee Icon Click Handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-employee-btn')) {
            const employeeId = e.target.getAttribute('data-id');
            viewEmployee(employeeId);
        }
    });

    // Archive Employee Icon Click Handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('archive-employee-btn')) {
            const employeeId = e.target.getAttribute('data-id');
            archiveEmployee(employeeId);
        }
    });

    // Function to edit employee
    function editEmployee(employeeId) {
        console.log('Edit employee:', employeeId);
        // Fetch employee data and show edit modal
        fetch(`get_employee.php?id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                // Display employee edit form in a modal
                showEditEmployeeModal(data);
            })
            .catch(error => {
                console.error('Error fetching employee:', error);
                alert('Failed to load employee details');
            });
    }

    // Function to view employee
    function viewEmployee(employeeId) {
        console.log('View employee:', employeeId);
        // Fetch and display employee details in a modal or redirect to view page
        fetch(`get_employee.php?id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                // Display employee details
                showEmployeeModal(data);
            })
            .catch(error => {
                console.error('Error fetching employee:', error);
                alert('Failed to load employee details');
            });
    }

    // Function to show edit employee form in a modal
    function showEditEmployeeModal(employee) {
        const modalContent = `
            <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); padding: 0; border-radius: 16px; max-width: 800px; width: 90%; margin: 20px auto; max-height: 90vh; overflow-y: auto; position: relative; z-index: 1001; box-shadow: 0 20px 60px rgba(0, 82, 204, 0.15); border: 1px solid rgba(0, 82, 204, 0.1);">
                <div style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); padding: 25px 30px; border-radius: 16px 16px 0 0; position: sticky; top: 0; z-index: 10;">
                    <h2 style="color: white; margin: 0; font-size: 24px; font-weight: 600;">Edit Employee</h2>
                </div>
                <form id="editEmployeeForm" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 30px;">
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">First Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" name="firstName" maxlength="100" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" placeholder="Enter first name" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Middle Name</label>
                        <input type="text" name="middleName" maxlength="100" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" placeholder="Enter middle name" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Last Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" name="lastName" maxlength="100" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" placeholder="Enter last name" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Email <span style="color: #dc3545;">*</span></label>
                        <input type="email" name="email" maxlength="255" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" placeholder="Enter valid email" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Contact No <span style="color: #dc3545;">*</span></label>
                        <input type="tel" name="phone" maxlength="11" pattern="[0-9]{11}" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" placeholder="11 digits (e.g., 09123456789)" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Birthdate <span style="color: #dc3545;">*</span></label>
                        <input type="date" name="birthdate" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Department <span style="color: #dc3545;">*</span></label>
                        <select id="departmentSelect" name="department" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease; background-color: white;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                            <option value="">-- Select Department --</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Position <span style="color: #dc3545;">*</span></label>
                        <select id="positionSelect" name="position" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease; background-color: white;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                            <option value="">-- Select Position --</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Status</label>
                        <select name="status" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease; background-color: white;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                            <option value="Full Time" ${employee.Status === 'Full Time' ? 'selected' : ''}>Full Time</option>
                            <option value="Part Time" ${employee.Status === 'Part Time' ? 'selected' : ''}>Part Time</option>
                            <option value="Contract Worker" ${employee.Status === 'Contract Worker' ? 'selected' : ''}>Contract Worker</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #0052CC; display: block; margin-bottom: 8px; font-size: 14px;">Date Hired</label>
                        <input type="date" name="dateHired" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 14px; transition: all 0.3s ease;" onfocus="this.style.borderColor='#0052CC'; this.style.boxShadow='0 0 0 3px rgba(0, 82, 204, 0.1)'" onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                    </div>
                </form>
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding: 20px 30px; border-top: 1px solid rgba(0, 82, 204, 0.1); background: rgba(0, 82, 204, 0.02); position: sticky; bottom: 0; z-index: 10;">
                    <button type="button" id="cancelBtn" style="background: #f0f0f0; color: #333; padding: 12px 28px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;" onmouseover="this.style.background='#e8e8e8'; this.style.borderColor='#999'" onmouseout="this.style.background='#f0f0f0'; this.style.borderColor='#e0e0e0'">Cancel</button>
                    <button type="button" id="saveBtn" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">Save Changes</button>
                </div>
            </div>
        `;
        
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.id = 'employeeModalOverlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;';
        overlay.innerHTML = modalContent;
        
        document.body.appendChild(overlay);
        
        // Set form field values directly
        const form = overlay.querySelector('#editEmployeeForm');
        if (form) {
            // Set input values
            const firstNameInput = form.querySelector('input[name="firstName"]');
            const middleNameInput = form.querySelector('input[name="middleName"]');
            const lastNameInput = form.querySelector('input[name="lastName"]');
            const emailInput = form.querySelector('input[name="email"]');
            const phoneInput = form.querySelector('input[name="phone"]');
            const birthdateInput = form.querySelector('input[name="birthdate"]');
            const dateHiredInput = form.querySelector('input[name="dateHired"]');
            const statusSelect = form.querySelector('select[name="status"]');
            
            if (firstNameInput) firstNameInput.value = employee.FirstName || '';
            if (middleNameInput) middleNameInput.value = employee.MiddleName || '';
            if (lastNameInput) lastNameInput.value = employee.LastName || '';
            if (emailInput) emailInput.value = employee.Email || '';
            if (phoneInput) phoneInput.value = employee.ContactNo || '';
            if (birthdateInput) birthdateInput.value = employee.Birthdate || '';
            if (dateHiredInput) dateHiredInput.value = employee.DateHired || '';
            console.log('Status select element found:', !!statusSelect);
            console.log('Current employee status:', employee.Status);
            if (statusSelect) {
                statusSelect.value = employee.Status || 'Active';
                console.log('Status value set to:', statusSelect.value);
            }
            
            console.log('Form values set:', {
                firstName: firstNameInput?.value,
                middleName: middleNameInput?.value,
                lastName: lastNameInput?.value,
                email: emailInput?.value,
                phone: phoneInput?.value,
                birthdate: birthdateInput?.value,
                dateHired: dateHiredInput?.value,
                status: statusSelect?.value,
                departmentID: employee.DepartmentID,
                positionID: employee.PositionID
            });
            console.log('Employee raw data from backend:', {
                Birthdate: employee.Birthdate,
                DateHired: employee.DateHired,
                Email: employee.Email
            });
            
            // Store original values in form data attributes for change detection
            form.setAttribute('data-original-firstName', employee.FirstName || '');
            form.setAttribute('data-original-middleName', employee.MiddleName || '');
            form.setAttribute('data-original-lastName', employee.LastName || '');
            form.setAttribute('data-original-email', employee.Email || '');
            form.setAttribute('data-original-phone', employee.ContactNo || '');
            form.setAttribute('data-original-birthdate', employee.Birthdate || '');
            form.setAttribute('data-original-status', employee.Status || '');
            form.setAttribute('data-original-dateHired', employee.DateHired || '');
            form.setAttribute('data-original-department', employee.DepartmentID || '');
            form.setAttribute('data-original-position', employee.PositionID || '');
        }
        
        // Populate department and position dropdowns
        // Add delay to ensure they load properly
        console.log('Starting dropdown load with delay...');
        console.log('Employee data:', {
            DepartmentID: employee.DepartmentID,
            PositionID: employee.PositionID,
            Status: employee.Status
        });
        setTimeout(() => {
            console.log('Loading dropdowns now...');
            loadDepartments(employee.DepartmentID);
            loadPositions(employee.PositionID, employee.DepartmentID);
        }, 500);
        
        // Add event listeners to buttons
        const cancelBtn = overlay.querySelector('#cancelBtn');
        const saveBtn = overlay.querySelector('#saveBtn');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeEmployeeModal();
            });
        }
        
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                saveEmployeeChanges(employee.EmployeeID);
            });
        }
        
        // Add event listener for department change to filter positions
        const deptSelect = overlay.querySelector('#departmentSelect');
        if (deptSelect) {
            deptSelect.addEventListener('change', function() {
                const selectedDeptId = this.value;
                console.log('Department changed to:', selectedDeptId);
                if (selectedDeptId) {
                    // Load positions for the selected department
                    loadPositions(null, selectedDeptId);
                } else {
                    // Clear positions if no department selected
                    const posSelect = overlay.querySelector('#positionSelect');
                    if (posSelect) {
                        posSelect.innerHTML = '<option value="">-- Select Position --</option>';
                    }
                }
            });
        }
        
        // Close on overlay click (but not on modal content)
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeEmployeeModal();
            }
        });
    }

    // Function to load departments
    function loadDepartments(selectedDeptId) {
        console.log('loadDepartments called with ID:', selectedDeptId);
        fetch('get_departments.php')
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch departments');
                return response.json();
            })
            .then(data => {
                const deptSelect = document.getElementById('departmentSelect');
                if (!deptSelect) {
                    console.error('Department select not found');
                    return;
                }
                
                // Clear existing options except the first one
                while (deptSelect.options.length > 1) {
                    deptSelect.remove(1);
                }
                
                // Add all department options
                if (data && Array.isArray(data) && data.length > 0) {
                    data.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.DepartmentID;
                        option.textContent = dept.DepartmentName;
                        deptSelect.appendChild(option);
                    });
                    
                    // Set selected value
                    if (selectedDeptId) {
                        deptSelect.value = selectedDeptId;
                        console.log('✓ Department set to:', deptSelect.value);
                    }
                }
            })
            .catch(error => console.error('Error loading departments:', error));
    }

    // Function to load positions
    function loadPositions(selectedPosId, departmentId) {
        console.log('loadPositions called with PosId:', selectedPosId, 'DeptId:', departmentId);
        
        // Build URL with department filter if provided
        let url = 'get_positions.php';
        if (departmentId) {
            url += '?department_id=' + encodeURIComponent(departmentId);
        }
        
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch positions');
                return response.json();
            })
            .then(data => {
                const posSelect = document.getElementById('positionSelect');
                if (!posSelect) {
                    console.error('Position select not found');
                    return;
                }
                
                // Clear existing options except the first one
                while (posSelect.options.length > 1) {
                    posSelect.remove(1);
                }
                
                // Add position options filtered by department
                if (data && Array.isArray(data) && data.length > 0) {
                    data.forEach(pos => {
                        const option = document.createElement('option');
                        option.value = pos.PositionID;
                        option.textContent = pos.PositionName;
                        posSelect.appendChild(option);
                    });
                    
                    // Set selected value
                    if (selectedPosId) {
                        posSelect.value = selectedPosId;
                        console.log('✓ Position set to:', posSelect.value);
                    }
                }
            })
            .catch(error => console.error('Error loading positions:', error));
    }

    // Function to save employee changes
    function saveEmployeeChanges(employeeId) {
        console.log('Saving employee:', employeeId);
        
        // Find the modal overlay (the dynamically created one)
        const overlay = document.getElementById('employeeModalOverlay');
        if (!overlay) {
            alert('Modal not found');
            console.error('Could not find modal overlay');
            return;
        }
        
        // Find the form WITHIN the overlay
        const form = overlay.querySelector('#editEmployeeForm');
        if (!form) {
            alert('Form not found in modal');
            console.error('Could not find form with id editEmployeeForm in modal');
            return;
        }

        console.log('Form found:', form);

        // Check if dropdowns are loaded
        const deptSelect = document.getElementById('departmentSelect');
        const posSelect = document.getElementById('positionSelect');
        
        console.log('Department select found:', !!deptSelect, 'Options count:', deptSelect?.options.length);
        console.log('Position select found:', !!posSelect, 'Options count:', posSelect?.options.length);
        
        if (deptSelect && deptSelect.options.length <= 1) {
            alert('Department list is still loading. Please wait 2-3 seconds and try again.');
            return;
        }
        if (posSelect && posSelect.options.length <= 1) {
            alert('Position list is still loading. Please wait 2-3 seconds and try again.');
            return;
        }

        // Get current form values - search WITHIN the form, not the entire document
        const firstNameInput = form.querySelector('input[name="firstName"]');
        const middleNameInput = form.querySelector('input[name="middleName"]');
        const lastNameInput = form.querySelector('input[name="lastName"]');
        const emailInput = form.querySelector('input[name="email"]');
        const phoneInput = form.querySelector('input[name="phone"]');
        const birthdateInput = form.querySelector('input[name="birthdate"]');
        const statusSelect = form.querySelector('select[name="status"]');
        const dateHiredInput = form.querySelector('input[name="dateHired"]');
        const departmentSelect = form.querySelector('#departmentSelect');
        const positionSelect = form.querySelector('#positionSelect');

        console.log('Email input element:', emailInput);
        console.log('Email input value:', emailInput?.value);
        console.log('Birthdate input element:', birthdateInput);
        console.log('Birthdate input value:', birthdateInput?.value);

        const currentValues = {
            firstName: firstNameInput?.value || '',
            middleName: middleNameInput?.value || '',
            lastName: lastNameInput?.value || '',
            email: emailInput?.value || '',
            phone: phoneInput?.value || '',
            birthdate: birthdateInput?.value || '',
            status: statusSelect?.value || '',
            dateHired: dateHiredInput?.value || '',
            department: departmentSelect?.value || '',
            position: positionSelect?.value || ''
        };

        console.log('Current form values:', currentValues);
        console.log('Email value details:', {
            raw: emailInput?.value,
            trimmed: emailInput?.value?.trim(),
            isEmpty: !emailInput?.value,
            type: typeof emailInput?.value
        });
        console.log('Birthdate value details:', {
            raw: birthdateInput?.value,
            trimmed: birthdateInput?.value?.trim(),
            isEmpty: !birthdateInput?.value,
            type: typeof birthdateInput?.value
        });
        console.log('All input elements found:', {
            firstName: !!firstNameInput,
            lastName: !!lastNameInput,
            email: !!emailInput,
            phone: !!phoneInput,
            birthdate: !!birthdateInput,
            status: !!statusSelect,
            dateHired: !!dateHiredInput,
            department: !!departmentSelect,
            position: !!positionSelect
        });

        // Get original values from data attributes (stored when modal opened)
        const originalValues = {
            firstName: form.getAttribute('data-original-firstName') || '',
            middleName: form.getAttribute('data-original-middleName') || '',
            lastName: form.getAttribute('data-original-lastName') || '',
            email: form.getAttribute('data-original-email') || '',
            phone: form.getAttribute('data-original-phone') || '',
            birthdate: form.getAttribute('data-original-birthdate') || '',
            status: form.getAttribute('data-original-status') || '',
            dateHired: form.getAttribute('data-original-dateHired') || '',
            department: form.getAttribute('data-original-department') || '',
            position: form.getAttribute('data-original-position') || ''
        };

        // Check if any values have changed
        const hasChanges = Object.keys(currentValues).some(key => currentValues[key] !== originalValues[key]);

        console.log('Has changes:', hasChanges);
        console.log('Current values:', currentValues);
        console.log('Original values:', originalValues);

        // Validate required fields
        console.log('Validation check - firstName:', currentValues.firstName, 'trimmed:', currentValues.firstName.trim());
        
        if (!currentValues.firstName || !currentValues.firstName.trim()) {
            alert('First Name is required');
            return;
        }
        if (!currentValues.lastName || !currentValues.lastName.trim()) {
            alert('Last Name is required');
            return;
        }
        
        // Email is REQUIRED - validate
        if (!currentValues.email || !currentValues.email.trim()) {
            alert('Email is required');
            return;
        }
        const emailTrimmed = currentValues.email.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;  // Check for valid email format
        if (!emailRegex.test(emailTrimmed)) {
            console.log('Email validation failed for:', emailTrimmed);
            alert('Please enter a valid email address (e.g., user@example.com)');
            return;
        }
        console.log('Email validation passed for:', emailTrimmed);
        
        if (!currentValues.phone || !currentValues.phone.trim()) {
            alert('Contact No is required');
            return;
        }
        
        // Birthdate is REQUIRED - validate and check age (18+)
        if (!currentValues.birthdate || !currentValues.birthdate.trim()) {
            alert('Birthdate is required');
            return;
        }
        
        // Calculate age from birthdate
        const birthDate = new Date(currentValues.birthdate);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age < 18) {
            alert('Employee must be at least 18 years old. Current age: ' + age + ' years');
            return;
        }
        console.log('Age validation passed. Employee age: ' + age + ' years');
        
        if (!currentValues.department || currentValues.department === '') {
            alert('Department is required');
            return;
        }
        if (!currentValues.position || currentValues.position === '') {
            alert('Position is required');
            return;
        }
        
        // Allow save even if no changes - just update with same values
        console.log('All validations passed. Proceeding with save...');

        // Create FormData and add all form fields with error checking
        const formData = new FormData();

        // Add all form inputs with safe retrieval
        // Use correct field names that match the backend expectations
        formData.append('EmployeeID', employeeId);
        formData.append('FirstName', currentValues.firstName.trim());
        formData.append('MiddleName', currentValues.middleName.trim());
        formData.append('LastName', currentValues.lastName.trim());
        formData.append('Email', currentValues.email.trim());
        formData.append('Phone', currentValues.phone.trim());
        formData.append('Birthdate', currentValues.birthdate.trim());
        formData.append('status', currentValues.status.trim());
        formData.append('DateHired', currentValues.dateHired.trim());
        formData.append('department', currentValues.department);
        formData.append('position', currentValues.position);

        console.log('FormData being sent:', {
            id: employeeId,
            firstName: currentValues.firstName,
            lastName: currentValues.lastName,
            email: currentValues.email,
            birthdate: currentValues.birthdate,
            phone: currentValues.phone,
            dateHired: currentValues.dateHired,
            department: currentValues.department,
            position: currentValues.position,
            status: currentValues.status,
            hasChanges: hasChanges
        });
        
        // Log all FormData entries
        console.log('All FormData entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: "${value}"`);
        }

        // Show loading alert
        Swal.fire({
            title: 'Saving Changes...',
            html: 'Please wait while we update the employee information.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('update_employee.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            // Try to parse as JSON, if fails, return text
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        return { success: false, error: text };
                    }
                });
            }
        })
        .then(data => {
            console.log('Response data:', data);
            console.log('Save success:', data.success);
            console.log('Save error:', data.error);
            if (data.success) {
                // Show success alert
                Swal.fire({
                    title: 'Success!',
                    text: 'Employee information has been updated successfully.',
                    icon: 'success',
                    confirmButtonColor: '#0052CC',
                    confirmButtonText: 'OK'
                }).then(() => {
                    console.log('✅ Employee saved! Refreshing employee list...');
                    closeEmployeeModal();
                    // Reload only the employees table without changing page
                    if (typeof updateEmployeeTable === 'function') {
                        updateEmployeeTable();
                    }
                });
            } else {
                // Show error alert
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'Failed to update employee',
                    icon: 'error',
                    confirmButtonColor: '#0052CC',
                    confirmButtonText: 'OK'
                });
                console.error('❌ Save failed:', data.error);
            }
        })
        .catch(error => {
            console.error('Error saving employee:', error);
            Swal.fire({
                title: 'Error',
                text: 'Failed to save changes: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#0052CC',
                confirmButtonText: 'OK'
            });
        });
    }

    // Function to show employee details in a modal
    function showEmployeeModal(employee) {
        const modalContent = `
            <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); padding: 0; border-radius: 20px; max-width: 900px; width: 95%; margin: 20px auto; max-height: 90vh; overflow-y: auto; position: relative; z-index: 1001; box-shadow: 0 25px 80px rgba(0, 82, 204, 0.2); border: 1px solid rgba(0, 82, 204, 0.1);">
                <!-- Header with gradient and profile section -->
                <div style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); padding: 40px 30px; border-radius: 20px 20px 0 0; position: sticky; top: 0; z-index: 10;">
                    <div style="display: flex; align-items: center; gap: 25px;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 50px; color: white; border: 4px solid rgba(255,255,255,0.4); box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
                            👤
                        </div>
                        <div style="color: white; flex: 1;">
                            <h2 style="margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">${employee.FirstName} ${employee.LastName}</h2>
                            <p style="margin: 12px 0 0 0; font-size: 15px; opacity: 0.95; font-weight: 500;">${employee.PositionName || 'Position'}</p>
                            <p style="margin: 4px 0 0 0; font-size: 14px; opacity: 0.85;">${employee.DepartmentName || 'Department'}</p>
                        </div>
                    </div>
                </div>

                <!-- Content sections with improved spacing -->
                <div style="padding: 40px 30px;">
                    <!-- Personal Information Section -->
                    <div style="margin-bottom: 40px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                            <div style="width: 4px; height: 24px; background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); border-radius: 2px;"></div>
                            <h3 style="color: #0052CC; font-size: 18px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Personal Information</h3>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div style="background: linear-gradient(135deg, rgba(0, 82, 204, 0.08) 0%, rgba(0, 82, 204, 0.02) 100%); padding: 20px; border-radius: 12px; border-left: 5px solid #0052CC; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 82, 204, 0.08);">
                                <p style="margin: 0 0 8px 0; font-size: 11px; color: #0052CC; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Email</p>
                                <p style="margin: 0; font-size: 15px; color: #333; word-break: break-all; font-weight: 500;">${employee.Email || 'N/A'}</p>
                            </div>
                            <div style="background: linear-gradient(135deg, rgba(0, 82, 204, 0.08) 0%, rgba(0, 82, 204, 0.02) 100%); padding: 20px; border-radius: 12px; border-left: 5px solid #0052CC; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 82, 204, 0.08);">
                                <p style="margin: 0 0 8px 0; font-size: 11px; color: #0052CC; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Phone</p>
                                <p style="margin: 0; font-size: 15px; color: #333; font-weight: 500;">${employee.ContactNo || 'N/A'}</p>
                            </div>
                            <div style="background: linear-gradient(135deg, rgba(0, 82, 204, 0.08) 0%, rgba(0, 82, 204, 0.02) 100%); padding: 20px; border-radius: 12px; border-left: 5px solid #0052CC; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 82, 204, 0.08);">
                                <p style="margin: 0 0 8px 0; font-size: 11px; color: #0052CC; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Birthdate</p>
                                <p style="margin: 0; font-size: 15px; color: #333; font-weight: 500;">${employee.Birthdate || 'N/A'}</p>
                            </div>
                            <div style="background: linear-gradient(135deg, rgba(0, 82, 204, 0.08) 0%, rgba(0, 82, 204, 0.02) 100%); padding: 20px; border-radius: 12px; border-left: 5px solid #0052CC; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 82, 204, 0.08);">
                                <p style="margin: 0 0 8px 0; font-size: 11px; color: #0052CC; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Age</p>
                                <p style="margin: 0; font-size: 15px; color: #333; font-weight: 500;">${employee.Age || 'N/A'} years</p>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Information Section -->
                    <div style="margin-bottom: 40px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                            <div style="width: 4px; height: 24px; background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); border-radius: 2px;"></div>
                            <h3 style="color: #4CAF50; font-size: 18px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Employment Information</h3>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(76, 175, 80, 0.02) 100%); padding: 20px; border-radius: 12px; border-left: 5px solid #4CAF50; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.08);">
                                <p style="margin: 0 0 8px 0; font-size: 11px; color: #4CAF50; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Status</p>
                                <p style="margin: 0; font-size: 15px; color: #333;">
                                    <span style="display: inline-block; background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 6px 16px; border-radius: 25px; font-size: 13px; font-weight: 700; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);">
                                        ${employee.Status || 'N/A'}
                                    </span>
                                </p>
                            </div>
                            <div style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.08) 0%, rgba(33, 150, 243, 0.02) 100%); padding: 20px; border-radius: 12px; border-left: 5px solid #2196F3; box-shadow: 0 2px 8px rgba(33, 150, 243, 0.08);">
                                <p style="margin: 0 0 8px 0; font-size: 11px; color: #2196F3; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Date Hired</p>
                                <p style="margin: 0; font-size: 15px; color: #333; font-weight: 500;">${employee.DateHired || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer with close button -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding: 25px 30px; border-top: 2px solid rgba(0, 82, 204, 0.1); background: linear-gradient(135deg, rgba(0, 82, 204, 0.03) 0%, rgba(0, 82, 204, 0.01) 100%); position: sticky; bottom: 0; z-index: 10;">
                    <button type="button" onclick="closeEmployeeModal()" style="background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%); color: white; padding: 14px 32px; border: none; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 15px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3); text-transform: uppercase; letter-spacing: 0.5px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 82, 204, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 82, 204, 0.3)'">Close</button>
                </div>
            </div>
        `;
        
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.id = 'employeeModalOverlay';
        overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
        overlay.innerHTML = modalContent;
        
        document.body.appendChild(overlay);
        
        // Add event listener to close button
        const closeBtn = overlay.querySelector('button');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeEmployeeModal();
            });
        }
        
        // Close on overlay click (but not on modal content)
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeEmployeeModal();
            }
        });
    }

    // ===== GLOBAL SCOPE FUNCTIONS =====
    // These functions are defined outside DOMContentLoaded so they can be called from HTML onclick handlers
    
    // ===== DEPARTMENT MODAL FUNCTIONS =====
    function closeEditDepartmentModal() {
        const modal = document.getElementById('editDepartmentModal');
        if (modal) modal.style.display = 'none';
    }
    
    function closeDeleteDepartmentModal() {
        const modal = document.getElementById('deleteDepartmentModal');
        if (modal) modal.style.display = 'none';
    }
    
    // ===== POSITION MODAL FUNCTIONS =====
    function closeEditPositionModal() {
        const modal = document.getElementById('editPositionModal');
        if (modal) modal.style.display = 'none';
    }
    
    function closeDeletePositionModal() {
        const modal = document.getElementById('deletePositionModal');
        if (modal) modal.style.display = 'none';
    }
    
    // ===== ADD DEPARTMENT/POSITION MODAL FUNCTIONS =====
    function closeAddDepartmentModal() {
        const modal = document.getElementById('addDepartmentModal');
        if (modal) modal.style.display = 'none';
    }
    
    function closeAddPositionModal() {
        const modal = document.getElementById('addPositionModal');
        if (modal) modal.style.display = 'none';
    }
    
    // Function to close employee modal
    function closeEmployeeModal() {
        const overlay = document.getElementById('employeeModalOverlay');
        if (overlay) {
            overlay.remove();
        }
    }

    // Function to edit employee from view modal
    function editEmployeeFromView(employeeId) {
        closeEmployeeModal();
        editEmployee(employeeId);
    }

    // Function to archive employee
    function archiveEmployee(employeeId) {
        console.log('Archive employee:', employeeId);
        
        if (!employeeId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid employee ID',
                confirmButtonColor: '#0052CC',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        Swal.fire({
            icon: 'warning',
            title: 'Archive Employee?',
            text: 'Are you sure you want to archive this employee? This action can be undone.',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading alert
                Swal.fire({
                    title: 'Archiving Employee',
                    html: 'Please wait...',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send archive request via POST
                const formData = new FormData();
                formData.append('EmployeeID', employeeId);
                
                fetch('archive_employee.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Employee Archived',
                            text: 'The employee has been successfully archived.',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload employees list
                            if (typeof updateEmployeeTable === 'function') {
                                updateEmployeeTable();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Failed to archive employee.',
                            confirmButtonColor: '#0052CC',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while archiving the employee.',
                        confirmButtonColor: '#0052CC',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }
    
    // Filter phone number inputs to only allow numbers
    document.addEventListener('input', function(e) {
        if (e.target.name === 'contact_no' || e.target.name === 'emergency_contact_no') {
            // Remove any non-numeric characters
            e.target.value = e.target.value.replace(/[^\d]/g, '');
            // Limit to 11 digits
            if (e.target.value.length > 11) {
                e.target.value = e.target.value.slice(0, 11);
            }
        }
    });
    
    // Reset wizard when modal is closed
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('close') && e.target.closest('#addEmployeeModal')) {
            currentStep = 1;
            updateWizardDisplay();
        }
    });

    // ===== TRAVEL MANAGEMENT FUNCTIONS =====
    window.loadManageTravelOrders = function() {
        fetch('fetch_travel_orders.php?status=Pending')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('manage-travel-tbody');
                if (!tbody) return;
                
                if (!data || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#666;">No pending travel orders</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.map(row => `
                    <tr>
                        <td>${row.ControlNumber || 'N/A'}</td>
                        <td>${row.FirstName} ${row.LastName}</td>
                        <td>${row.Destination}</td>
                        <td>${row.Purpose}</td>
                        <td>${new Date(row.StartDate).toLocaleDateString()}</td>
                        <td>${new Date(row.EndDate).toLocaleDateString()}</td>
                        <td><span style="background:#ffc107;padding:4px 8px;border-radius:4px;color:#000;">${row.Status}</span></td>
                        <td style="display:flex;gap:8px;justify-content:center;">
                            <button class="approve-travel-btn" data-id="${row.TravelID}" title="Approve" style="background:none;border:none;padding:8px;cursor:pointer;color:#28a745;font-size:18px;">✓</button>
                            <button class="reject-travel-btn" data-id="${row.TravelID}" title="Reject" style="background:none;border:none;padding:8px;cursor:pointer;color:#dc3545;font-size:18px;">✕</button>
                            <button class="view-travel-btn" data-id="${row.TravelID}" title="View" style="background:none;border:none;padding:8px;cursor:pointer;color:#007bff;font-size:18px;">👁️</button>
                        </td>
                    </tr>
                `).join('');
                
                attachTravelButtonListeners();
            })
            .catch(err => console.error('Failed to load travel orders', err));
    };

    window.loadOngoingTravels = function() {
        fetch('fetch_travel_orders.php?status=Approved')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('ongoing-travel-tbody');
                if (!tbody) return;
                
                // Filter to show only travels where EndDate is today or in the future
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const ongoingData = data.filter(row => {
                    const endDate = new Date(row.EndDate);
                    endDate.setHours(0, 0, 0, 0);
                    return endDate >= today;
                });
                
                if (!ongoingData || ongoingData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#666;">No ongoing/upcoming travels</td></tr>';
                    return;
                }
                
                tbody.innerHTML = ongoingData.map(row => `
                    <tr>
                        <td>${row.ControlNumber || 'N/A'}</td>
                        <td>${row.FirstName} ${row.LastName}</td>
                        <td>${row.Destination}</td>
                        <td>${row.Purpose}</td>
                        <td>${new Date(row.StartDate).toLocaleDateString()}</td>
                        <td>${new Date(row.EndDate).toLocaleDateString()}</td>
                        <td><span style="background:#28a745;padding:4px 8px;border-radius:4px;color:#fff;">${row.Status}</span></td>
                        <td style="display:flex;gap:8px;justify-content:center;">
                            <button class="view-travel-btn" data-id="${row.TravelID}" title="View" style="background:none;border:none;padding:8px;cursor:pointer;color:#007bff;font-size:18px;">👁️</button>
                        </td>
                    </tr>
                `).join('');
                
                attachTravelButtonListeners();
            })
            .catch(err => console.error('Failed to load ongoing travels', err));
    };

    window.loadTravelHistory = function() {
        console.log('loadTravelHistory called');
        fetch('fetch_travel_orders.php?status=Approved,Rejected')
            .then(res => res.json())
            .then(data => {
                console.log('Fetched travel data:', data);
                const tbody = document.getElementById('travel-history-tbody');
                if (!tbody) {
                    console.error('travel-history-tbody not found');
                    return;
                }
                
                // Filter to show only travels where EndDate is in the past
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                console.log('Today date:', today);
                
                const historyData = data.filter(row => {
                    const endDate = new Date(row.EndDate);
                    endDate.setHours(0, 0, 0, 0);
                    const isPast = endDate < today;
                    console.log(`Travel ${row.ControlNumber}: EndDate=${endDate}, isPast=${isPast}`);
                    return isPast;
                });
                
                console.log('Filtered history data:', historyData);
                
                if (!historyData || historyData.length === 0) {
                    console.log('No history data found');
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#666;">No travel history</td></tr>';
                    return;
                }
                
                tbody.innerHTML = historyData.map(row => `
                    <tr>
                        <td>${row.ControlNumber || 'N/A'}</td>
                        <td>${row.FirstName} ${row.LastName}</td>
                        <td>${row.Destination}</td>
                        <td>${row.Purpose}</td>
                        <td>${new Date(row.StartDate).toLocaleDateString()}</td>
                        <td>${new Date(row.EndDate).toLocaleDateString()}</td>
                        <td><span style="background:${row.Status === 'Approved' ? '#28a745' : '#dc3545'};padding:4px 8px;border-radius:4px;color:#fff;">${row.Status}</span></td>
                        <td style="display:flex;gap:8px;justify-content:center;">
                            <button class="view-travel-btn" data-id="${row.TravelID}" title="View" style="background:none;border:none;padding:8px;cursor:pointer;color:#007bff;font-size:18px;">👁️</button>
                        </td>
                    </tr>
                `).join('');
                
                console.log('Travel history rendered');
                attachTravelButtonListeners();
            })
            .catch(err => console.error('Failed to load travel history', err));
    };

    function attachTravelButtonListeners() {
        // Approve travel
        document.querySelectorAll('.approve-travel-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Approve Travel Order?',
                    text: 'Are you sure you want to approve this travel order?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('approve_travel.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Approved!', 'Travel order approved successfully.', 'success');
                                loadManageTravelOrders();
                            } else {
                                Swal.fire('Error', data.error || 'Failed to approve', 'error');
                            }
                        })
                        .catch(err => Swal.fire('Error', 'Network error', 'error'));
                    }
                });
            });
        });

        // Reject travel
        document.querySelectorAll('.reject-travel-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Reject Travel Order?',
                    text: 'Are you sure you want to reject this travel order?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('reject_travel.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Rejected!', 'Travel order rejected.', 'success');
                                loadManageTravelOrders();
                            } else {
                                Swal.fire('Error', data.error || 'Failed to reject', 'error');
                            }
                        })
                        .catch(err => Swal.fire('Error', 'Network error', 'error'));
                    }
                });
            });
        });

        // View travel
        document.querySelectorAll('.view-travel-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                window.open('fetch_travel_order_content.php?id=' + encodeURIComponent(id), '_blank', 'width=900,height=700');
            });
        });
    }

    // Load travel data when sections are shown
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                const target = mutation.target;
                if (target.id === 'manage-travel-section' && target.style.display !== 'none') {
                    loadManageTravelOrders();
                } else if (target.id === 'ongoing-travel-section' && target.style.display !== 'none') {
                    loadOngoingTravels();
                } else if (target.id === 'travel-history-section' && target.style.display !== 'none') {
                    loadTravelHistory();
                }
            }
        });
    });

    const sections = document.querySelectorAll('[id$="-travel-section"]');
    console.log('Found travel sections:', sections.length);
    sections.forEach(section => {
        console.log('Observing section:', section.id);
        observer.observe(section, { attributes: true, attributeFilter: ['style'] });
    });
    
    // Also add direct click handlers for menu buttons
    document.querySelectorAll('.menu-btn[data-target="travel-history-section"]').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Travel History button clicked');
            setTimeout(() => loadTravelHistory(), 100);
        });
    });

    // ===== ASSIGN TRAVEL MODAL HANDLERS =====
    // Load employees into the dropdown
    window.loadEmployeesForAssignTravel = function() {
        console.log('Loading employees for assign travel...');
        fetch('fetch_employees_list.php')
            .then(res => res.json())
            .then(data => {
                console.log('Employees loaded:', data);
                const select = document.getElementById('assignTravelEmployee');
                if (!select) {
                    console.error('Employee select not found');
                    return;
                }
                
                select.innerHTML = '<option value="">-- Select an Employee --</option>';
                if (data && Array.isArray(data)) {
                    data.forEach(emp => {
                        const option = document.createElement('option');
                        option.value = emp.EmployeeID;
                        option.textContent = `${emp.FirstName} ${emp.LastName} (${emp.DepartmentName || 'N/A'})`;
                        select.appendChild(option);
                    });
                }
            })
            .catch(err => console.error('Failed to load employees', err));
    };


    });
</script>

</html>