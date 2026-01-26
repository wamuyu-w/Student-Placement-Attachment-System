<?php
require_once '../config.php';
requireLogin('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student-applications.php");
    exit();
}

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

if (!$studentId) {
    header("Location: ../Login Pages/login-student.php");
    exit();
}

// Double check if student has an existing pending or approved application
$checkStmt = $conn->prepare("SELECT ApplicationID FROM attachmentapplication WHERE StudentID = ? AND (ApplicationStatus = 'Pending' OR ApplicationStatus = 'Approved')");
$checkStmt->bind_param("i", $studentId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    // Already has active application
    header("Location: student-applications.php?error=already_applied");
    exit();
}
$checkStmt->close();

// Insert new application
$intendedHost = isset($_POST['intended_host']) ? trim($_POST['intended_host']) : null;
$stmt = $conn->prepare("INSERT INTO attachmentapplication (StudentID, ApplicationDate, ApplicationStatus, IntendedHostOrg) VALUES (?, CURDATE(), 'Pending', ?)");
$stmt->bind_param("is", $studentId, $intendedHost);

if ($stmt->execute()) {
    header("Location: student-applications.php?success=applied");
} else {
    header("Location: student-applications.php?error=db_error");
}

$stmt->close();
$conn->close();
?>
