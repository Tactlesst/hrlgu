<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // --- 1. Check Admin Table (Once) ---
    // ASSUMPTION: Your 'Admin' table MUST have a 'Role' column.
    $sql = "SELECT AdminID AS id, Username AS username, Password AS password, Role AS role 
            FROM Admin 
            WHERE Username = ? 
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error (Admin): " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows === 1) {
        // --- Admin was found ---
        $row = $adminResult->fetch_assoc();
        
        // !! SECURITY WARNING: Store admin passwords with password_hash()
        // Replace this plain-text check with password_verify()
        if ($password === $row['password']) { 
            
            // Set session variables from the database
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // Get the role from the DB

            // Redirect based on the role we found
            if ($row['role'] == 'super_admin') {
                header("Location: /hrlgu/Pages/super_admin_dashboard.php");
                exit();
            } else if ($row['role'] == 'admin') {
                header("Location: /hrlgu/Pages/Admin-Dashboard.php");
                exit();
            } else {
                // Handle a case where the role is something else
                echo "<script>alert('Error: Unknown admin role.'); window.location.href='/hrlgu/Pages/Login.php';</script>";
                exit();
            }

        }
        // Admin password was wrong. We continue on to check the Employee table.

    }
    $stmt->close();


    // --- 2. Check Employee Table ---
    // We only run this if NO admin account was found with that username
    $sql = "SELECT EmployeeID AS id, Email AS username, Password AS password, 'employee' AS role 
            FROM Employee 
            WHERE Email = ? 
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error (Employee): " . $conn->error);
    }
    
    $stmt->bind_param("s", $username); // Employees log in with email
    $stmt->execute();
    $empResult = $stmt->get_result();

    if ($empResult->num_rows === 1) {
        // --- Employee was found ---
        $row = $empResult->fetch_assoc();

        // Your employee password check is correct!
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: /hrlgu/Pages/Employee-Dashboard.php");
            exit();
        }
    }
    $stmt->close();
    $conn->close();

    // --- 3. Final Failure ---
    // If the code reaches this point, no user was found OR the password was wrong.
    echo "<script>alert('Wrong Username or Password'); window.location.href='/hrlgu/Pages/Login.php';</script>";
    exit();
}
?>

<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Human Resource Leave Management Login</title>
        <link rel="icon" type="image/x-icon" href="../Pictures/logo.ico">
    <link rel="stylesheet" href="../CSS/Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body>
    <div class="bg-img">
        <div class="content">
            <header>Login</header>
            <form action="/hrlgu/Pages/Login.php" method="POST">
                <div class="field">
                    <span class="fa fa-user"></span>
                    <input type="text" name="username" required placeholder="Email or Phone">
                </div>
                <div class="field space">
                    <span class="fa fa-lock"></span>
                    <input type="password" name="password" class="pass-key" required placeholder="Password">
                    <span class="show">SHOW</span>
                </div>
                <div class="pass">
                    <a href="#">Forget Password</a>
                </div>
                <div class="field">
                    <input type="submit" value="LOGIN">
                </div>
            </form>
        </div>
    </div>
    <script>
        const pass_field = document.querySelector('.pass-key');
        const showBtn = document.querySelector('.show');
        showBtn.addEventListener('click', function () {
            if (pass_field.type === "password") {
                pass_field.type = "text";
                showBtn.textContent = "HIDE";
                showBtn.style.color = "#3498db";
            } else {
                pass_field.type = "password";
                showBtn.textContent = "SHOW";
                showBtn.style.color = "#222";
            }
        });
    </script>
</body>

</html>