<?php
require_once '../config.php';
requireLogin('admin');

if (isset($_GET['id'])) {
    $conn = getDBConnection();
    $id = (int)$_GET['id'];
    
    // Check if it exists and maybe check for dependencies (like applications)?
    // If there are applications, we probably shouldn't delete it or cascading delete?
    // For now, we'll try to delete. If valid FK constraints exist, it might fail if there are applications.
    // Let's check for applications first to be safe and give a good error message.
    
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM jobapplication WHERE OpportunityID = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($appCount);
    $checkStmt->fetch();
    $checkStmt->close();
    
    if ($appCount > 0) {
        header("Location: admin-opportunities-management.php?error=" . urlencode("Cannot delete opportunity because there are " . $appCount . " existing applications. Please close the opportunity instead."));
        $conn->close();
        exit();
    }
    
    // Delete
    $stmt = $conn->prepare("DELETE FROM attachmentopportunity WHERE OpportunityID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin-opportunities-management.php?success=" . urlencode("Opportunity deleted successfully"));
    } else {
        header("Location: admin-opportunities-management.php?error=" . urlencode("Error deleting opportunity: " . $conn->error));
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: admin-opportunities-management.php");
    exit();
}
?>
