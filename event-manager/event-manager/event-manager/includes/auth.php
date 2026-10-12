<?php
session_start();

// Redirect to login if not authenticated
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user has permission to access this page
$allowed_roles = ['admin', 'manager', 'user']; // Default all roles
if(isset($required_role) && !in_array($_SESSION['role'], $required_role)) {
    header("Location: ../unauthorized.php");
    exit();
}
?>