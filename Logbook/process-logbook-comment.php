<?php
require_once '../config.php';
requireLogin('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $entryId = sanitizeInput($_POST['entry_id']);
    $comment = sanitizeInput($_POST['comment']);
    
    if ($entryId && $comment) {
        // Technically we should check if this entry belongs to a student supervised by this lecturer
        // validation could be added here for stricter security
        
        $stmt = $conn->prepare("UPDATE logbookentry SET HostSupervisorComments = ? WHERE EntryID = ?");
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
