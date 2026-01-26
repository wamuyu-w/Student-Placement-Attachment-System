<?php
require_once '../config.php';
requireLogin('student');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $studentId = $_SESSION['student_id'];
    $hostOrgId = sanitizeInput($_POST['host_org_id']);
    $startDate = sanitizeInput($_POST['start_date']);
    $endDate = sanitizeInput($_POST['end_date']);
    
    if ($hostOrgId && $startDate && $endDate) {
        // Double check if student already has an active attachment
        $checkStmt = $conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
        $checkStmt->bind_param("i", $studentId);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows === 0) {
            // Insert new attachment
            $insertStmt = $conn->prepare("INSERT INTO attachment (StudentID, HostOrgID, StartDate, EndDate, ClearanceStatus, AttachmentStatus) VALUES (?, ?, ?, ?, 'Pending', 'Ongoing')");
            $insertStmt->bind_param("iiss", $studentId, $hostOrgId, $startDate, $endDate);
            
            if ($insertStmt->execute()) {
                // Insert an attachment
                header("Location: student-applications.php?success=registered");
            } else {
                header("Location: student-applications.php?error=insert_failed");
            }
            $insertStmt->close();
        } else {
            header("Location: student-applications.php?error=already_active");
        }
        $checkStmt->close();
    } else {
        header("Location: student-applications.php?error=missing_fields");
    }
    $conn->close();
} else {
    header("Location: student-applications.php");
}
?>
