<?php
// Script to generate opportunities and job applications
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') === 0) {
        $file = __DIR__ . '/app/' . str_replace('\\', '/', substr($className, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
});

use App\Config\Database;
use App\Core\Mailer;

try {
    $db = new Database();
    $conn = $db->connect();

    // 1. Check for the 20 students with AdmissionNumbers starting with 10990
    $studentResult = $conn->query("
        SELECT s.StudentID, s.FirstName, s.LastName, u.Username AS AdmissionNumber 
        FROM student s
        JOIN users u ON s.UserID = u.UserID
        WHERE u.Username LIKE '10990%'
    ");
    
    if ($studentResult->num_rows < 20) {
        echo "Found " . $studentResult->num_rows . " bulk students.\n";
        echo "Please upload 'test_students.csv' via the Admin Panel -> Students -> Bulk Upload first, then run this script again.\n";
        exit;
    }

    $students = [];
    while ($row = $studentResult->fetch_assoc()) {
        $students[] = $row;
    }
    // We only need exactly 20
    $students = array_slice($students, 0, 20);

    // 2. Mix of Host Organizations (Goal: 20 Orgs, one for each opportunity)
    $hostOrgs = [];
    $existingRes = $conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY RAND() LIMIT 10");
    while ($row = $existingRes->fetch_assoc()) {
        $hostOrgs[] = $row;
    }

    // Generate remaining Host Organizations to reach 20 total
    $neededHosts = 20 - count($hostOrgs);
    echo "Using " . count($hostOrgs) . " existing organizations. Generating $neededHosts new ones...\n";

    $insUser = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
    $insHost = $conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email) VALUES (?, ?, ?, ?)");

    for ($i = 1; $i <= $neededHosts; $i++) {
        // Find next Username
        $userStmt = $conn->query("SELECT Username FROM users WHERE Role = 'Host Organization' AND Username LIKE 'H%' ORDER BY CAST(SUBSTRING(Username, 2) AS UNSIGNED) DESC LIMIT 1");
        $lastUsername = "H000";
        if ($userStmt->num_rows > 0) {
            $lastUsername = $userStmt->fetch_assoc()['Username'];
        }
        $num = intval(substr($lastUsername, 1));
        $newUsername = 'H' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        
        $hashed = password_hash('Changeme123!', PASSWORD_DEFAULT);
        $insUser->bind_param("ss", $newUsername, $hashed);
        $insUser->execute();
        $userId = $conn->insert_id;

        $orgName = "Safaricom " . $i . " Branch";
        if ($i % 3 == 0) $orgName = "KCB Group - Region " . $i;
        if ($i % 5 == 0) $orgName = "Kenya Airways Tech Dept " . $i;
        
        $contactPerson = "HR " . $i;
        $email = "hr{$i}@generatedcorp.co.ke";
        $insHost->bind_param("isss", $userId, $orgName, $contactPerson, $email);
        $insHost->execute();
        
        $hostOrgs[] = [
            'HostOrgID' => $conn->insert_id,
            'OrganizationName' => $orgName
        ];
    }

    // 3. Generate 20 Opportunities (1 per host)
    echo "Generating 20 Opportunities...\n";
    $roles = ["Software Engineer Intern", "Data Science Attaché", "Marketing Intern", "Financial Analyst Intern", "HR Assistant", "IT Support Technician", "Business Dev Intern", "Graphic Design Attaché", "Network Engineer Intern", "Cybersecurity Analyst Intern"];
    
    $opportunities = [];
    $insOpp = $conn->prepare("INSERT INTO attachmentopportunity (HostOrgID, Description, EligibilityCriteria, ApplicationStartDate, ApplicationEndDate, Status) VALUES (?, ?, 'Relevant coursework. GPA 3.0+', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Open')");

    foreach ($hostOrgs as $index => $host) {
        $roleName = $roles[array_rand($roles)] . " at " . $host['OrganizationName'];
        $insOpp->bind_param("is", $host['HostOrgID'], $roleName);
        $insOpp->execute();
        
        $opportunities[] = [
            'OpportunityID' => $conn->insert_id,
            'HostOrgID' => $host['HostOrgID'],
            'Role' => $roleName
        ];
    }

    // 4. Generate 20 Job Applications
    echo "Generating 20 Job Applications...\n";
    $insApp = $conn->prepare("INSERT INTO jobapplication (OpportunityID, HostOrgID, StudentID, ApplicationDate, Status, Motivation) VALUES (?, ?, ?, CURDATE(), 'Pending', ?)");

    foreach ($students as $index => $student) {
        $opp = $opportunities[$index]; // 1-to-1 mapping
        $motivation = "I am very interested in the {$opp['Role']} position to grow my skills.";
        
        $insApp->bind_param("iiis", $opp['OpportunityID'], $opp['HostOrgID'], $student['StudentID'], $motivation);
        $insApp->execute();
        
        echo " -> Submitted application for " . $student['FirstName'] . " to " . $opp['Role'] . "\n";
    }

    echo "\nAll Done! Successfully created 20 students, 20 mixed organizations, 20 opportunities, and 20 applications.\n";

} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
