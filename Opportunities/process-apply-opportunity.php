<?php
require_once '../config.php';
requireLogin('student');

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    $conn = getDBConnection();

    // Validate input
    $opportunityId = sanitizeInput($_POST['opportunity_id'] ?? '');
    $studentId = $_SESSION['student_id'] ?? null;
    $motivation = sanitizeInput($_POST['motivation'] ?? '');
    $resumeLink = trim($_POST['resume_link'] ?? '');

    if (!$opportunityId || !$studentId || !$motivation) {
        throw new Exception('Missing required fields');
    }

    // Validate motivation length
    if (strlen($motivation) > 500) {
        throw new Exception('Motivation statement exceeds 500 characters');
    }

    $hasFile = isset($_FILES['resume']) && $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE;
    $hasLink = !empty($resumeLink);
    $resumeFileName = null;

    if (!$hasFile && !$hasLink) {
        throw new Exception('Please upload a resume or provide a resume link.');
    }

    if ($hasFile) {
        $file = $_FILES['resume'];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];

        // Validate file
        if ($fileError !== UPLOAD_ERR_OK) {
            throw new Exception('Error uploading file');
        }

        if ($fileSize > 5 * 1024 * 1024) {
            throw new Exception('File size exceeds 5MB limit');
        }

        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Only PDF and DOCX files are allowed');
        }

        // Create uploads directory if it doesn't exist
        $uploadsDir = '../assets/uploads/resumes/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        // Generate unique filename
        $resumeFileName = 'resume_' . $studentId . '_' . $opportunityId . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadsDir . $resumeFileName;

        // Move uploaded file
        if (!move_uploaded_file($fileTmp, $filePath)) {
            throw new Exception('Failed to upload resume');
        }
    }
    
    // Verify opportunity exists and is still active
    // Verify opportunity exists and is still active, and get HostOrgID
    $oppStmt = $conn->prepare("
        SELECT OpportunityID, ApplicationEndDate, HostOrgID
        FROM attachmentopportunity
        WHERE OpportunityID = ?
    ");
    $oppStmt->bind_param("i", $opportunityId);
    $oppStmt->execute();
    $oppResult = $oppStmt->get_result();

    if ($oppResult->num_rows === 0) {
        throw new Exception('Opportunity not found');
    }

    $opportunity = $oppResult->fetch_assoc();
    $oppStmt->close();

    if (strtotime($opportunity['ApplicationEndDate']) < time()) {
        throw new Exception('Application deadline has passed');
    }

    $hostOrgId = $opportunity['HostOrgID'];

    // Check if student already applied (jobapplication table)
    $checkStmt = $conn->prepare("
        SELECT * FROM jobapplication
        WHERE StudentID = ? AND OpportunityID = ?
    ");
    $checkStmt->bind_param("ii", $studentId, $opportunityId);
    $checkStmt->execute();

    if ($checkStmt->get_result()->num_rows > 0) {
        throw new Exception('You have already applied to this opportunity');
    }
    $checkStmt->close();
    

    // Insert application into jobapplication table
    $insertStmt = $conn->prepare("
        INSERT INTO jobapplication (OpportunityID, HostOrgID, StudentID, ApplicationDate, Status, ResumePath, ResumeLink)
        VALUES (?, ?, ?, NOW(), 'Pending', ?, ?)
    ");
    if (!$insertStmt) {
        if ($resumeFileName) unlink($filePath);
        throw new Exception('Database error: ' . $conn->error);
    }
    $resumePathToStore = $resumeFileName ? $resumeFileName : null;
    $resumeLinkToStore = $hasLink ? $resumeLink : null;
    $insertStmt->bind_param("iiisss", $opportunityId, $hostOrgId, $studentId, $resumePathToStore, $resumeLinkToStore);
    if (!$insertStmt->execute()) {
        if ($resumeFileName) unlink($filePath);
        throw new Exception('Failed to submit application: ' . $insertStmt->error);
    }
    $insertStmt->close();

    $response['success'] = true;
    $response['message'] = 'Application submitted successfully! You will receive updates via email.';
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
?>
