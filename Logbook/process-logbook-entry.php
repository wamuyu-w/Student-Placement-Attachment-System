<?php
require_once '../config.php';
requireLogin('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student-logbook.php");
    exit();
}

$conn = getDBConnection();
$logbookId = sanitizeInput($_POST['logbook_id']);
$date = sanitizeInput($_POST['entry_date']);
$activities = sanitizeInput($_POST['activities']);

if (empty($logbookId) || empty($date) || empty($activities)) {
    // to do: set a message showing missing fields
    header("Location: student-logbook.php?error=missing_fields");
    exit();
}

// Verify this logbook belongs to the student's active attachment
$studentId = $_SESSION['student_id'];
$checkStmt = $conn->prepare("
    SELECT lb.LogbookID
    FROM logbook lb
    JOIN attachment a ON lb.AttachmentID = a.AttachmentID
    WHERE lb.LogbookID = ? AND a.StudentID = ? AND a.AttachmentStatus = 'Ongoing'
");
$checkStmt->bind_param("ii", $logbookId, $studentId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows === 0) {
    header("Location: student-logbook.php?error=invalid_logbook");
    exit();
}
$checkStmt->close();

// Insert entry
$stmt = $conn->prepare("INSERT INTO logbookentry (LogbookID, EntryDate, Activities) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $logbookId, $date, $activities);

if ($stmt->execute()) {
    header("Location: student-logbook.php?success=entry_added");
} else {
    header("Location: student-logbook.php?error=db_error");
}

$stmt->close();
$conn->close();
?>
