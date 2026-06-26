<?php
// public/index.php


// set the session to 10 minutes - security purposes
ini_set('session.gc_maxlifetime', 1200);
session_set_cookie_params([
    'lifetime' => 1200, // 20 minutes
    'httponly' => true,
    'samesite' => 'Lax',
]);

// this is for testing purposes to allow for concurrent tabs to be open
$uri = $_SERVER['REQUEST_URI'];
$role = '';


// this is to show which route the user is on and to accomodate showing different functionality across multiple users
//this should be removed during production
if (strpos($uri, '/auth/forgot-password') !== false || strpos($uri, '/auth/reset-password') !== false) {
    $role = ''; // Force default session for password reset flow to avoid CSRF mismatch
} elseif (strpos($uri, '/admin') !== false || strpos($uri, '/staff') !== false) {
    $role = 'STAFF';
} elseif (strpos($uri, '/host') !== false) {
    $role = 'HOST';
} elseif (strpos($uri, '/student') !== false) {
    $role = 'STUDENT';
} elseif (isset($_POST['user_type']) || isset($_POST['role'])) {
    $ut = $_POST['user_type'] ?? $_POST['role'];
    if ($ut === 'staff' || $ut === 'admin') $role = 'STAFF';
    elseif ($ut === 'host_org') $role = 'HOST';
    elseif ($ut === 'student') $role = 'STUDENT';
} elseif (isset($_GET['role'])) {
    $ut = $_GET['role'];
    if ($ut === 'staff' || $ut === 'admin') $role = 'STAFF';
    elseif ($ut === 'host_org') $role = 'HOST';
    elseif ($ut === 'student') $role = 'STUDENT';
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    if (strpos($ref, '/admin') !== false || strpos($ref, '/staff') !== false) $role = 'STAFF';
    elseif (strpos($ref, '/host') !== false) $role = 'HOST';
    elseif (strpos($ref, '/student') !== false) $role = 'STUDENT';
}

if ($role) {
    session_name('SESS_' . $role);
}

session_start();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Composer autoloader (for libraries such as PHPMailer)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Simple PSR‑4 style autoloader for our app
spl_autoload_register(function ($className) {
    // Convert namespace to file path, e.g. App\Controllers\HomeController → ../app/Controllers/HomeController.php
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) return;
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

// Dispatch the request
$router = new \App\Core\Router();
$router->dispatch();