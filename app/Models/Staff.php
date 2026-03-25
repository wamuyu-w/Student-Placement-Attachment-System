<?php
namespace App\Models;
use App\Config\Database;
// Staff model for handling staff-related database operations
class Staff {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getDashboardStats($staffId) {
        $stmt = $this->conn->prepare("
            SELECT
                (SELECT COUNT(*) FROM attachment a
                    WHERE a.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
                ) AS monitored_attachments,
                (SELECT COUNT(*) FROM logbook l
                    WHERE l.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
                    AND l.Status = 'Pending'
                ) AS pending_reviews,
                (SELECT COUNT(DISTINCT a.StudentID) FROM attachment a
                    WHERE a.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
                ) AS students_monitored,
                (SELECT COUNT(*) FROM logbook l
                    WHERE l.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
                ) AS total_logbooks
        ");
        $stmt->bind_param("iiii", $staffId, $staffId, $staffId, $staffId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getRecentLogs($staffId) {
        $stmt = $this->conn->prepare("
            SELECT
                st.FirstName, st.LastName, st.Course, l.IssueDate, l.Status
            FROM supervision sv
            INNER JOIN attachment a ON sv.AttachmentID = a.AttachmentID
            INNER JOIN logbook l ON l.AttachmentID = a.AttachmentID
            INNER JOIN student st ON a.StudentID = st.StudentID
            WHERE sv.LecturerID = ?
            ORDER BY l.IssueDate DESC
            LIMIT 5
        ");
        $stmt->bind_param("i", $staffId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getSupervisedStudents($staffId) {
        $stmt = $this->conn->prepare("
            SELECT s.StudentID, s.FirstName, s.LastName, u.Username as RegistrationNumber, s.Course, 
                   ho.OrganizationName, a.AttachmentID, a.AttachmentStatus,
                   (SELECT COUNT(*) FROM assessment WHERE AttachmentID = a.AttachmentID AND Status = 'Completed') as AssessmentCount,
                   (SELECT MAX(AssessmentDate) FROM assessment WHERE AttachmentID = a.AttachmentID AND Status = 'Completed') as LastAssessment,
                   (SELECT MIN(AssessmentDate) FROM assessment WHERE AttachmentID = a.AttachmentID AND Status = 'Scheduled') as NextAssessmentDate
            FROM supervision sup
            JOIN attachment a ON sup.AttachmentID = a.AttachmentID
            JOIN student s ON a.StudentID = s.StudentID
            JOIN users u ON s.UserID = u.UserID
            JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
            WHERE sup.LecturerID = ? AND a.AttachmentStatus = 'Ongoing'
        ");
        $stmt->bind_param("i", $staffId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateProfile($staffId, $data) {
        $stmt = $this->conn->prepare("UPDATE lecturer SET Name = ?, Department = ? WHERE LecturerID = ?");
        $stmt->bind_param("ssi", $data['name'], $data['department'], $staffId);
        return $stmt->execute();
    }

    public function completeProfile($staffId, $data) {
        $stmt = $this->conn->prepare("UPDATE lecturer SET Name=?, Department=?, Faculty=? WHERE LecturerID=?");
        $stmt->bind_param("sssi", $data['name'], $data['department'], $data['faculty'], $staffId);
        return $stmt->execute();
    }
}
