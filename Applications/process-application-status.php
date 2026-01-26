<?php
require_once '../config.php';
requireLogin('host_org');

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

try {
    $conn = getDBConnection();
    
    $opportunityId = filter_input(INPUT_POST, 'opportunity_id', FILTER_VALIDATE_INT);
    $studentId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status'); // Approved or Rejected
    $hostOrgId = $_SESSION['host_org_id'];

    if (!$opportunityId || !$studentId || !in_array($status, ['Approved', 'Rejected'])) {
        throw new Exception('Invalid input parameters');
    }

    // Verify the application belongs to this host org
    $verifyStmt = $conn->prepare("
        SELECT * FROM jobapplication 
        WHERE OpportunityID = ? AND StudentID = ? AND HostOrgID = ?
    ");
    $verifyStmt->bind_param("iii", $opportunityId, $studentId, $hostOrgId);
    $verifyStmt->execute();
    
    if ($verifyStmt->get_result()->num_rows === 0) {
        throw new Exception('Application not found or access denied');
    }
    $verifyStmt->close();

    // Update status
    $updateStmt = $conn->prepare("
        UPDATE jobapplication 
        SET Status = ? 
        WHERE OpportunityID = ? AND StudentID = ?
    ");
    $updateStmt->bind_param("sii", $status, $opportunityId, $studentId);
    
    if ($updateStmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Application successfully " . strtolower($status);
    } else {
        throw new Exception('Failed to update status: ' . $conn->error);
    }
    $updateStmt->close();

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);
?>
