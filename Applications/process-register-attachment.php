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
        // Check if student has ANY attachment record (due to UNIQUE constraint)
        $checkStmt = $conn->prepare("SELECT AttachmentID, AttachmentStatus FROM attachment WHERE StudentID = ?");
        $checkStmt->bind_param("i", $studentId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            // No attachment exists, insert new one
            $insertStmt = $conn->prepare("INSERT INTO attachment (StudentID, HostOrgID, StartDate, EndDate, ClearanceStatus, AttachmentStatus) VALUES (?, ?, ?, ?, 'Pending', 'Ongoing')");
            $insertStmt->bind_param("iiss", $studentId, $hostOrgId, $startDate, $endDate);
            
            if ($insertStmt->execute()) {
                header("Location: student-applications.php?success=registered");
                exit();
            } else {
                // Log error for debugging
                error_log("Insert failed: " . $insertStmt->error);
                header("Location: student-applications.php?error=insert_failed");
                exit();
            }
            $insertStmt->close();
        } else {
            // Attachment exists
            $row = $result->fetch_assoc();
            if ($row['AttachmentStatus'] == 'Ongoing') {
                header("Location: student-applications.php?error=already_active");
                exit();
            } else {
                // If exists but not Ongoing (e.g. Cleared, Terminated), what to do? 
                // For now, assume they can't register another one or we update the existing one?
                // Depending on business logic. The user report implies they are trying to register.
                // If they have a stale record, we might need to Update.
                // Let's try to UPDATE if it exists but is not meaningful.
                // But for safety, let's just error out for now or Update if it was 'Pending'?
                // Safe bet: Show error user already has a record.
                 header("Location: student-applications.php?error=already_has_record");
                 exit();
            }
        }
        $checkStmt->close();
    } else {
        header("Location: student-applications.php?error=missing_fields");
        exit();
    }
    $conn->close();
} else {
    header("Location: student-applications.php");
    exit();
}
?>
