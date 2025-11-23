<?php
/**
 * Database Connection for HR Leave Management System
 * Uses centralized configuration from config.php
 */

// Load configuration
require_once(__DIR__ . '../../config.php');

// Create connection using constants from config.php
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Log error securely in production
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed. Please contact the administrator.");
}

// Set charset to prevent SQL injection
$conn->set_charset("utf8mb4");
?>