<?php
/**
 * CRON SCRIPT: Send Assessment Reminders
 * Run this script daily via cron to send reminders to students, hosts, and lecturers
 * for any assessments scheduled within the next 24 hours.
 */

// Define absolute path to include index.php properly
require_once __DIR__ . '/../public/index.php';

use App\Config\Database;
use App\Core\Mailer;

$db = (new Database())->connect();

// Get assessments scheduled for tomorrow
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$sql = "
    SELECT 
        a.AssessmentDate, a.AssessmentType, a.Status,
        att.AttachmentID, 
        s.FirstName, s.LastName, s.Email as StudentEmail,
        h.OrganizationName, h.Email as HostEmail,
        l.Name as LecturerName, l.Email as LecturerEmail
    FROM assessment a
    JOIN attachment att ON a.AttachmentID = att.AttachmentID
    JOIN student s ON att.StudentID = s.StudentID
    JOIN hostorganization h ON att.HostOrgID = h.HostOrgID
    JOIN lecturer l ON a.LecturerID = l.LecturerID
    WHERE a.Status = 'Scheduled'
      AND DATE(a.AssessmentDate) = '$tomorrow'
";

$res = $db->query($sql);
$count = 0;

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $studentName = trim($row['FirstName'] . ' ' . $row['LastName']);
        $assessmentDate = $row['AssessmentDate'];
        $assessmentType = $row['AssessmentType'];
        
        // Notify Student
        if (!empty($row['StudentEmail'])) {
            Mailer::notifyAssessmentReminder(
                $row['StudentEmail'], 
                $studentName, 
                $studentName, 
                $assessmentDate, 
                $assessmentType, 
                'student'
            );
        }
        
        // Notify Host
        if (!empty($row['HostEmail'])) {
            Mailer::notifyAssessmentReminder(
                $row['HostEmail'], 
                $row['OrganizationName'] . ' Team', 
                $studentName, 
                $assessmentDate, 
                $assessmentType, 
                'host organization'
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
                'lecturer/supervisor'
            );
        }
        
        $count++;
    }
}

echo "Successfully sent reminders for $count assessments scheduled on $tomorrow.\n";
