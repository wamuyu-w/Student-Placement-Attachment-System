<?php
require_once '../config.php';
requireLogin('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $entryId = sanitizeInput($_POST['entry_id']);
    $comment = sanitizeInput($_POST['comment']);
    
    if ($entryId && $comment) {
        // Verify supervision relationship
        $lecturerId = $_SESSION['LecturerID'];
        $checkStmt = $conn->prepare("
            SELECT 1 
            FROM logbookentry le
            JOIN logbook l ON le.LogbookID = l.LogbookID
            JOIN supervision s ON l.AttachmentID = s.AttachmentID
            WHERE le.EntryID = ? AND s.LecturerID = ?
        ");
        $checkStmt->bind_param("ii", $entryId, $lecturerId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            $checkStmt->close();
            header("Location: staff-logbook.php?error=unauthorized_access");
            exit();
        }
        $checkStmt->close();
        
        $stmt = $conn->prepare("UPDATE logbookentry SET AcademicSupervisorComments = ? WHERE EntryID = ?");
        $stmt->bind_param("si", $comment, $entryId);
        
        if ($stmt->execute()) {
            header("Location: staff-logbook.php?success=comment_saved");
        } else {
            header("Location: staff-logbook.php?error=save_failed");
        }
        $stmt->close();
    } else {
        header("Location: staff-logbook.php?error=missing_data");
    }
    $conn->close();
} else {
    header("Location: staff-logbook.php");
}
?>
