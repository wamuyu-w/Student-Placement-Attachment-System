<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $attachmentId = sanitizeInput($_POST['attachment_id']);
    $lecturerId = sanitizeInput($_POST['lecturer_id']);
    
    if ($attachmentId && $lecturerId) {
        // Check existing supervisors
        $checkStmt = $conn->prepare("SELECT LecturerID FROM supervision WHERE AttachmentID = ?");
        $checkStmt->bind_param("i", $attachmentId);
        $checkStmt->execute();
        $res = $checkStmt->get_result();
        
        $supervisors = [];
        while ($row = $res->fetch_assoc()) {
            $supervisors[] = $row['LecturerID'];
        }
        $checkStmt->close();

        // Check existing assessments
        $assessStmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment WHERE AttachmentID = ?");
        $assessStmt->bind_param("i", $attachmentId);
        $assessStmt->execute();
        $assessCount = $assessStmt->get_result()->fetch_assoc()['count'];
        $assessStmt->close();

        $canAssign = false;
        $errorMsg = "";

        if (count($supervisors) == 0) {
            $canAssign = true; // 1st Supervisor
        } elseif (count($supervisors) == 1) {
            if (in_array($lecturerId, $supervisors)) {
                $errorMsg = "already_assigned"; // Same supervisor
            } elseif ($assessCount == 0) {
                $errorMsg = "assessment_pending"; // 1st Assessment not done yet
            } else {
                $canAssign = true; // 2nd Supervisor
            }
        } else {
            $errorMsg = "max_supervisors_reached";
        }
        
        if ($canAssign) {
            $stmt = $conn->prepare("INSERT INTO supervision (LecturerID, AttachmentID) VALUES (?, ?)");
            $stmt->bind_param("ii", $lecturerId, $attachmentId);
            
            if ($stmt->execute()) {
                header("Location: admin-supervisors.php?success=assigned");
            } else {
                header("Location: admin-supervisors.php?error=assignment_failed");
            }
            $stmt->close();
        } else {
            header("Location: admin-supervisors.php?error=" . urlencode($errorMsg));
        }
    } else {
        header("Location: admin-supervisors.php?error=missing_data");
    }
    $conn->close();
} else {
    header("Location: admin-supervisors.php");
}
?>
