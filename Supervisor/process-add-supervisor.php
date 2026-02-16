<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // Get and sanitize input
    $staffNumber = sanitizeInput($_POST['staffNumber']);
    
    // Validation
    if (empty($staffNumber)) {
        header("Location: admin-supervisors.php?error=" . urlencode("Staff Number is required"));
        exit();
    }
    
    // Check if Staff Number already exists in Lecturer table
    $checkStmt = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
    $checkStmt->bind_param("s", $staffNumber);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        header("Location: admin-supervisors.php?error=" . urlencode("Supervisor with this Staff Number already exists"));
        exit();
    }
    $checkStmt->close();
    
    // Start Transaction
    $conn->begin_transaction();
    
    try {
        // 1. Generate L-Username
        // Find the highest existing L-number
        $result = $conn->query("SELECT Username FROM users WHERE Username LIKE 'L%' ORDER BY CAST(SUBSTRING(Username, 2) AS UNSIGNED) DESC LIMIT 1");
        
        $nextNum = 1;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Extract number part
            $lastUsername = $row['Username'];
            $numberPart = (int)substr($lastUsername, 1);
            $nextNum = $numberPart + 1;
        }
        
        // Format as L00X
        $newUsername = 'L' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        
        // 2. Create User Account
        // Default password: Changeme123!
        $defaultPassword = 'Changeme123!';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $role = 'Lecturer';
        $status = 'Active';
        
        $userStmt = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, ?, ?)");
        $userStmt->bind_param("ssss", $newUsername, $hashedPassword, $role, $status);
        
        if (!$userStmt->execute()) {
            throw new Exception("Error creating user account: " . $userStmt->error);
        }
        $userID = $conn->insert_id;
        $userStmt->close();
        
        // 3. Create Lecturer/Supervisor Record
        $lecRole = 'Supervisor';
        
        $lecStmt = $conn->prepare("INSERT INTO lecturer (UserID, StaffNumber, Role) VALUES (?, ?, ?)");
        $lecStmt->bind_param("iss", $userID, $staffNumber, $lecRole);
        
        if (!$lecStmt->execute()) {
            throw new Exception("Error creating supervisor profile: " . $lecStmt->error);
        }
        $lecStmt->close();
        
        // Commit Transaction
        $conn->commit();
        
        $message = "Supervisor added successfully. Login Credentials -> Username: " . $newUsername . " | Password: " . $defaultPassword;
        header("Location: admin-supervisors.php?success=" . urlencode($message));
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin-supervisors.php?error=" . urlencode($e->getMessage()));
    }
    
    $conn->close();
} else {
    header("Location: admin-supervisors.php");
    exit();
}
?>
