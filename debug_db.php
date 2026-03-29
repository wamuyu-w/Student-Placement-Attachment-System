<?php
// Let's debug why it's hanging or what's happening.
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace('\\', '/', substr($className, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
});

use App\Config\Database;

try {
    $db = new Database();
    $conn = $db->connect();

    $res = $conn->query("SELECT COUNT(*) as c FROM student");
    $rowCount = $res->fetch_assoc()['c'];
    echo "Total students: $rowCount\n";
    
    $res2 = $conn->query("SELECT COUNT(*) as c FROM attachmentapplication");
    $appCount = $res2->fetch_assoc()['c'];
    echo "Total applications: $appCount\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
