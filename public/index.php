<?php
// public/index.php

// Session configuration
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
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