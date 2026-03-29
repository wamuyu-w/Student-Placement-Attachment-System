<?php
require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace('\\', '/', substr($className, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
});

use App\Config\Database;
$db = new Database();
$conn = $db->connect();

$res = $conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization WHERE Email='michellewachira25@gmail.com'");
echo "Hosts created for Michelle: " . $res->num_rows . "\n";

$res2 = $conn->query("SELECT ApplicationStatus, FinancialClearanceStatus, COUNT(*) as c FROM attachmentapplication GROUP BY ApplicationStatus, FinancialClearanceStatus");
while($row = $res2->fetch_assoc()) {
    echo "App Status: {$row['ApplicationStatus']} | Finance: {$row['FinancialClearanceStatus']} | Count: {$row['c']}\n";
}
