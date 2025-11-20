<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruit') {
    header("Location: Login.php");
    exit();
}
?>