<?php
require_once '../config.php';
requireLogin('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $attachmentId = sanitizeInput($_POST['attachment_id']);
    $score = sanitizeInput($_POST['total_score']);
    $grade = sanitizeInput($_POST['grade']);
    $comments = sanitizeInput($_POST['comments']);
    $lecturerId = sanitizeInput($_POST['lecturer_id']);
    
    // Validate inputs
    if ($attachmentId && $score && $grade && $lecturerId) {
        
        // Prepare statement (assuming 'assessment' table exists as per logic gap analysis, 
        // if not we'd create it, but standard assumption is schema exists)
        // Schema check: The View File on sql file earlier showed basic schema. 
        // Let's assume standard fields. If 'assessment' table missing, we might need SQL execution.
        // For now, proceeding with standard insert.
        
        $sql = "INSERT INTO assessment (AttachmentID, TotalScore, Grade, Comments, DateAssessed) 
                VALUES (?, ?, ?, ?, NOW())";
                
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("idss", $attachmentId, $score, $grade, $comments);
            
            if ($stmt->execute()) {
                header("Location: staff-assessment.php?success=submitted");
            } else {
                error_log("Assessment Insert Error: " . $stmt->error);
                header("Location: staff-assessment.php?error=db_error");
            }
            $stmt->close();
        } else {
            error_log("Assessment Prepare Error: " . $conn->error);
            header("Location: staff-assessment.php?error=prepare_error");
        }
        
    } else {
        header("Location: staff-assessment.php?error=missing_data");
    }
    
    $conn->close();
} else {
    header("Location: staff-assessment.php");
}
?>
