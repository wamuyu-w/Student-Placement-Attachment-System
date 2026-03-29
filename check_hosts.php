<?php
require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') === 0) {
        require_once __DIR__ . '/app/' . str_replace('\\', '/', substr($className, 4)) . '.php';
    }
});
$db = new \App\Core\Database();
$conn = $db->connect();
$result = $conn->query("SELECT count(*) as count FROM hostorganization WHERE Email='michellewachira25@gmail.com'");
$row = $result->fetch_assoc();
echo "Generated Hosts count: " . $row['count'] . "\n";
