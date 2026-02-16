<?php
require_once '../config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // Get and sanitize input
    $id = (int)$_POST['opportunity_id'];
    $hostOrgId = (int)$_POST['host_org_id'];
    $description = sanitizeInput($_POST['description']);
    $criteria = sanitizeInput($_POST['eligibility_criteria']);
    $startDate = $_POST['application_start_date'];
    $endDate = $_POST['application_end_date'];
    $status = sanitizeInput($_POST['status']);
    
    // Validation
    if (empty($id) || empty($hostOrgId) || empty($description) || empty($criteria) || empty($startDate) || empty($endDate) || empty($status)) {
        header("Location: admin-opportunities-management.php?error=" . urlencode("All fields are required"));
        exit();
    }
    
    if (strtotime($startDate) >= strtotime($endDate)) {
        header("Location: admin-opportunities-management.php?error=" . urlencode("End date must be after start date"));
        exit();
    }
    
    // Update
    $stmt = $conn->prepare("UPDATE attachmentopportunity SET HostOrgID=?, Description=?, EligibilityCriteria=?, ApplicationStartDate=?, ApplicationEndDate=?, Status=? WHERE OpportunityID=?");
    $stmt->bind_param("isssssi", $hostOrgId, $description, $criteria, $startDate, $endDate, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: admin-opportunities-management.php?success=" . urlencode("Opportunity updated successfully"));
    } else {
        header("Location: admin-opportunities-management.php?error=" . urlencode("Error updating opportunity: " . $conn->error));
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: admin-opportunities-management.php");
    exit();
}
?>
