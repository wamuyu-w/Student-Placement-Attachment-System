<?php
// public/index.php

session_start();

// Include Composer autoloader - this will be used for emails
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Autoloader (Simple version for manual structure)
spl_autoload_register(function ($className) {
    // Convert namespace to file path
    // App\Controllers\HomeController -> ../app/Controllers/HomeController.php
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) return;
    
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) require $file;
});

$router = new \App\Core\Router();
$router->dispatch();