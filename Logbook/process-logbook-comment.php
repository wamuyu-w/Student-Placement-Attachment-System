<?php
require_once '../config.php';
// requireLogin('staff'); // Verification moved inside logic to support both Staff and Host Org

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $entryId = sanitizeInput($_POST['entry_id']);
    $comment = sanitizeInput($_POST['comment']);
    
    if ($entryId && $comment) {
        $lecturerId = $_SESSION['LecturerID'] ?? null;
        $hostOrgId = $_SESSION['host_org_id'] ?? null;

        if ($lecturerId) {
            // Validate Supervision (Staff)
            $checkStmt = $conn->prepare("
                SELECT sv.SupervisionID 
                FROM supervision sv
                JOIN attachment a ON sv.AttachmentID = a.AttachmentID
                JOIN logbook l ON l.AttachmentID = a.AttachmentID
                JOIN logbookentry le ON le.LogbookID = l.LogbookID
                WHERE sv.LecturerID = ? AND le.EntryID = ?
            ");
            $checkStmt->bind_param("ii", $lecturerId, $entryId);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows === 0) {
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

        } elseif ($hostOrgId) {
            // Validate Host Org Association
            $checkStmt = $conn->prepare("
                SELECT a.AttachmentID
                FROM attachment a
                JOIN logbook l ON l.AttachmentID = a.AttachmentID
                JOIN logbookentry le ON le.LogbookID = l.LogbookID
                WHERE a.HostOrgID = ? AND le.EntryID = ?
            ");
            $checkStmt->bind_param("ii", $hostOrgId, $entryId);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows === 0) {
                header("Location: host-org-logbook.php?error=unauthorized_access");
                exit();
            }
            $checkStmt->close();

            $stmt = $conn->prepare("UPDATE logbookentry SET HostSupervisorComments = ? WHERE EntryID = ?");
            $stmt->bind_param("si", $comment, $entryId);

            if ($stmt->execute()) {
                header("Location: host-org-logbook.php?success=comment_saved");
            } else {
                header("Location: host-org-logbook.php?error=save_failed");
            }
            $stmt->close();

        } else {
            // No valid session found
            header("Location: ../Login Pages/login.php");
            exit();
        }
    } else {
        header("Location: staff-logbook.php?error=missing_data");
    }
    $conn->close();
} else {
    header("Location: staff-logbook.php");
}
?>
