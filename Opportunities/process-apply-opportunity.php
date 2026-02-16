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

    // Ensure all necessary form fields are present before proceeding
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

        // Check if file size is within the 5MB limit
        // Check if file size is within the 5MB limit
        if ($fileSize > 5242880) { // 5MB in bytes
            throw new Exception('File size exceeds 5MB limit');
        }

        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Only PDF and DOCX files are allowed');
        }

        // Create the directory for resumes if it doesn't already exist
        $uploadsDir = '../assets/uploads/resumes/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        // Generate a unique filename to prevent overwriting existing files
        $resumeFileName = 'resume_' . $studentId . '_' . $opportunityId . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadsDir . $resumeFileName;

        // Move uploaded file
        if (!move_uploaded_file($fileTmp, $filePath)) {
            throw new Exception('Failed to upload resume');
        }
    }
    
    // Check if the opportunity exists and extract the Host Organization ID
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

    // Prevent duplicate applications from the same student for this opportunity
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
    

    // Save the application details to the database
    $insertStmt = $conn->prepare("
        INSERT INTO jobapplication (OpportunityID, HostOrgID, StudentID, ApplicationDate, Status, ResumePath, ResumeLink, Motivation)
        VALUES (?, ?, ?, NOW(), 'Pending', ?, ?, ?)
    ");

    $resumePathToStore = $resumeFileName ? $resumeFileName : null;
    $resumeLinkToStore = $hasLink ? $resumeLink : null;
    
    $insertStmt->bind_param("iiisss", $opportunityId, $hostOrgId, $studentId, $resumePathToStore, $resumeLinkToStore, $motivation);
    
    if (!$insertStmt->execute()) {
        if ($resumeFileName && file_exists($filePath)) unlink($filePath);
        throw new Exception('Failed to submit application: ' . $insertStmt->error);
    }
    $insertStmt->close();

    $response['success'] = true;
    $response['message'] = 'Application submitted successfully! You will receive updates via email.';

} catch (Exception $e) {
    if (isset($resumeFileName) && isset($filePath) && file_exists($filePath)) {
        unlink($filePath);
    }
    $response['success'] = false;
    $response['message'] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
?>
