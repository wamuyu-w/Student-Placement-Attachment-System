<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $studentId = sanitizeInput($_POST['student_id']);
    
    if ($studentId) {
        // Update Student EligibilityStatus to 'Cleared'
        // Also update any active attachment to 'Completed' if not already
        
        $conn->begin_transaction();
        
        try {
            // 1. Update Student
            $stmt1 = $conn->prepare("UPDATE student SET EligibilityStatus = 'Cleared' WHERE StudentID = ?");
            $stmt1->bind_param("i", $studentId);
            $stmt1->execute();
            $stmt1->close();
            
            // 2. Update Attachment (force complete ongoing attachments)
            $stmt2 = $conn->prepare("UPDATE attachment SET AttachmentStatus = 'Completed', EndDate = COALESCE(EndDate, CURDATE()) WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
            $stmt2->bind_param("i", $studentId);
            $stmt2->execute();
            $stmt2->close();
            
            $conn->commit();
            header("Location: admin-students.php?success=cleared");
            
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: admin-students.php?error=clearance_failed");
        }
        
    } else {
        header("Location: admin-students.php?error=missing_data");
    }
    
    $conn->close();
} else {
    header("Location: admin-students.php");
}
?>
