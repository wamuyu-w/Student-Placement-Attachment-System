<?php
require_once __DIR__ . '/../app/Config/Database.php';
$db = new \App\Config\Database();
$conn = $db->connect();

$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo "Table: " . $row[0] . "\n";
    $cols = $conn->query("SHOW COLUMNS FROM `" . $row[0] . "`");
    while ($col = $cols->fetch_assoc()) {
        echo "  - " . $col['Field'] . "\n";
    }
}
