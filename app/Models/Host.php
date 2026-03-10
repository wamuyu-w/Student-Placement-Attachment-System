<?php
namespace App\Models;
use App\Config\Database;
// and handle student attachments, while ensuring secure database interactions through prepared statements and transaction management for critical operations like registration.
// It serves as the primary interface for host organizations to interact
class Host {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }
    public function getDashboardStats($hostOrgId) {
        $stmt = $this->conn->prepare("
            SELECT 
                (SELECT COUNT(*) FROM attachmentopportunity WHERE HostOrgID = ?) as active_placements,
                (SELECT COUNT(*) FROM attachment WHERE HostOrgID = ? AND AttachmentStatus IN ('Active', 'Ongoing')) as students_attached,
                (SELECT COUNT(*) FROM attachment a 
                  INNER JOIN logbook l ON a.AttachmentID = l.AttachmentID 
                  WHERE a.HostOrgID = ? AND l.Status = 'Pending') as pending_logbooks
        ");
        $stmt->bind_param("iii", $hostOrgId, $hostOrgId, $hostOrgId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getRecentPlacements($hostOrgId) {
        $stmt = $this->conn->prepare("
            SELECT 
                s.FirstName, s.LastName, s.Course,
                COALESCE(ao.Description, 'Placement') as position_applied,
                a.StartDate, a.AttachmentStatus as Status
            FROM attachment a
            INNER JOIN student s ON a.StudentID = s.StudentID
            LEFT JOIN jobapplication ja ON a.StudentID = ja.StudentID AND a.HostOrgID = ja.HostOrgID
            LEFT JOIN attachmentopportunity ao ON ja.OpportunityID = ao.OpportunityID
            WHERE a.HostOrgID = ?
            ORDER BY a.StartDate DESC
            LIMIT 5
        ");
        $stmt->bind_param("i", $hostOrgId);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function updateProfile($hostId, $data) {
        $stmt = $this->conn->prepare("UPDATE hostorganization SET OrganizationName = ?, ContactPerson = ?, Email = ?, PhoneNumber = ? WHERE HostOrgID = ?");
        $stmt->bind_param("ssssi", $data['org_name'], $data['contact_person'], $data['email'], $data['phone'], $hostId);
        return $stmt->execute();
    }

    public function completeProfile($hostId, $data) {
        $stmt = $this->conn->prepare("UPDATE hostorganization SET OrganizationName=?, ContactPerson=?, Email=?, PhoneNumber=? WHERE HostOrgID=?");
        $stmt->bind_param("ssssi", $data['org_name'], $data['contact_person'], $data['email'], $data['phone'], $hostId);
        return $stmt->execute();
    }

    public function getAttachedStudents($hostOrgId) {
        $stmt = $this->conn->prepare("
            SELECT s.StudentID, s.FirstName, s.LastName, s.Course, s.YearOfStudy, a.AttachmentID, a.StartDate, a.EndDate, a.AttachmentStatus, a.AssessmentCode
            FROM attachment a
            JOIN student s ON a.StudentID = s.StudentID
            WHERE a.HostOrgID = ?
            ORDER BY a.StartDate DESC
        ");
        $stmt->bind_param("i", $hostOrgId);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function generateAssessmentCode($attachmentId, $hostOrgId) {
        // Verify ownership
        $verifyStmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE AttachmentID = ? AND HostOrgID = ?");
        $verifyStmt->bind_param("ii", $attachmentId, $hostOrgId);
        $verifyStmt->execute();
        if ($verifyStmt->get_result()->num_rows === 0) return false;

        $code = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6));
        $updateStmt = $this->conn->prepare("UPDATE attachment SET AssessmentCode = ? WHERE AttachmentID = ?");
        $updateStmt->bind_param("si", $code, $attachmentId);
        return $updateStmt->execute();
    }

    public function createFromRegistration($data) {
        $this->conn->begin_transaction();
        try {
            // 1. Create User Account
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $userStmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
            $userStmt->bind_param("ss", $data['username'], $hashedPassword);
            if (!$userStmt->execute()) throw new \Exception("Could not create user account.");
            $userId = $this->conn->insert_id;
            $userStmt->close();

            // 2. Create Host Organization Profile
            $hostStmt = $this->conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email, PhoneNumber) VALUES (?, ?, ?, ?, ?)");
            $hostStmt->bind_param("issss", $userId, $data['org_name'], $data['contact_person'], $data['email'], $data['phone']);
            if (!$hostStmt->execute()) throw new \Exception("Could not create host profile.");
            $hostStmt->close();

            $this->conn->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
