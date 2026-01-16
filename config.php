<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'attachmentmanagementsystem');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Redirect to login if not logged in
function requireLogin($userType) {
    if (!isLoggedIn() || $_SESSION['user_type'] !== $userType) {
        header("Location: ../Login Pages/" . $userType . "-login.html");
        exit();
    }
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Hash password using PHP's password_hash (bcrypt)
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password against hash
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>
