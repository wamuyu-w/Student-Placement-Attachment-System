<?php
namespace App\Models;
use App\Config\Database;
/**
 * Class Host
 * 
 * Manages operations and data related to Host Organizations.
 * Includes methods for retrieving statistics, managing attached students,
 * updating profiles, and generating secure verification codes for assessments.
 */
class Host {
    private $db;
    private $conn;

    /**
     * Initializes the database connection.
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }
    /**
     * Retrieves aggregated statistics for a specific host organization's dashboard.
     * Counts active opportunities, attached students, and pending logbooks.
     * 
     * @param int $hostOrgId The host organization ID
     * @return array Associative array of statistics
     */
    public function getDashboardStats($hostOrgId) {
        $stmt = $this->conn->prepare("
            SELECT 
                (SELECT COUNT(*) FROM attachmentopportunity WHERE HostOrgID = ? AND Status = 'Active' AND ApplicationEndDate >= CURDATE()) as active_placements,
                (SELECT COUNT(*) FROM attachment WHERE HostOrgID = ? AND AttachmentStatus IN ('Active', 'Ongoing')) as students_attached,
                (SELECT COUNT(*) FROM attachment a 
                  INNER JOIN logbook l ON a.AttachmentID = l.AttachmentID 
                  WHERE a.HostOrgID = ? AND l.Status = 'Pending') as pending_logbooks
        ");
        $stmt->bind_param("iii", $hostOrgId, $hostOrgId, $hostOrgId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    /**
     * Fetches the most recent student placements at this host organization.
     * 
     * @param int $hostOrgId The host organization ID
     * @return \mysqli_result|false Result set
     */
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
    /**
     * Updates the basic profile information of a host organization.
     * 
     * @param int $hostId
     * @param array $data Associative array of profile fields
     * @return bool True on successful update
     */
    public function updateProfile($hostId, $data) {
        $stmt = $this->conn->prepare("UPDATE hostorganization SET OrganizationName = ?, ContactPerson = ?, Email = ?, PhoneNumber = ? WHERE HostOrgID = ?");
        $stmt->bind_param("ssssi", $data['org_name'], $data['contact_person'], $data['email'], $data['phone'], $hostId);
        return $stmt->execute();
    }

    /**
     * Finalizes the host profile data during the first-login initialization process.
     * 
     * @param int $hostId
     * @param array $data Profile fields
     * @return bool True on success
     */
    public function completeProfile($hostId, $data) {
        $stmt = $this->conn->prepare("UPDATE hostorganization SET OrganizationName=?, ContactPerson=?, Email=?, PhoneNumber=? WHERE HostOrgID=?");
        $stmt->bind_param("ssssi", $data['org_name'], $data['contact_person'], $data['email'], $data['phone'], $hostId);
        return $stmt->execute();
    }

    /**
     * Retrieves a list of all students currently attached to this host organization.
     * 
     * @param int $hostOrgId
     * @return \mysqli_result|false Result set
     */
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
    /**
     * Generates a secure, 6-character alphanumeric verification code for a specific attachment.
     * This code must be shared with the academic supervisor to authorize an assessment.
     * 
     * @param int $attachmentId
     * @param int $hostOrgId Prevents unauthorized generation by validating ownership
     * @return bool True on successful generation and update
     */
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

    /**
     * Creates a new host organization profile and its associated user account.
     * Used by admins to manually register new host entities.
     * 
     * @param array $data Form data including credentials and organization details
     * @return array Success status and optional error message
     */
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
            
            if (!empty($data['email'])) {
                \App\Core\Mailer::sendHostCredentials($data['email'], $data['org_name'], $data['username'], $data['password']);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
