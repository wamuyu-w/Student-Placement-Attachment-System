<?php
require_once '../config.php';
requireLogin('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student-settings.php");
    exit();
}

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$currentPass = $_POST['current_password'];
$newPass = $_POST['new_password'];
$confirmPass = $_POST['confirm_password'];

// Basic validation
if (strlen($newPass) < 6) {
    header("Location: student-settings.php?error=weak_password");
    exit();
}

if ($newPass !== $confirmPass) {
    header("Location: student-settings.php?error=mismatch");
    exit();
}

// Verify current password
$stmt = $conn->prepare("SELECT Password FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($currentPass, $user['Password'])) {
    header("Location: student-settings.php?error=wrong_current");
    exit();
}

// Update password
$hashedInfo = hashPassword($newPass);
$updateStmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
$updateStmt->bind_param("si", $hashedInfo, $userId);

if ($updateStmt->execute()) {
    header("Location: student-settings.php?success=updated");
} else {
    header("Location: student-settings.php?error=db_error");
}

$updateStmt->close();
$conn->close();
?>
