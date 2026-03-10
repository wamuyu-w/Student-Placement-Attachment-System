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

        // File upload logic
        $targetDir = __DIR__ . "/../../public/uploads/reports/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = "report_" . $studentId . "_" . time() . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
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
}
