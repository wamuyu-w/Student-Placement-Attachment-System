<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$logbookId = sanitizeInput($_POST['logbook_id'] ?? '');
$weekEnding = sanitizeInput($_POST['week_ending'] ?? '');
$tasks = $_POST['tasks'] ?? [];
$comments = $_POST['comments'] ?? [];

if (empty($logbookId) || empty($weekEnding)) {
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

// Structure the activities data as JSON
$activitiesData = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

foreach ($days as $day) {
    $activitiesData[$day] = [
        'task' => sanitizeInput($tasks[$day] ?? ''),
        'comment' => sanitizeInput($comments[$day] ?? '')
    ];
}

$activitiesJson = json_encode($activitiesData);

// Check if entry exists for this week
$checkEntry = $conn->prepare("SELECT EntryID FROM logbookentry WHERE LogbookID = ? AND EntryDate = ?");
$checkEntry->bind_param("is", $logbookId, $weekEnding);
$checkEntry->execute();
$result = $checkEntry->get_result();
$existingEntry = $result->fetch_assoc();
$checkEntry->close();

if ($existingEntry) {
    // Update existing
    $stmt = $conn->prepare("UPDATE logbookentry SET Activities = ? WHERE EntryID = ?");
    $stmt->bind_param("si", $activitiesJson, $existingEntry['EntryID']);
} else {
    // Insert new
    $stmt = $conn->prepare("INSERT INTO logbookentry (LogbookID, EntryDate, Activities) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $logbookId, $weekEnding, $activitiesJson);
}

if ($stmt->execute()) {
    header("Location: student-logbook.php?success=entry_saved");
} else {
    header("Location: student-logbook.php?error=db_error");
}

$stmt->close();
$conn->close();
?>
