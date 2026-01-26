<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $opportunityId = sanitizeInput($_POST['opportunity_id']);
    $studentId = sanitizeInput($_POST['student_id']);
    $status = sanitizeInput($_POST['status']);
    
    if ($opportunityId && $studentId && $status) {
        $stmt = $conn->prepare("UPDATE jobapplication SET Status = ? WHERE OpportunityID = ? AND StudentID = ?");
        $stmt->bind_param("sii", $status, $opportunityId, $studentId);
        
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
