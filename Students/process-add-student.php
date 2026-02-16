<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // Get and sanitize input
    $admNumber = sanitizeInput($_POST['admNumber']); // This will be the Username
    
    // Validation
    if (empty($admNumber)) {
        header("Location: admin-students.php?error=" . urlencode("Admission Number is required"));
        exit();
    }
    
    // Check if username (Adm Number) already exists
    $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
    $checkStmt->bind_param("s", $admNumber);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        header("Location: admin-students.php?error=" . urlencode("Student with this Admission Number already exists"));
        exit();
    }
    $checkStmt->close();
    
    // Start Transaction
    $conn->begin_transaction();
    
    try {
        // 1. Create User Account
        // Default password: Changeme123!
        $defaultPassword = 'Changeme123!';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $role = 'Student';
        $status = 'Active';
        
        $userStmt = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, ?, ?)");
        $userStmt->bind_param("ssss", $admNumber, $hashedPassword, $role, $status);
        
        if (!$userStmt->execute()) {
            throw new Exception("Error creating user account: " . $userStmt->error);
        }
        $userID = $conn->insert_id;
        $userStmt->close();
        
        // 2. Create Student Record
        // We only have the UserID and EligibilityStatus. Everything else is NULL for now.
        $eligibility = 'Pending'; 
        
        $studentStmt = $conn->prepare("INSERT INTO student (UserID, EligibilityStatus) VALUES (?, ?)");
        $studentStmt->bind_param("is", $userID, $eligibility);
        
        if (!$studentStmt->execute()) {
            throw new Exception("Error creating student profile: " . $studentStmt->error);
        }
        $studentStmt->close();
        
        // Commit Transaction
        $conn->commit();
        
        header("Location: admin-students.php?success=" . urlencode("Student added successfully. They will update their details on first login."));
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin-students.php?error=" . urlencode($e->getMessage()));
    }
    
    $conn->close();
} else {
    header("Location: admin-students.php");
    exit();
}
?>
