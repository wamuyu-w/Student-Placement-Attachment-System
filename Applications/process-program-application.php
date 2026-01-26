<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $applicationId = sanitizeInput($_POST['application_id']);
    $status = sanitizeInput($_POST['status']);
    
    if ($applicationId && $status) {
        // Validate status
        if (!in_array($status, ['Approved', 'Rejected'])) {
            header("Location: admin-applications.php?error=invalid_status");
            exit();
        }

        $stmt = $conn->prepare("UPDATE attachmentapplication SET ApplicationStatus = ? WHERE ApplicationID = ?");
        $stmt->bind_param("si", $status, $applicationId);
        
        if ($stmt->execute()) {
            header("Location: admin-applications.php?success=status_updated");
        } else {
            header("Location: admin-applications.php?error=update_failed");
        }
        $stmt->close();
    } else {
        header("Location: admin-applications.php?error=missing_data");
    }
    $conn->close();
} else {
    header("Location: admin-applications.php");
}
?>
