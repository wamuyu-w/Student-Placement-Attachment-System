<?php
require_once '../config.php';
requireLogin('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $attachmentId = sanitizeInput($_POST['attachment_id']);
    $lecturerId = sanitizeInput($_POST['lecturer_id']);
    $totalScore = filter_var($_POST['total_score'], FILTER_SANITIZE_NUMBER_INT);
    $comments = sanitizeInput($_POST['comments']);
    
    $criteriaData = $_POST['criteria'] ?? [];
    $criteriaJson = json_encode($criteriaData);
    
    // Validate inputs
    if ($attachmentId && $lecturerId && isset($totalScore) && !empty($criteriaData)) {
        
        // Ensure session validation from the previous step is present
        if (!isset($_SESSION['authorized_assessment_' . $attachmentId])) {
            header("Location: ../Supervisor/staff-supervision.php?error=unauthorized_assessment");
            exit();
        }

        // Assessment type logic - determine if first or final based on existing count
        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment WHERE AttachmentID = ?");
        $countStmt->bind_param("i", $attachmentId);
        $countStmt->execute();
        $res = $countStmt->get_result();
        $count = $res->fetch_assoc()['count'];
        $assessmentType = ($count == 0) ? 'First Assessment' : 'Final Assessment';
        $countStmt->close();
        
        // Insert into assessment table including CriteriaScores
        $sql = "INSERT INTO assessment (AttachmentID, LecturerID, AssessmentType, Marks, Remarks, AssessmentDate, CriteriaScores) 
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?)";
                
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iisdss", $attachmentId, $lecturerId, $assessmentType, $totalScore, $comments, $criteriaJson);
            
            if ($stmt->execute()) {
                // Done - unset the authorization
                unset($_SESSION['authorized_assessment_' . $attachmentId]);
                header("Location: ../Supervisor/staff-supervision.php?success=assessment_submitted");
            } else {
                error_log("Assessment Insert Error: " . $stmt->error);
                header("Location: ../Supervisor/staff-supervision.php?error=db_error");
            }
            $stmt->close();
        } else {
            error_log("Assessment Prepare Error: " . $conn->error);
            header("Location: ../Supervisor/staff-supervision.php?error=db_error");
        }
        
    } else {
        header("Location: ../Supervisor/staff-supervision.php?error=missing_data");
    }
    
    $conn->close();
} else {
    header("Location: ../Supervisor/staff-supervision.php");
}
?>
