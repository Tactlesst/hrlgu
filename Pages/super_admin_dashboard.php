<?php
// Start the session to check for super_admin
session_start();
include 'db_connect.php'; // 1. CONNECT TO THE DATABASE

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: /login.php'); // Redirect to your login page
    exit;
}

// 2. FETCH ADMINS FROM DATABASE
$admins_from_db = [];

/* FIXED: This query no longer selects 'DateCreated' and orders by Username instead */
$sql = "SELECT AdminID AS id, Username AS username 
        FROM Admin 
        WHERE Role = 'admin' 
        ORDER BY Username ASC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $admins_from_db[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
        <link rel="icon" type="image/x-icon" href="/Pictures/logo.ico">
    
    <style>
        :root {
            --primary-color: #0056b3;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --light-grey: #f4f7f6;
            --dark-grey: #555;
            --border-color: #ddd;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: var(--light-grey);
            color: var(--dark-grey);
            line-height: 1.6;
        }
        .header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        .logout-btn {
            text-decoration: none;
            color: var(--danger-color);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border: 1px solid var(--danger-color);
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background-color: var(--danger-color);
            color: #fff;
        }
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }
        .btn-create {
            background-color: var(--success-color);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-create:hover {
            background-color: #218838;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .admin-table th,
        .admin-table td {
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            text-align: left;
        }
        .admin-table th {
            background-color: #f9f9f9;
        }
        .admin-table tr:hover {
            background-color: #f1f1f1;
        }
        .btn-delete {
            background-color: var(--danger-color);
            color: #fff;
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .modal {
            display: none; 
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); 
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .modal-close {
            color: #aaa;
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
        }
        .modal-close:hover {
            color: #333;
        }
        .modal-form div {
            margin-bottom: 1rem;
        }
        .modal-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .modal-form input[type="text"],
        .modal-form input[type="email"],
        .modal-form input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            box-sizing: border-box; 
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Super Admin Dashboard</h1>
        <a href="/Pages/Logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="container">
        
        <div class="dashboard-header">
            <h2>Manage Admins</h2>
            <button id="createAdminBtn" class="btn-create">Create New Admin</button>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="adminTableBody">
                <?php foreach ($admins_from_db as $admin): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['id']); ?></td>
                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                    <td>
                        <button class="btn-delete" data-id="<?php echo htmlspecialchars($admin['id']); ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($admins_from_db)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No admins found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <div id="createAdminModal" class="modal">
        <div class="modal-content">
            <span id="closeModalBtn" class="modal-close">&times;</span>
            <h2>Create New Admin</h2>
            <form id="createAdminForm" class="modal-form">
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-create">Save Admin</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            // --- Modal Control ---
            const modal = document.getElementById("createAdminModal");
            const createBtn = document.getElementById("createAdminBtn");
            const closeBtn = document.getElementById("closeModalBtn");

            createBtn.onclick = () => {
                modal.style.display = "block";
            }
            closeBtn.onclick = () => {
                modal.style.display = "none";
            }
            window.onclick = (event) => {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // --- Handle "Create Admin" Form Submission ---
            // --- Handle "Create Admin" Form Submission ---
            const createForm = document.getElementById("createAdminForm");
            createForm.addEventListener("submit", (event) => {
                event.preventDefault(); 
                const formData = new FormData(createForm);

                // **THE FIX:** Update this path to be correct.
                // I am guessing your Pages folder is inside an 'hrlgu' folder.
                // Adjust this to match your project's URL.
                fetch("/Pages/create_admin.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => {
                    // **NEW:** Add this check to see the error
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Admin created successfully!");
                        modal.style.display = "none";
                        createForm.reset();
                        location.reload(); 
                    } else {
                        alert("Error: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    // This error is more specific now
                    alert("A network or server error occurred: " + error.message);
                });
            });


            // --- Handle "Delete Admin" Button Clicks ---
            const adminTableBody = document.getElementById("adminTableBody");
            adminTableBody.addEventListener("click", (event) => {
                if (event.target.classList.contains("btn-delete")) {
                    
                    const adminId = event.target.dataset.id;
                    const row = event.target.closest("tr");
                    
                    if (confirm(`Are you sure you want to delete admin ID #${adminId}?`)) {
                        
                        // --- AJAX Call to your PHP script ---
                        // You must uncomment this block and use your 'delete_admin.php' file
                        
                        fetch("delete_admin.php", {
                            method: "POST",
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `id=${adminId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Admin deleted successfully!");
                                row.remove(); // Remove the row from the table
                            } else {
                                alert("Error: " + data.error);
                            }
                        })
                        .catch(error => {
                            console.error("Fetch error:", error);
                            alert("A network error occurred. Please try again.");
                        });
                        
                    }
                }
            });

        });
    </script>

</body>
</html>