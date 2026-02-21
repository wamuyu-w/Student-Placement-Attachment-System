<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin-students.php");
    exit();
}

if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    header("Location: admin-students.php?error=Probelm uploading file");
    exit();
}

$file = $_FILES['csvFile']['tmp_name'];
$handle = fopen($file, "r");

if ($handle === FALSE) {
    header("Location: admin-students.php?error=Could not open file");
    exit();
}

$conn = getDBConnection();
$successCount = 0;
$errorCount = 0;
$row = 0;

// Default password hash
$defaultPassword = hashPassword('Changeme123!');

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $row++;
    // Skip header row if it exists (assuming header matches the expected format or just skip first row)
    // Basic check: if first column is "AdmissionNumber" or similar
    if ($row == 1 && (strtolower($data[0]) == 'admissionnumber' || strtolower($data[0]) == 'admno')) {
        continue;
    }

    // Expected format: AdmNumber, FirstName, LastName
    // You can extend this to include Course, Faculty, etc. if needed
    if (count($data) < 1) {
        continue;
    }

    $admNumber = sanitizeInput($data[0]);
    $firstName = sanitizeInput($data[1] ?? '');
    $lastName = sanitizeInput($data[2] ?? '');
    $faculty = sanitizeInput($_POST['faculty'] ?? '');

    if (empty($admNumber)) {
        continue;
    }

    // Check if user exists
    $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
    $checkStmt->bind_param("s", $admNumber);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        // Create User
        $insertUser = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Student', 'Active')");
        $insertUser->bind_param("ss", $admNumber, $defaultPassword);
        
        if ($insertUser->execute()) {
            $userID = $conn->insert_id;
            
            // Create Student Profile
            $insertStudent = $conn->prepare("INSERT INTO student (UserID, FirstName, LastName, Faculty, EligibilityStatus) VALUES (?, ?, ?, ?, 'Pending')");
            $insertStudent->bind_param("issss", $userID, $firstName, $lastName, $faculty);
            $insertStudent->execute();
            $insertStudent->close();
            
            $successCount++;
        } else {
            $errorCount++;
        }
        $insertUser->close();
    } else {
        $errorCount++; // Duplicate
    }
    $checkStmt->close();
}

fclose($handle);
$conn->close();

header("Location: admin-students.php?success=Imported $successCount students. Failed/Duplicate: $errorCount");
exit();
