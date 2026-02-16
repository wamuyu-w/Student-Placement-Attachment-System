<?php
require_once '../config.php';
requireLogin('staff');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$conn = getDBConnection();

$attachmentId = $_POST['attachment_id'] ?? null;
$type = $_POST['assessment_type'] ?? null;
$date = $_POST['assessment_date'] ?? null;
$remarks = $_POST['remarks'] ?? '';

if (!$attachmentId || !$type || !$date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check if assessment already exists for this type?
// For now, we allow multiple, but maybe warn? Let's simply insert.

$stmt = $conn->prepare("INSERT INTO assessment (AttachmentID, AssessmentType, AssessmentDate, Remarks) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $attachmentId, $type, $date, $remarks);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
