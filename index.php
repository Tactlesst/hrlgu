<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: Pages/Admin-Dashboard.php');
    } else {
        header('Location: Pages/Employee-Dashboard.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRLGU - Leave Management System</title>
    <link rel="icon" type="image/x-icon" href="Pictures/logo.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 82, 204, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
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

        .logo-section {
            margin-bottom: 30px;
        }

        .logo-section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%);
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0, 82, 204, 0.2);
        }

        h1 {
            color: #0052CC;
            font-size: 32px;
            margin: 20px 0 10px 0;
            font-weight: 700;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 40px 0;
            text-align: left;
        }

        .feature {
            padding: 20px;
            background: rgba(0, 82, 204, 0.05);
            border-radius: 12px;
            border-left: 4px solid #0052CC;
            transition: all 0.3s ease;
        }

        .feature:hover {
            background: rgba(0, 82, 204, 0.1);
            transform: translateY(-5px);
        }

        .feature i {
            color: #0052CC;
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        .feature h3 {
            color: #0052CC;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .feature p {
            color: #666;
            font-size: 12px;
            line-height: 1.4;
        }

        .cta-section {
            margin: 40px 0 0 0;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }

        .login-btn {
            display: inline-block;
            background: linear-gradient(135deg, #0052CC 0%, #003DA5 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 82, 204, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 24px;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .logo-section img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <img src="Pictures/logo.png" alt="HRLGU Logo">
        </div>

        <h1>HRLGU</h1>
        <p class="subtitle">Leave Management System</p>

        <div class="features">
            <div class="feature">
                <i class="fas fa-calendar-check"></i>
                <h3>Leave Management</h3>
                <p>Manage and track employee leave requests efficiently</p>
            </div>
            <div class="feature">
                <i class="fas fa-users"></i>
                <h3>Employee Management</h3>
                <p>Organize and manage employee information</p>
            </div>
            <div class="feature">
                <i class="fas fa-map-location-dot"></i>
                <h3>Travel Orders</h3>
                <p>Track and manage travel order requests</p>
            </div>
            <div class="feature">
                <i class="fas fa-chart-bar"></i>
                <h3>Analytics</h3>
                <p>View detailed reports and statistics</p>
            </div>
        </div>

        <div class="cta-section">
            <a href="Pages/Login.php" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login Now
            </a>
        </div>

        <div class="footer">
            <p>&copy; 2025 HRLGU Leave Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
