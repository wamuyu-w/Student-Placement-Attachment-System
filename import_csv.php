<?php
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

    $handle = fopen(__DIR__ . '/test_students.csv', "r");
    $row = 0;
    
    $insUser = $conn->prepare("INSERT INTO users (Username, Password, Role) VALUES (?, ?, 'Student')");
    $insStudent = $conn->prepare("INSERT INTO student (UserID, FirstName, LastName, Course, Faculty, YearOfStudy, EligibilityStatus) VALUES (?, ?, ?, 'BSc Computer Science', 'Science', 3, 'Eligible')");

    $successCount = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        if ($row == 1) continue; // Skip header

        if (count($data) < 3) continue;

        $adm = trim($data[0]);
        $first = trim($data[1]);
        $last = trim($data[2]);

        // Check if exists
        $chk = $conn->query("SELECT UserID FROM users WHERE Username = '$adm'");
        if ($chk->num_rows > 0) continue;

        $hashed = password_hash('Changeme123!', PASSWORD_DEFAULT);
        $insUser->bind_param("ss", $adm, $hashed);
        $insUser->execute();
        $userId = $conn->insert_id;

        $insStudent->bind_param("iss", $userId, $first, $last);
        $insStudent->execute();
        
        $successCount++;
    }
    
    fclose($handle);
    echo "Bulk upload simulated successfully. Added $successCount new students.\n";

} catch (\Exception $e) {
    echo "Error inserting CSV: " . $e->getMessage() . "\n";
}
