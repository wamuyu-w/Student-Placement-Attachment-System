<?php
namespace App\Models;
use App\Config\Database;

class Assessment {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAssessmentCode($attachmentId) {
        $stmt = $this->conn->prepare("SELECT AssessmentCode FROM attachment WHERE AttachmentID = ?");
        $stmt->bind_param("i", $attachmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['AssessmentCode'];
        }
        return null;
    }

    public function getAssessmentCount($attachmentId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM assessment WHERE AttachmentID = ? AND Status = 'Completed'");
        $stmt->bind_param("i", $attachmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function create($data) {
        $sql = "INSERT INTO assessment (AttachmentID, LecturerID, AssessmentType, Marks, Remarks, AssessmentDate, CriteriaScores, Status)
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?, 'Completed')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdss",
            $data['attachment_id'],
            $data['lecturer_id'],
            $data['assessment_type'],
            $data['marks'],
            $data['remarks'],
            $data['criteria_scores']
        );
        return $stmt->execute();
    }

    public function schedule($data) {
        // Now using SupervisionComments and Status columns.
        $sql = "INSERT INTO assessment (AttachmentID, LecturerID, AssessmentType, AssessmentDate, Marks, Status, SupervisionComments)
                VALUES (?, ?, ?, ?, 0, 'Scheduled', ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss",
            $data['attachment_id'],
            $data['lecturer_id'],
            $data['assessment_type'],
            $data['assessment_date'],
            $data['supervision_comments']
        );
        return $stmt->execute();
    }

    public function getById($assessmentId) {
        $sql = "SELECT
                    a.AssessmentDate, a.AssessmentType, a.Marks, a.Remarks, a.CriteriaScores, a.SupervisionComments, a.Status,
                    s.FirstName, s.LastName, u.Username as AdmissionNumber, s.Course, s.Faculty,
                    ho.OrganizationName,
                    l.Name as AssessorName, l.Department as AssessorDept,
                    att.StudentID
                FROM assessment a
                JOIN attachment att ON a.AttachmentID = att.AttachmentID
                JOIN student s ON att.StudentID = s.StudentID
                JOIN users u ON s.UserID = u.UserID
                JOIN hostorganization ho ON att.HostOrgID = ho.HostOrgID
                LEFT JOIN lecturer l ON a.LecturerID = l.LecturerID
                WHERE a.AssessmentID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $assessmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getStudentAssessments($studentId) {
        $sql = "SELECT
                    a.AssessmentID, a.AssessmentDate, a.AssessmentType, a.Marks,
                    l.Name as AssessorName
                FROM assessment a
                JOIN attachment att ON a.AttachmentID = att.AttachmentID
                LEFT JOIN lecturer l ON a.LecturerID = l.LecturerID
                WHERE att.StudentID = ?
                ORDER BY a.AssessmentDate DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getScheduled($attachmentId) {
        $stmt = $this->conn->prepare("SELECT * FROM assessment WHERE AttachmentID = ? AND Status = 'Scheduled' LIMIT 1");
        $stmt->bind_param("i", $attachmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($assessmentId, $data) {
        $sql = "UPDATE assessment SET 
                Marks = ?, 
                Remarks = ?, 
                CriteriaScores = ?, 
                Status = 'Completed', 
                AssessmentDate = CURDATE(),
                LecturerID = ?
                WHERE AssessmentID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("dssii", 
            $data['marks'], 
            $data['remarks'], 
            $data['criteria_scores'], 
            $data['lecturer_id'],
            $assessmentId
        );
        return $stmt->execute();
    }
}
