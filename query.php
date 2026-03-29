<?php
require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') === 0) {
        require_once __DIR__ . '/app/' . str_replace('\\', '/', substr($className, 4)) . '.php';
    }
});
$db = new \App\Config\Database();
$conn = $db->connect();
$res = $conn->query("SELECT StudentID, FirstName, LastName, EligibilityStatus FROM student LIMIT 10");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
