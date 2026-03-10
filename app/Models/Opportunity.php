<?php
namespace App\Models;
use App\Config\Database;
// Model for managing attachment opportunities, including CRUD operations and application handling.
class Opportunity {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    // --- Admin Methods ---
    public function getAll() {
        $sql = "SELECT ao.*, COALESCE(ho.OrganizationName, 'Unknown') as OrganizationName 
                FROM attachmentopportunity ao
                LEFT JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
                ORDER BY ao.ApplicationEndDate DESC";
        return $this->conn->query($sql);
    }

    public function getHostOrganizations() {
        return $this->conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY OrganizationName");
    }

    // --- Student/Public Methods ---
    public function getAllActive() {
        $sql = "SELECT ao.*, ho.OrganizationName 
                FROM attachmentopportunity ao
                JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
                WHERE ao.Status = 'Active' AND ao.ApplicationEndDate >= CURDATE()
                ORDER BY ao.ApplicationEndDate ASC";
        return $this->conn->query($sql);
    }

    // --- Host Methods ---
    public function getByHost($hostId) {
        $stmt = $this->conn->prepare("SELECT *, DATE_ADD(ApplicationEndDate, INTERVAL 0 DAY) as daysUntilExpire FROM attachmentopportunity WHERE HostOrgID = ? ORDER BY ApplicationEndDate DESC");
        $stmt->bind_param("i", $hostId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM attachmentopportunity WHERE OpportunityID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function hasApplied($studentId, $opportunityId) {
        $stmt = $this->conn->prepare("SELECT ApplicationID FROM jobapplication WHERE StudentID = ? AND OpportunityID = ?");
        $stmt->bind_param("ii", $studentId, $opportunityId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function createApplication($data) {
        $this->conn->begin_transaction();

        try {
            // Check if opportunity exists and deadline
            $opportunity = $this->findById($data['opportunity_id']);
            if (!$opportunity) {
                throw new \Exception('Opportunity not found.');
            }
            if (strtotime($opportunity['ApplicationEndDate']) < time()) {
                throw new \Exception('Application deadline has passed.');
            }

            // Check for duplicate application
            if ($this->hasApplied($data['student_id'], $data['opportunity_id'])) {
                throw new \Exception('You have already applied to this opportunity.');
            }

            // Handle file upload
            $resumePathToStore = null;
            if (isset($data['resume_file']) && $data['resume_file']['error'] === UPLOAD_ERR_OK) {
                $file = $data['resume_file'];
                
                if ($file['size'] > 5242880) { // 5MB
                    throw new \Exception('File size exceeds 5MB limit.');
                }
                $allowedExtensions = ['pdf', 'doc', 'docx'];
                $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new \Exception('Only PDF, DOC, and DOCX files are allowed.');
                }

                $uploadsDir = __DIR__ . '/../../public/uploads/resumes/';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }

                $resumeFileName = 'resume_' . $data['student_id'] . '_' . $data['opportunity_id'] . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadsDir . $resumeFileName;

                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new \Exception('Failed to move uploaded resume.');
                }
                $resumePathToStore = $resumeFileName;
            }

            // Insert into jobapplication
            $stmt = $this->conn->prepare(
                "INSERT INTO jobapplication (OpportunityID, HostOrgID, StudentID, ApplicationDate, Status, ResumePath, ResumeLink, Motivation)
                 VALUES (?, ?, ?, NOW(), 'Pending', ?, ?, ?)"
            );

            $resumeLinkToStore = !empty($data['resume_link']) ? $data['resume_link'] : null;
            
            $stmt->bind_param(
                "iiisss",
                $data['opportunity_id'],
                $opportunity['HostOrgID'],
                $data['student_id'],
                $resumePathToStore,
                $resumeLinkToStore,
                $data['motivation']
            );

            if (!$stmt->execute()) {
                throw new \Exception('Database error: Failed to submit application.');
            }

            $this->conn->commit();
            return ['success' => true];

        } catch (\Exception $e) {
            $this->conn->rollback();
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // --- CRUD Operations ---
    public function save($data) {
        $this->conn->begin_transaction();
        try {
            $hostOrgId = $data['host_org_id'] ?? null;

            // Handle Admin creating a new Host Org on the fly
            if (isset($data['create_new_org']) && $data['create_new_org'] === true) {
                $orgName = $data['organization_name'];
                
                // Check if exists
                $stmt = $this->conn->prepare("SELECT HostOrgID FROM hostorganization WHERE OrganizationName = ?");
                $stmt->bind_param("s", $orgName);
                $stmt->execute();
                $res = $stmt->get_result();
                
                if ($row = $res->fetch_assoc()) {
                    $hostOrgId = $row['HostOrgID'];
                } else {
                    // Create User & Host Org
                    $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $orgName));
                    $username = $baseUsername . '_' . bin2hex(random_bytes(2));
                    $tempPassword = bin2hex(random_bytes(4));
                    $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
                    
                    $userStmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
                    $userStmt->bind_param("ss", $username, $hashedPassword);
                    $userStmt->execute();
                    $userId = $this->conn->insert_id;

                    $defaultEmail = $username . '@example.com';
                    $hostStmt = $this->conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, Email, PhoneNumber) VALUES (?, ?, ?, 'Pending')");
                    $hostStmt->bind_param("iss", $userId, $orgName, $defaultEmail);
                    $hostStmt->execute();
                    $hostOrgId = $this->conn->insert_id;
                }
            }

            if (!$hostOrgId) throw new \Exception("Host Organization ID is missing.");

            if (isset($data['opportunity_id']) && !empty($data['opportunity_id'])) {
                // Update
                $stmt = $this->conn->prepare("UPDATE attachmentopportunity SET HostOrgID=?, Description=?, EligibilityCriteria=?, ApplicationStartDate=?, ApplicationEndDate=?, Status=? WHERE OpportunityID=?");
                $stmt->bind_param("isssssi", 
                    $hostOrgId, 
                    $data['description'], 
                    $data['eligibility_criteria'], 
                    $data['start_date'], 
                    $data['end_date'], 
                    $data['status'], 
                    $data['opportunity_id']
                );
            } else {
                // Insert
                $stmt = $this->conn->prepare("INSERT INTO attachmentopportunity (HostOrgID, Description, EligibilityCriteria, ApplicationStartDate, ApplicationEndDate, Status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", 
                    $hostOrgId, 
                    $data['description'], 
                    $data['eligibility_criteria'], 
                    $data['start_date'], 
                    $data['end_date'], 
                    $data['status']
                );
            }

            if (!$stmt->execute()) throw new \Exception($stmt->error);
            
            $this->conn->commit();
            return ['success' => true];

        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id) {
        // Check dependencies
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM jobapplication WHERE OpportunityID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];
        
        if ($count > 0) {
            return ['success' => false, 'message' => "Cannot delete: $count applications exist. Close the opportunity instead."];
        }

        $stmt = $this->conn->prepare("DELETE FROM attachmentopportunity WHERE OpportunityID = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $this->conn->error];
        }
    }
}
