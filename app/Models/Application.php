<?php
namespace App\Models;
use App\Config\Database;

class Application {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllJobApplications() {
        $sql = "SELECT ja.OpportunityID, ja.StudentID, ja.ApplicationDate, s.FirstName, s.LastName, ao.Description, h.OrganizationName, ja.Status
                FROM jobapplication ja
                JOIN student s ON ja.StudentID = s.StudentID
                JOIN attachmentopportunity ao ON ja.OpportunityID = ao.OpportunityID
                JOIN hostorganization h ON ja.HostOrgID = h.HostOrgID
                ORDER BY ja.ApplicationDate DESC LIMIT 50";
        return $this->conn->query($sql);
    }

    public function getAllProgramApplications() {
        $sql = "SELECT aa.ApplicationID, aa.StudentID, aa.ApplicationDate, aa.ApplicationStatus, 
                COALESCE(h.OrganizationName, aa.IntendedHostOrg) AS OrganizationName, 
                s.FirstName, s.LastName 
                FROM attachmentapplication aa
                JOIN student s ON aa.StudentID = s.StudentID
                LEFT JOIN hostorganization h ON aa.HostOrgID = h.HostOrgID
                ORDER BY aa.ApplicationDate DESC";
        return $this->conn->query($sql);
    }

    public function updateProgramStatus($appId, $status, $rejectionReason = null) {
        if ($status === 'Rejected' && $rejectionReason) {
            $stmt = $this->conn->prepare("UPDATE attachmentapplication SET ApplicationStatus = ?, RejectionReason = ? WHERE ApplicationID = ?");
            $stmt->bind_param("ssi", $status, $rejectionReason, $appId);
        } else {
            $stmt = $this->conn->prepare("UPDATE attachmentapplication SET ApplicationStatus = ? WHERE ApplicationID = ?");
            $stmt->bind_param("si", $status, $appId);
        }
        return $stmt->execute();
    }

    public function approveAndCreateAttachment($appId, $financialClearance = 'Cleared') {
        $this->conn->begin_transaction();
        try {
            // Get application details including student-supplied dates
            $stmt = $this->conn->prepare(
                "SELECT aa.StudentID, aa.HostOrgID, aa.IntendedHostOrg, aa.StartDate, aa.EndDate
                 FROM attachmentapplication aa WHERE aa.ApplicationID = ?"
            );
            $stmt->bind_param("i", $appId);
            $stmt->execute();
            $app = $stmt->get_result()->fetch_assoc();

            if (!$app) throw new \Exception("Application not found.");

            $studentId = $app['StudentID'];
            $hostOrgId = $app['HostOrgID'];

            $startDate = $app['StartDate'] ?: date('Y-m-d');
            $endDate   = $app['EndDate']   ?: date('Y-m-d', strtotime($startDate . ' +84 days'));

            $check = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
            $check->bind_param("i", $studentId);
            $check->execute();
            if ($check->get_result()->num_rows > 0)
                throw new \Exception("Student already has an ongoing attachment.");

            $upd = $this->conn->prepare("UPDATE attachmentapplication SET ApplicationStatus = 'Approved', FinancialClearanceStatus = ? WHERE ApplicationID = ?");
            $upd->bind_param("si", $financialClearance, $appId);
            $upd->execute();

            // Create attachment record with student-defined dates and ClearedAt = NOW()
            $ins = $this->conn->prepare(
                "INSERT INTO attachment (StudentID, HostOrgID, StartDate, EndDate, ClearanceStatus, AttachmentStatus, ClearedAt)
                 VALUES (?, ?, ?, ?, 'Cleared', 'Ongoing', NOW())"
            );
            $ins->bind_param("iiss", $studentId, $hostOrgId, $startDate, $endDate);
            $ins->execute();

            $this->conn->commit();
            return ['success' => true, 'student_id' => $studentId, 'host_org_id' => $hostOrgId];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getHostApplications($hostOrgId) {
        $sql = "SELECT ja.ApplicationDate, s.FirstName, s.LastName, s.Course, ao.Description, ja.Status, ja.OpportunityID, ja.StudentID, ja.ResumePath, ja.ResumeLink, ja.Motivation
                FROM jobapplication ja
                JOIN student s ON ja.StudentID = s.StudentID
                JOIN attachmentopportunity ao ON ja.OpportunityID = ao.OpportunityID
                WHERE ja.HostOrgID = ?
                ORDER BY ja.ApplicationDate DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $hostOrgId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function verifyAndGetApplication($opportunityId, $studentId, $hostOrgId) {
        $stmt = $this->conn->prepare("SELECT * FROM jobapplication WHERE OpportunityID = ? AND StudentID = ? AND HostOrgID = ?");
        $stmt->bind_param("iii", $opportunityId, $studentId, $hostOrgId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // --- Shared Methods ---
    public function updateJobStatus($opportunityId, $studentId, $status) {
        $stmt = $this->conn->prepare("UPDATE jobapplication SET Status = ? WHERE OpportunityID = ? AND StudentID = ?");
        $stmt->bind_param("sii", $status, $opportunityId, $studentId);
        return $stmt->execute();
    }

    public function getStudentApplications($studentId) {
        $stmt = $this->conn->prepare(
            "SELECT *, RejectionReason, FinancialClearanceStatus 
             FROM attachmentapplication WHERE StudentID = ? ORDER BY ApplicationDate DESC"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function hasActiveAttachment($studentId) {
        $stmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function hasPendingOrApprovedApp($studentId) {
        $stmt = $this->conn->prepare("SELECT ApplicationID FROM attachmentapplication WHERE StudentID = ? AND (ApplicationStatus = 'Pending' OR ApplicationStatus = 'Approved')");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createSessionApplication($studentId, $data) {
        $this->conn->begin_transaction();
        try {
            $hostOrgId = null;
            $intendedHost = $data['intended_host'] ?? null;
            
            if ($intendedHost) {
                // Check if Host Org exists
                $hostStmt = $this->conn->prepare("SELECT HostOrgID FROM hostorganization WHERE OrganizationName = ?");
                $hostStmt->bind_param("s", $intendedHost);
                $hostStmt->execute();
                $hostResult = $hostStmt->get_result();

                if ($hostResult->num_rows > 0) {
                    $hostOrgId = $hostResult->fetch_assoc()['HostOrgID'];
                } else {
                    // Create New Host Org
                    $contactPerson = $data['contact_person'] ?? '';
                    $contactEmail = $data['contact_email'] ?? '';

                    // Generate Username
                    $userStmt = $this->conn->query("SELECT Username FROM users WHERE Role = 'Host Organization' ORDER BY UserID DESC LIMIT 1");
                    $lastUsername = "H000";
                    if ($userStmt->num_rows > 0) {
                        $lastUsername = $userStmt->fetch_assoc()['Username'];
                    }
                    $num = intval(substr($lastUsername, 1));
                    $newUsername = 'H' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
                    $rawPassword = bin2hex(random_bytes(8));
                    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

                    $insertUser = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
                    $insertUser->bind_param("ss", $newUsername, $hashedPassword);
                    $insertUser->execute();
                    $newUserId = $this->conn->insert_id;

                    $insertHost = $this->conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email) VALUES (?, ?, ?, ?)");
                    $insertHost->bind_param("isss", $newUserId, $intendedHost, $contactPerson, $contactEmail);
                    $insertHost->execute();
                    $hostOrgId = $this->conn->insert_id;

                    $newHostDetails = [
                        'email'    => $contactEmail,
                        'orgName'  => $intendedHost,
                        'username' => $newUsername,
                        'password' => $rawPassword
                    ];
                }
            }

            $financialStatus = $data['financial_clearance_status'] ?? 'Pending';
            $startDate = !empty($data['start_date']) ? $data['start_date'] : null;
            $endDate   = !empty($data['end_date'])   ? $data['end_date']   : null;
            $stmt = $this->conn->prepare(
                "INSERT INTO attachmentapplication 
                 (StudentID, ApplicationDate, ApplicationStatus, IntendedHostOrg, HostOrgID, FinancialClearanceStatus, StartDate, EndDate)
                 VALUES (?, CURDATE(), 'Pending', ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("isisss", $studentId, $intendedHost, $hostOrgId, $financialStatus, $startDate, $endDate);
            
            if (!$stmt->execute()) throw new \Exception("Failed to submit application");
            
            $this->conn->commit();

            if (isset($newHostDetails) && !empty($newHostDetails['email'])) {
                \App\Core\Mailer::sendHostCredentials($newHostDetails['email'], $newHostDetails['orgName'], $newHostDetails['username'], $newHostDetails['password']);
            }

            return ['success' => true];

        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function registerPlacement($studentId, $data) {
        // Check for existing
        $checkStmt = $this->conn->prepare("SELECT AttachmentID, AttachmentStatus FROM attachment WHERE StudentID = ?");
        $checkStmt->bind_param("i", $studentId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['AttachmentStatus'] == 'Ongoing') {
                return ['success' => false, 'message' => 'already_active'];
            }
            return ['success' => false, 'message' => 'already_has_record'];
        }

        $stmt = $this->conn->prepare("INSERT INTO attachment (StudentID, HostOrgID, StartDate, EndDate, ClearanceStatus, AttachmentStatus) VALUES (?, ?, ?, ?, 'Pending', 'Ongoing')");
        $stmt->bind_param("iiss", $studentId, $data['host_org_id'], $data['start_date'], $data['end_date']);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => $this->conn->error];
    }

    public function getAllHosts() {
        return $this->conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY OrganizationName");
    }

    public function getApplicationById($appId) {
        $stmt = $this->conn->prepare("SELECT * FROM attachmentapplication WHERE ApplicationID = ?");
        $stmt->bind_param("i", $appId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
