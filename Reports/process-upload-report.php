<?php
require_once '../config.php';
requireLogin('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student-reports.php");
    exit();
}

$conn = getDBConnection();
$attachmentId = sanitizeInput($_POST['attachment_id']);
$studentId = $_SESSION['student_id'];

// Verify attachment
$checkStmt = $conn->prepare("SELECT AttachmentID FROM attachment WHERE AttachmentID = ? AND StudentID = ? AND AttachmentStatus = 'Active'");
$checkStmt->bind_param("ii", $attachmentId, $studentId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows === 0) {
    header("Location: student-reports.php?error=invalid_attachment");
    exit();
}
$checkStmt->close();

if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
    header("Location: student-reports.php?error=upload_failed");
    exit();
}

$file = $_FILES['report_file'];
$fileName = $file['name'];
$fileTmp = $file['tmp_name'];
$fileSize = $file['size'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExt, ['pdf', 'doc', 'docx'])) {
    header("Location: student-reports.php?error=invalid_type");
    exit();
}

if ($fileSize > 10 * 1024 * 1024) { // 10MB
    header("Location: student-reports.php?error=too_large");
    exit();
}

// Create directory
$uploadDir = '../assets/uploads/reports/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$newFileName = 'final_report_' . $attachmentId . '_' . time() . '.' . $fileExt;
$destPath = $uploadDir . $newFileName;

if (move_uploaded_file($fileTmp, $destPath)) {
    // Save to DB
    $stmt = $conn->prepare("INSERT INTO finalreport (AttachmentID, SubmissionDate, ReportFile, Status) VALUES (?, NOW(), ?, 'Pending')");
    $stmt->bind_param("is", $attachmentId, $newFileName);
    
    if ($stmt->execute()) {
        header("Location: student-reports.php?success=submitted");
    } else {
        header("Location: student-reports.php?error=db_error");
    }
    $stmt->close();
} else {
    header("Location: student-reports.php?error=move_failed");
}

$conn->close();
?>
