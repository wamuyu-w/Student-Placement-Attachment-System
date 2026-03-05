<?php
namespace App\Models;
use App\Config\Database;
// The Assessment class provides methods to manage assessments related to student attachments, including creating new assessments, scheduling them, and retrieving assessment details for students and specific assessment IDs.
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
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM assessment WHERE AttachmentID = ?");
        $stmt->bind_param("i", $attachmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function create($data) {
        $sql = "INSERT INTO assessment (AttachmentID, LecturerID, AssessmentType, Marks, Remarks, AssessmentDate, CriteriaScores) 
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?)";
        
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
    // this function schedules an assessment for a student's attachment by inserting a new record into the assessment table with the provided details,
    // including the attachment ID, lecturer ID, assessment type, date, and any remarks
    public function schedule($data) {
        $sql = "INSERT INTO assessment (AttachmentID, LecturerID, AssessmentType, AssessmentDate, Remarks) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $data['attachment_id'], $data['lecturer_id'], $data['assessment_type'], $data['assessment_date'], $data['remarks']);
        return $stmt->execute();
    }

    public function getById($assessmentId) {
        $sql = "SELECT 
                    a.AssessmentDate, a.AssessmentType, a.Marks, a.Remarks, a.CriteriaScores,
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
}
