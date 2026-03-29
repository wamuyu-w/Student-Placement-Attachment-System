<?php
// generate_test_placements.php

if (false) {
    die("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/vendor/autoload.php';

// Autoloader for App classes
spl_autoload_register(function ($className) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) return;
    
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) require $file;
});

use App\Config\Database;
use App\Core\Mailer;

try {
    $db = new Database();
    $conn = $db->connect();

    // 1. Get all students who do not have an attachment or an application
    $sql = "SELECT s.StudentID, s.FirstName, s.LastName 
            FROM student s 
            LEFT JOIN attachmentapplication aa ON s.StudentID = aa.StudentID 
            WHERE aa.ApplicationID IS NULL";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows === 0) {
        echo "No students found without a placement/application.\n";
        exit;
    }

    $financeStatuses = ['Cleared', 'Pending', 'Not Cleared'];
    $targetEmail = 'michellewachira25@gmail.com';

    echo "Found " . $result->num_rows . " students. Generating host organizations...\n";

    // Prepare statements outside loop for performance (except when fetching last username which we update in loop)
    $insertUser = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
    $insertHost = $conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email) VALUES (?, ?, ?, ?)");
    $insertApp = $conn->prepare("INSERT INTO attachmentapplication (StudentID, ApplicationDate, ApplicationStatus, IntendedHostOrg, HostOrgID, FinancialClearanceStatus) VALUES (?, CURDATE(), 'Pending', ?, ?, ?)");

    while ($student = $result->fetch_assoc()) {
        $studentName = $student['FirstName'] . ' ' . $student['LastName'];
        $orgName = 'Test Org for ' . $studentName;
        
        // Ensure unique username for host
        $userStmt = $conn->query("SELECT Username FROM users WHERE Role = 'Host Organization' AND Username LIKE 'H%' ORDER BY CAST(SUBSTRING(Username, 2) AS UNSIGNED) DESC LIMIT 1");
        $lastUsername = "H000";
        if ($userStmt->num_rows > 0) {
            $lastUsername = $userStmt->fetch_assoc()['Username'];
        }
        $num = intval(substr($lastUsername, 1));
        $newUsername = 'H' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        
        $rawPassword = bin2hex(random_bytes(6));
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

        // Insert User
        $insertUser->bind_param("ss", $newUsername, $hashedPassword);
        if (!$insertUser->execute()) {
            echo "Failed to create user for $studentName: " . $conn->error . "\n";
            continue;
        }
        $userId = $conn->insert_id;

        // Insert Host
        $contactPerson = "HR " . $student['FirstName'];
        $insertHost->bind_param("isss", $userId, $orgName, $contactPerson, $targetEmail);
        if (!$insertHost->execute()) {
            echo "Failed to create host org for $studentName: " . $conn->error . "\n";
            continue;
        }
        $hostOrgId = $conn->insert_id;

        // Insert Application
        $financeStatus = $financeStatuses[array_rand($financeStatuses)];
        $insertApp->bind_param("isis", $student['StudentID'], $orgName, $hostOrgId, $financeStatus);
        if (!$insertApp->execute()) {
            echo "Failed to create application for $studentName: " . $conn->error . "\n";
            continue;
        }

        echo "Created Host: $orgName | Application Status: Pending | Finance: $financeStatus\n";

        // Send Email
        try {
            Mailer::sendHostCredentials($targetEmail, $orgName, $newUsername, $rawPassword);
            echo "  -> Welcome email sent to $targetEmail (for $orgName)\n";
        } catch (\Exception $e) {
            echo "  -> Failed to send email for $orgName: " . $e->getMessage() . "\n";
        }
        
        // Sleep a bit to prevent overwhelming the SMTP server if there are many students
        sleep(1);
    }

    echo "\nFinished generating placements and sending emails.\n";

} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
