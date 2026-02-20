<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin-supervisors.php");
    exit();
}

if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    header("Location: admin-supervisors.php?error=Problem uploading file");
    exit();
}

$file = $_FILES['csvFile']['tmp_name'];
$handle = fopen($file, "r");

if ($handle === FALSE) {
    header("Location: admin-supervisors.php?error=Could not open file");
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
    // Header check
    if ($row == 1 && (strtolower($data[0]) == 'staffnumber' || strtolower($data[0]) == 'staffno')) {
        continue;
    }

    // Expected: StaffNumber, Name, Department
    if (count($data) < 2) {
        continue;
    }

    $staffNumber = sanitizeInput($data[0]);
    $name = sanitizeInput($data[1] ?? '');
    $department = sanitizeInput($data[2] ?? '');

    if (empty($staffNumber)) {
        continue;
    }

    // Since we need a username for the `users` table, we can generate one or use StaffNumber as username.
    // But for bulk, using StaffNumber as Username is safer/easier unless we want to implement auto-increment logic here (L004, L005 etc).
    // Let's use StaffNumber as Username for simplicity and uniqueness.
    
    $username = $staffNumber; 

    // Check if user exists (by Username)
    $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $errorCount++;
        $checkStmt->close();
        continue;
    }
    $checkStmt->close();

    // Check if Lecturer exists (by StaffNumber)
    $checkLec = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
    $checkLec->bind_param("s", $staffNumber);
    $checkLec->execute();
    if ($checkLec->get_result()->num_rows > 0) {
        $errorCount++;
        $checkLec->close();
        continue;
    }
    $checkLec->close();


    // Insert User
    // Default to using StaffNumber as Username for bulk upload to ensure uniqueness without complex logic
    $insertUser = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Lecturer', 'Active')");
    $insertUser->bind_param("ss", $username, $defaultPassword);
    
    if ($insertUser->execute()) {
        $userID = $conn->insert_id;
        
        // Insert Lecturer and insert the Role = Supervisor 
        $insertLec = $conn->prepare("INSERT INTO lecturer (UserID, StaffNumber, Name, Department, Role) VALUES (?, ?, ?, ?, 'Supervisor')");
        $insertLec->bind_param("isss", $userID, $staffNumber, $name, $department);
        
        if ($insertLec->execute()) {
            $successCount++;
        } else {
            // If lecturer insert fails, delete the user created 
            $conn->query("DELETE FROM users WHERE UserID = $userID");
            $errorCount++;
            error_log("Failed to insert lecturer details for $staffNumber: " . $insertLec->error);
        }
        $insertLec->close();
    } else {
        $errorCount++;
        error_log("Failed to insert user for $staffNumber: " . $insertUser->error);
    }
    $insertUser->close();
}

fclose($handle);
$conn->close();

header("Location: admin-supervisors.php?success=Imported $successCount supervisors. Failed/Duplicate: $errorCount");
exit();
