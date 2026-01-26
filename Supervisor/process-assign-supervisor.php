<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $attachmentId = sanitizeInput($_POST['attachment_id']);
    $lecturerId = sanitizeInput($_POST['lecturer_id']);
    
    if ($attachmentId && $lecturerId) {
        // Check if already assigned
        $checkStmt = $conn->prepare("SELECT SupervisionID FROM supervision WHERE AttachmentID = ?");
        $checkStmt->bind_param("i", $attachmentId);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO supervision (LecturerID, AttachmentID) VALUES (?, ?)");
            $stmt->bind_param("ii", $lecturerId, $attachmentId);
            
            if ($stmt->execute()) {
                header("Location: admin-supervisors.php?success=assigned");
            } else {
                header("Location: admin-supervisors.php?error=assignment_failed");
            }
            $stmt->close();
        } else {
            header("Location: admin-supervisors.php?error=already_assigned");
        }
        $checkStmt->close();
    } else {
        header("Location: admin-supervisors.php?error=missing_data");
    }
    $conn->close();
} else {
    header("Location: admin-supervisors.php");
}
?>
