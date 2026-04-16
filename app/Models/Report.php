<?php
namespace App\Models;
use App\Config\Database;

class Report {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
        $this->ensureTablesExist();
    }

    private function ensureTablesExist() {
        $check = $this->conn->query("SHOW TABLES LIKE 'finalreport'");
        if ($check->num_rows == 0) {
            $sql = "CREATE TABLE finalreport (
                ReportID INT AUTO_INCREMENT PRIMARY KEY,
                AttachmentID INT NOT NULL,
                ReportFile VARCHAR(255) NOT NULL,
                SubmissionDate DATE DEFAULT CURRENT_TIMESTAMP,
                Status VARCHAR(20) DEFAULT 'Pending',
                UNIQUE KEY unique_attachment (AttachmentID)
            )";
            $this->conn->query($sql);
        }
    }

    // Admin Reports
    public function getPlacementStats() {
        // Placements by Faculty
        $sql = "SELECT s.Faculty, COUNT(*) as count 
                FROM attachment a 
                JOIN student s ON a.StudentID = s.StudentID 
                WHERE a.AttachmentStatus = 'Ongoing' OR a.AttachmentStatus = 'Completed'
                GROUP BY s.Faculty";
        return $this->conn->query($sql);
    }

    public function getHostStats() {
        $sql = "SELECT ho.OrganizationName, COUNT(a.AttachmentID) as student_count
                FROM hostorganization ho
                LEFT JOIN attachment a ON ho.HostOrgID = a.HostOrgID AND (a.AttachmentStatus = 'Ongoing' OR a.AttachmentStatus = 'Completed')
                GROUP BY ho.HostOrgID
                ORDER BY student_count DESC
                LIMIT 10";
        return $this->conn->query($sql);
    }

    public function getAssessmentSchedule() {
        $sql = "SELECT 
                    s.FirstName, s.LastName, u.Username as AdmNumber,
                    l.Name as LecturerName,
                    ho.OrganizationName,
                    a.StartDate, a.EndDate,
                    ass.AssessmentDate, ass.AssessmentType
                FROM attachment a
                JOIN student s ON a.StudentID = s.StudentID
                JOIN users u ON s.UserID = u.UserID
                JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
                LEFT JOIN supervision sup ON a.AttachmentID = sup.AttachmentID
                LEFT JOIN lecturer l ON sup.LecturerID = l.LecturerID
                LEFT JOIN assessment ass ON a.AttachmentID = ass.AttachmentID
                WHERE a.AttachmentStatus = 'Ongoing'
                ORDER BY ass.AssessmentDate ASC";
        return $this->conn->query($sql);
    }

    public function getSupervisorStats() {
        $sql = "SELECT l.Name, l.Department, COUNT(sup.AttachmentID) as student_count
                FROM lecturer l
                LEFT JOIN supervision sup ON l.LecturerID = sup.LecturerID
                GROUP BY l.LecturerID
                ORDER BY student_count DESC";
        return $this->conn->query($sql);
    }

    public function getSystemStats() {
        $stats = [];
        // Final Reports
        $stats['final_reports'] = $this->conn->query("SELECT COUNT(*) FROM finalreport")->fetch_row()[0];
        // Cleared Students
        $stats['cleared_students'] = $this->conn->query("SELECT COUNT(*) FROM student WHERE EligibilityStatus = 'Cleared'")->fetch_row()[0];
        // Job Applications
        $res = $this->conn->query("SELECT Status, COUNT(*) as count FROM jobapplication GROUP BY Status");
        $stats['job_apps'] = [];
        while($row = $res->fetch_assoc()) $stats['job_apps'][$row['Status']] = $row['count'];
        
        return $stats;
    }

    // Staff Reports
    public function getSupervisedStats($lecturerId) {
        // Students supervised by this lecturer
        $sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.Course, a.AttachmentStatus, a.AttachmentID,
                       (SELECT COUNT(*) FROM logbook WHERE AttachmentID = a.AttachmentID) as log_count,
                       (SELECT AVG(Marks) FROM assessment WHERE AttachmentID = a.AttachmentID) as avg_score
                FROM supervision sup
                JOIN attachment a ON sup.AttachmentID = a.AttachmentID
                JOIN student s ON a.StudentID = s.StudentID
                WHERE sup.LecturerID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $lecturerId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getLecturerGrades($lecturerId) {
        $sql = "SELECT s.FirstName, s.LastName, u.Username as AdmNumber,
                       ass.AssessmentType, ass.Marks, ass.AssessmentDate,
                       ho.OrganizationName
                FROM assessment ass
                JOIN attachment a ON ass.AttachmentID = a.AttachmentID
                JOIN student s ON a.StudentID = s.StudentID
                JOIN users u ON s.UserID = u.UserID
                JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
                WHERE ass.LecturerID = ?
                ORDER BY ass.AssessmentDate DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $lecturerId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Host Reports
    public function getHostStudentStats($hostId) {
        $sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.Course, a.StartDate, a.EndDate, a.AttachmentID,
                       (SELECT COUNT(*) FROM logbook WHERE AttachmentID = a.AttachmentID) as log_count
                FROM attachment a
                JOIN student s ON a.StudentID = s.StudentID
                WHERE a.HostOrgID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $hostId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getHostPerformanceReport($hostId) {
        // Condensed logbook: Weekly Host comments only
        $sql = "SELECT s.FirstName, s.LastName, l.WeekNumber, l.HostSupervisorComments, l.StartDate
                FROM logbook l
                JOIN attachment a ON l.AttachmentID = a.AttachmentID
                JOIN student s ON a.StudentID = s.StudentID
                WHERE a.HostOrgID = ? AND l.HostSupervisorComments IS NOT NULL AND l.HostSupervisorComments != ''
                ORDER BY s.StudentID, l.WeekNumber";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $hostId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Student Reports
    public function getStudentProgress($studentId) {
        // FIX: Return ALL attachment sessions to handle dual-attachment history correctly
        $sql = "SELECT a.AttachmentID, a.AttachmentStatus, a.StartDate, a.EndDate, a.AssessmentCode,
                       ho.OrganizationName,
                       (SELECT COUNT(*) FROM logbook WHERE AttachmentID = a.AttachmentID) as log_count,
                       (SELECT COUNT(*) FROM assessment WHERE AttachmentID = a.AttachmentID) as assessment_count,
                       fr.ReportFile as ReportPath, fr.SubmissionDate as UploadDate, fr.Status as ReportStatus
                FROM attachment a
                JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
                LEFT JOIN finalreport fr ON a.AttachmentID = fr.AttachmentID
                WHERE a.StudentID = ? 
                ORDER BY a.StartDate DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sessions = [];
        while($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }
        return $sessions;
    }

    public function uploadFinalReport($studentId, $file) {
        // First get attachment ID (active one)
        $stmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus IN ('Ongoing', 'Active')");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) return ['success' => false, 'message' => 'No active attachment found.'];
        
        $attachmentId = $res->fetch_assoc()['AttachmentID'];

        // Validate MIME type — reject anything that isn't a real PDF
        $allowedMimeTypes = ['application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return ['success' => false, 'message' => 'Only PDF files are allowed.'];
        }

        // File upload logic
        $targetDir = __DIR__ . "/../../public/uploads/reports/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        
        // Force .pdf extension regardless of client-supplied name
        $fileName = "report_" . $studentId . "_" . time() . ".pdf";
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            // Insert into DB
            $stmt = $this->conn->prepare("INSERT INTO finalreport (AttachmentID, ReportFile, SubmissionDate, Status) VALUES (?, ?, NOW(), 'Pending') ON DUPLICATE KEY UPDATE ReportFile = ?, SubmissionDate = NOW(), Status = 'Pending'");
            $stmt->bind_param("iss", $attachmentId, $fileName, $fileName);
            if ($stmt->execute()) {
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'Upload failed.'];
    }

    public function getAssessmentSummary() {
        $sql = "SELECT 
                    s.FirstName, s.LastName, u.Username as AdmNumber,
                    a.AttachmentStatus,
                    (SELECT Marks FROM assessment WHERE AttachmentID = a.AttachmentID AND AssessmentType = 'First Assessment' AND Status = 'Completed' LIMIT 1) as FirstScore,
                    (SELECT Marks FROM assessment WHERE AttachmentID = a.AttachmentID AND AssessmentType = 'Final Assessment' AND Status = 'Completed' LIMIT 1) as SecondScore,
                    (SELECT AVG(Marks) FROM assessment WHERE AttachmentID = a.AttachmentID AND Status = 'Completed') as AverageScore
                FROM attachment a
                JOIN student s ON a.StudentID = s.StudentID
                JOIN users u ON s.UserID = u.UserID
                WHERE a.AttachmentStatus != 'Pending'
                ORDER BY s.LastName, s.FirstName";
        return $this->conn->query($sql);
    }

    public function getSystemEffectiveness() {
        $stats = [];
        $stats['totalStudents'] = $this->conn->query("SELECT COUNT(*) FROM student")->fetch_row()[0];
        $stats['placedStudents'] = $this->conn->query("SELECT COUNT(DISTINCT StudentID) FROM attachment WHERE AttachmentStatus != 'Cancelled'")->fetch_row()[0];
        $stats['totalOpportunities'] = $this->conn->query("SELECT COUNT(*) FROM attachmentopportunity")->fetch_row()[0];
        $stats['completedAttachments'] = $this->conn->query("SELECT COUNT(*) FROM attachment WHERE AttachmentStatus = 'Completed'")->fetch_row()[0];
        
        // Placements over time (by month)
        $stats['placementsByMonth'] = $this->conn->query("SELECT DATE_FORMAT(StartDate, '%Y-%m') as Month, COUNT(*) as count FROM attachment GROUP BY Month ORDER BY Month DESC LIMIT 6");
        
        return $stats;
    }

    public function getLecturerAssessedStats() {
        $sql = "SELECT l.Name, COUNT(DISTINCT a.AttachmentID) as students_assessed, AVG(a.Marks) as avg_marks_given
                FROM lecturer l
                JOIN assessment a ON l.LecturerID = a.LecturerID
                WHERE a.Status = 'Completed'
                GROUP BY l.LecturerID
                ORDER BY students_assessed DESC";
        return $this->conn->query($sql);
    }

    public function getPlacementCompletions() {
        $data = [];

        // Summary counters
        $data['total_completed']  = $this->conn->query("SELECT COUNT(*) FROM attachment WHERE AttachmentStatus = 'Completed'")->fetch_row()[0];
        $data['total_cleared']    = $this->conn->query("SELECT COUNT(*) FROM attachment WHERE ClearanceStatus = 'Cleared'")->fetch_row()[0];
        $data['total_ongoing']    = $this->conn->query("SELECT COUNT(*) FROM attachment WHERE AttachmentStatus = 'Ongoing'")->fetch_row()[0];
        $data['reports_approved'] = $this->conn->query("SELECT COUNT(*) FROM finalreport WHERE Status = 'Approved'")->fetch_row()[0];

        // Avg first & final scores for completed students
        $data['avg_first']  = $this->conn->query("SELECT ROUND(AVG(Marks),1) FROM assessment WHERE AssessmentType='First Assessment' AND Status='Completed'")->fetch_row()[0] ?? 0;
        $data['avg_final']  = $this->conn->query("SELECT ROUND(AVG(Marks),1) FROM assessment WHERE AssessmentType='Final Assessment' AND Status='Completed'")->fetch_row()[0] ?? 0;

        // Full per-student completion table
        $sql = "SELECT
                    s.StudentID, s.FirstName, s.LastName, s.Course, s.Faculty,
                    u.Username AS AdmNumber,
                    ho.OrganizationName,
                    a.AttachmentID, a.StartDate, a.EndDate,
                    a.AttachmentStatus, a.ClearanceStatus, a.AssessmentCode,
                    l.Name AS SupervisorName,
                    (SELECT COUNT(*) FROM logbook lb WHERE lb.AttachmentID = a.AttachmentID AND lb.Status = 'Approved') AS ApprovedWeeks,
                    (SELECT Marks FROM assessment WHERE AttachmentID = a.AttachmentID AND AssessmentType = 'First Assessment' AND Status = 'Completed' LIMIT 1) AS FirstScore,
                    (SELECT Marks FROM assessment WHERE AttachmentID = a.AttachmentID AND AssessmentType = 'Final Assessment' AND Status = 'Completed' LIMIT 1) AS FinalScore,
                    (SELECT AVG(Marks) FROM assessment WHERE AttachmentID = a.AttachmentID AND Status = 'Completed') AS AvgScore,
                    fr.Status AS ReportStatus, fr.SubmissionDate AS ReportSubmitted
                FROM attachment a
                JOIN student s ON a.StudentID = s.StudentID
                JOIN users u ON s.UserID = u.UserID
                JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
                LEFT JOIN supervision sup ON a.AttachmentID = sup.AttachmentID
                LEFT JOIN lecturer l ON sup.LecturerID = l.LecturerID
                LEFT JOIN finalreport fr ON a.AttachmentID = fr.AttachmentID
                ORDER BY a.ClearanceStatus DESC, s.LastName, s.FirstName";
        $data['students'] = $this->conn->query($sql);

        return $data;
    }

    public function getPlacementImpact() {
        $data = [];

        // Top-level KPIs
        $data['total_students']       = $this->conn->query("SELECT COUNT(*) FROM student")->fetch_row()[0];
        $data['placed_students']      = $this->conn->query("SELECT COUNT(DISTINCT StudentID) FROM attachment WHERE AttachmentStatus IN ('Ongoing','Completed')")->fetch_row()[0];
        $data['completed_placements'] = $this->conn->query("SELECT COUNT(*) FROM attachment WHERE AttachmentStatus='Completed'")->fetch_row()[0];
        $data['total_host_orgs']      = $this->conn->query("SELECT COUNT(*) FROM hostorganization")->fetch_row()[0];
        $data['total_logbook_weeks']  = $this->conn->query("SELECT COUNT(*) FROM logbook WHERE Status='Approved'")->fetch_row()[0];
        $data['total_assessments']    = $this->conn->query("SELECT COUNT(*) FROM assessment WHERE Status='Completed'")->fetch_row()[0];
        $data['avg_grade']            = $this->conn->query("SELECT ROUND(AVG(Marks),1) FROM assessment WHERE Status='Completed'")->fetch_row()[0] ?? 0;
        $data['total_applications']   = $this->conn->query("SELECT COUNT(*) FROM jobapplication")->fetch_row()[0];
        $data['accepted_applications']= $this->conn->query("SELECT COUNT(*) FROM jobapplication WHERE Status='Accepted'")->fetch_row()[0];

        // Placement rate by faculty
        $data['faculty_impact'] = $this->conn->query(
            "SELECT s.Faculty,
                    COUNT(DISTINCT s.StudentID) AS total_students,
                    COUNT(DISTINCT a.StudentID) AS placed_students,
                    ROUND(AVG(CASE WHEN asmt.Marks IS NOT NULL THEN asmt.Marks END),1) AS avg_score
             FROM student s
             LEFT JOIN attachment a ON s.StudentID = a.StudentID
             LEFT JOIN assessment asmt ON a.AttachmentID = asmt.AttachmentID AND asmt.Status='Completed'
             GROUP BY s.Faculty
             ORDER BY placed_students DESC"
        );

        // Top performing host organizations
        $data['top_hosts'] = $this->conn->query(
            "SELECT ho.OrganizationName, ho.PhysicalAddress,
                    COUNT(a.AttachmentID) AS student_count,
                    SUM(CASE WHEN a.ClearanceStatus='Cleared' THEN 1 ELSE 0 END) AS cleared_count,
                    ROUND(AVG(asmt.Marks),1) AS avg_score
             FROM hostorganization ho
             LEFT JOIN attachment a ON ho.HostOrgID = a.HostOrgID
             LEFT JOIN assessment asmt ON a.AttachmentID = asmt.AttachmentID AND asmt.Status='Completed'
             GROUP BY ho.HostOrgID
             HAVING student_count > 0
             ORDER BY avg_score DESC, student_count DESC
             LIMIT 15"
        );

        // Grade distribution buckets
        $data['grade_dist'] = $this->conn->query(
            "SELECT
                CASE
                    WHEN Marks >= 80 THEN 'Distinction (80-100)'
                    WHEN Marks >= 70 THEN 'Credit (70-79)'
                    WHEN Marks >= 60 THEN 'Pass (60-69)'
                    ELSE 'Below Pass (<60)'
                END AS GradeBand,
                COUNT(*) AS student_count
             FROM (
                SELECT AttachmentID, ROUND(AVG(Marks),1) AS Marks
                FROM assessment WHERE Status='Completed'
                GROUP BY AttachmentID
             ) AS avg_scores
             GROUP BY GradeBand
             ORDER BY FIELD(GradeBand,'Distinction (80-100)','Credit (70-79)','Pass (60-69)','Below Pass (<60)')"
        );

        // Job application conversion
        $data['job_app_stats'] = [];
        $res = $this->conn->query("SELECT Status, COUNT(*) AS cnt FROM jobapplication GROUP BY Status");
        while($r = $res->fetch_assoc()) $data['job_app_stats'][$r['Status']] = $r['cnt'];

        // Opportunity listings breakdown
        $data['opportunities'] = $this->conn->query(
            "SELECT ao.Description, ho.OrganizationName, ao.Status,
                    (SELECT COUNT(*) FROM jobapplication ja WHERE ja.OpportunityID = ao.OpportunityID) AS total_apps,
                    (SELECT COUNT(*) FROM jobapplication ja WHERE ja.OpportunityID = ao.OpportunityID AND ja.Status='Accepted') AS accepted
             FROM attachmentopportunity ao
             JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
             ORDER BY total_apps DESC"
        );

        // Logbook engagement rate per student (approved weeks out of 12)
        $data['logbook_engagement'] = $this->conn->query(
            "SELECT
                CASE
                    WHEN wks = 12 THEN '12 weeks (Full)'
                    WHEN wks >= 9  THEN '9-11 weeks'
                    WHEN wks >= 6  THEN '6-8 weeks'
                    ELSE '< 6 weeks'
                END AS EngagementBand,
                COUNT(*) AS student_count
             FROM (
                SELECT a.AttachmentID, COUNT(lb.LogbookID) AS wks
                FROM attachment a
                LEFT JOIN logbook lb ON a.AttachmentID = lb.AttachmentID AND lb.Status='Approved'
                WHERE a.AttachmentStatus IN ('Ongoing','Completed')
                GROUP BY a.AttachmentID
             ) AS t
             GROUP BY EngagementBand"
        );

        return $data;
    }
}
