<?php
require_once '../config.php';
requireLogin('host_org');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    $hostOrgId = $_SESSION['host_org_id'] ?? null;
    $attachmentId = $_POST['attachment_id'] ?? null;

    if ($hostOrgId && $attachmentId) {
        // Verify this attachment belongs to the host org selected
        $verifyStmt = $conn->prepare("SELECT AttachmentID FROM attachment WHERE AttachmentID = ? AND HostOrgID = ?");
        $verifyStmt->bind_param("ii", $attachmentId, $hostOrgId);
        $verifyStmt->execute();
        $result = $verifyStmt->get_result();

        if ($result->num_rows > 0) {
            // Generate a random 6 character alphanumeric code
            $code = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6));

            $updateStmt = $conn->prepare("UPDATE attachment SET AssessmentCode = ? WHERE AttachmentID = ?");
            $updateStmt->bind_param("si", $code, $attachmentId);
            if ($updateStmt->execute()) {
                header("Location: host-org-supervision.php?success=1");
                exit();
            }
            $updateStmt->close();
        }
        $verifyStmt->close();
    }
    $conn->close();
}

header("Location: host-org-supervision.php?error=1");
exit();
?>
