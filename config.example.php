<?php
/**
 * Database Configuration EXAMPLE for HR Leave Management System
 * 
 * INSTRUCTIONS:
 * 1. Copy this file and rename it to 'config.php'
 * 2. Update the PRODUCTION SETTINGS with your Hostinger credentials
 * 3. Never commit config.php to version control (it's in .gitignore)
 */

// Check if we're in production (Hostinger) or development (XAMPP)
$is_production = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false) 
                 || (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'hostinger') !== false)
                 || file_exists(__DIR__ . '/.production');

if ($is_production) {
    // PRODUCTION SETTINGS (Hostinger)
    // Update these with your Hostinger database credentials
    define('DB_SERVER', 'localhost'); // Usually 'localhost' or provided by Hostinger
    define('DB_USERNAME', 'your_hostinger_db_username'); // Replace with your database username
    define('DB_PASSWORD', 'your_hostinger_db_password'); // Replace with your database password
    define('DB_NAME', 'your_hostinger_db_name'); // Replace with your database name
} else {
    // DEVELOPMENT SETTINGS (XAMPP)
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', ''); // Your XAMPP MySQL password
    define('DB_NAME', 'HRMS');
}

// Additional configuration
define('UPLOAD_PATH', __DIR__ . '/Documents/');
define('PICTURES_PATH', __DIR__ . '/Pictures/');

// Error reporting (disable in production)
if ($is_production) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>
