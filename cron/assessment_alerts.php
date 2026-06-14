<?php
/**
 * Cron script to check for assessments scheduled within the next 24 hours.
 * Sends email alerts to the Student, Host Organization, and Lecturer.
 * 
 * Usage: php cron/assessment_alerts.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Core\Mailer;

$db = new Database();
$conn = $db->connect();

// Get assessments scheduled for exactly tomorrow
$sql = "
    SELECT 
        a.AssessmentID, a.AssessmentType, a.AssessmentDate, a.LecturerID,
        att.StudentID, att.HostOrgID,
        s.FirstName, s.LastName, s.Email as StudentEmail,
        l.Name as LecturerName, l.Email as LecturerEmail,
        ho.OrganizationName, ho.Email as HostEmail
    FROM assessment a
    JOIN attachment att ON a.AttachmentID = att.AttachmentID
    JOIN student s ON att.StudentID = s.StudentID
    LEFT JOIN lecturer l ON a.LecturerID = l.LecturerID
    LEFT JOIN hostorganization ho ON att.HostOrgID = ho.HostOrgID
    WHERE a.Status = 'Scheduled'
      AND a.AssessmentDate = CURDATE() + INTERVAL 1 DAY
";

$result = $conn->query($sql);

if (!$result) {
    echo "Error querying database: " . $conn->error . "\n";
    exit(1);
}

$count = 0;
while ($row = $result->fetch_assoc()) {
    $studentName = $row['FirstName'] . ' ' . $row['LastName'];
    $assessmentType = $row['AssessmentType'];
    $assessmentDate = $row['AssessmentDate'];

    // Notify Student
    if (!empty($row['StudentEmail'])) {
        Mailer::notifyAssessmentReminder(
            $row['StudentEmail'], 
            $studentName, 
            $studentName, 
            $assessmentDate, 
            $assessmentType, 
            'Student'
        );
    }

    // Notify Lecturer
    if (!empty($row['LecturerEmail'])) {
        Mailer::notifyAssessmentReminder(
            $row['LecturerEmail'], 
            $row['LecturerName'], 
            $studentName, 
            $assessmentDate, 
            $assessmentType, 
            'Lecturer'
        );
    }

    // Notify Host Organization
    if (!empty($row['HostEmail'])) {
        Mailer::notifyAssessmentReminder(
            $row['HostEmail'], 
            $row['OrganizationName'], 
            $studentName, 
            $assessmentDate, 
            $assessmentType, 
            'Host Organization'
        );
    }
    
    $count++;
}

echo "Assessment alerts sent for $count assessments.\n";
