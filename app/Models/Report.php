<?php
namespace App\Models;
use App\Config\Database;
// This model handles all report-related functionalities for admin, staff, host, and students.
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

    // Staff Reports
    public function getSupervisedStats($lecturerId) {
        // Students supervised by this lecturer
        $sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.Course, a.AttachmentStatus, 
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

    // Host Reports
    public function getHostStudentStats($hostId) {
        $sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.Course, a.StartDate, a.EndDate,
                       (SELECT COUNT(*) FROM logbook WHERE AttachmentID = a.AttachmentID) as log_count
                FROM attachment a
                JOIN student s ON a.StudentID = s.StudentID
                WHERE a.HostOrgID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $hostId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Student Reports
    public function getStudentProgress($studentId) {
        // Check attachment status, logbook count, assessment status
        $sql = "SELECT a.AttachmentID, a.AttachmentStatus, a.StartDate, a.EndDate,
                       (SELECT COUNT(*) FROM logbook WHERE AttachmentID = a.AttachmentID) as log_count,
                       (SELECT COUNT(*) FROM assessment WHERE AttachmentID = a.AttachmentID) as assessment_count,
                       fr.ReportFile as ReportPath, fr.SubmissionDate as UploadDate, fr.Status as ReportStatus
                FROM attachment a
                LEFT JOIN finalreport fr ON a.AttachmentID = fr.AttachmentID
                WHERE a.StudentID = ? AND (a.AttachmentStatus = 'Ongoing' OR a.AttachmentStatus = 'Completed' OR a.AttachmentStatus = 'Active')";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Database Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function uploadFinalReport($studentId, $file) {
        // First get attachment ID
        $stmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus IN ('Ongoing', 'Completed', 'Active')");
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
            if (!$stmt) {
                return ['success' => false, 'message' => 'Database error: ' . $this->conn->error];
            }
            $stmt->bind_param("iss", $attachmentId, $fileName, $fileName);
            if ($stmt->execute()) {
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'Upload failed.'];
    }
}
