<?php
namespace App\Models;
use App\Config\Database;
/**
 * Class Opportunity
 * 
 * Model for managing attachment job opportunities posted by host organizations,
 * including CRUD operations and handling student applications to these specific roles.
 */
class Opportunity {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Retrieves all opportunities across all host organizations.
     * Used primarily by administrators.
     * 
     * @return \mysqli_result|false Result set
     */
    public function getAll() {
        $sql = "SELECT ao.*, COALESCE(ho.OrganizationName, 'Unknown') as OrganizationName 
                FROM attachmentopportunity ao
                LEFT JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
                ORDER BY ao.ApplicationEndDate DESC";
        return $this->conn->query($sql);
    }

    /**
     * Retrieves a simplified list of all host organizations.
     * Used for populating dropdowns during opportunity creation.
     * 
     * @return \mysqli_result|false Result set
     */
    public function getHostOrganizations() {
        return $this->conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY OrganizationName");
    }
    /**
     * Retrieves all currently active and open opportunities.
     * Validates that the current date falls within the application window.
     * 
     * @return \mysqli_result|false Result set
     */
    public function getAllActive() {
        $sql = "SELECT ao.*, ho.OrganizationName 
                FROM attachmentopportunity ao
                JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
                WHERE ao.Status = 'Active' 
                  AND ao.ApplicationEndDate >= CURDATE() 
                  AND ao.ApplicationStartDate <= CURDATE()
                ORDER BY ao.ApplicationEndDate ASC";
        return $this->conn->query($sql);
    }

    /**
     * Retrieves all opportunities posted by a specific host organization.
     * 
     * @param int $hostId
     * @return \mysqli_result|false Result set
     */
    public function getByHost($hostId) {
        $stmt = $this->conn->prepare("SELECT *, DATE_ADD(ApplicationEndDate, INTERVAL 0 DAY) as daysUntilExpire FROM attachmentopportunity WHERE HostOrgID = ? ORDER BY ApplicationEndDate DESC");
        $stmt->bind_param("i", $hostId);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Finds a specific opportunity by its ID.
     * 
     * @param int $id
     * @return array|null Associative array of opportunity data or null
     */
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM attachmentopportunity WHERE OpportunityID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Checks if a specific student has already applied to a specific opportunity.
     * 
     * @param int $studentId
     * @param int $opportunityId
     * @return bool True if they have applied
     */
    public function hasApplied($studentId, $opportunityId) {
        $stmt = $this->conn->prepare("SELECT OpportunityID FROM jobapplication WHERE StudentID = ? AND OpportunityID = ?");
        $stmt->bind_param("ii", $studentId, $opportunityId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Retrieves all opportunity IDs that a student has applied to.
     * 
     * @param int $studentId
     * @return array Array of Opportunity IDs
     */
    public function getAppliedOpportunityIds($studentId) {
        $stmt = $this->conn->prepare("SELECT OpportunityID FROM jobapplication WHERE StudentID = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['OpportunityID'];
        }
        return $ids;
    }

    /**
     * Processes a student's application to a specific job opportunity.
     * Validates deadline, active status, and prevents duplicate applications.
     * 
     * @param array $data Application details including resume and motivation
     * @return array Success status and optional error message
     */
    public function createApplication($data) {
        $this->conn->begin_transaction();

        try {
            // Check if opportunity exists and deadline
            $opportunity = $this->findById($data['opportunity_id']);
            if (!$opportunity) {
                throw new \Exception('Opportunity not found.');
            }
            if ($opportunity['Status'] !== 'Active') {
                throw new \Exception('This opportunity is closed.');
            }
            if (strtotime($opportunity['ApplicationEndDate']) < time()) {
                throw new \Exception('Application deadline has passed.');
            }

            // Check for duplicate application
            if ($this->hasApplied($data['student_id'], $data['opportunity_id'])) {
                throw new \Exception('You have already applied to this opportunity.');
            }

            $resumePathToStore = $data['resume_path'] ?? null;
            $resumeLinkToStore = $data['resume_link'] ?? null;

            if (empty($resumePathToStore) && empty($resumeLinkToStore)) {
                throw new \Exception('A resume file or link must be provided.');
            }
            if (!empty($resumeLinkToStore) && !filter_var($resumeLinkToStore, FILTER_VALIDATE_URL)) {
                throw new \Exception('If provided, resume link must be a valid URL.');
            }

            // Insert into jobapplication
            $stmt = $this->conn->prepare(
                "INSERT INTO jobapplication (OpportunityID, HostOrgID, StudentID, ApplicationDate, Status, ResumePath, ResumeLink, Motivation)
                 VALUES (?, ?, ?, NOW(), 'Pending', ?, ?, ?)"
            );

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

    /**
     * Saves or updates an opportunity.
     * Also supports "on-the-fly" creation of a new Host Organization by an administrator,
     * including generating a temporary account and sending a welcome email.
     * 
     * @param array $data Form data for the opportunity and potentially new host
     * @return array Success status and optional error message
     */
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
                    // Create a Host Organization if it does nto exist
                    $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $orgName));

                    //assign a username in the format Hxxx THEN GENERATE A RANDOM PASSWORD
                    $username = $baseUsername . '_' . bin2hex(random_bytes(2));
                    $tempPassword = bin2hex(random_bytes(4));
                    $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
                    

                    //Add the new user to the db 
                    $userStmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
                    $userStmt->bind_param("ss", $username, $hashedPassword);
                    $userStmt->execute();
                    $userId = $this->conn->insert_id;

                    //send an email to the specified address about their new account creation in the system
                    //this is necessary for viewing job applications from CUEA
                    $defaultEmail = $username . '@example.com';
                    $hostStmt = $this->conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, Email, PhoneNumber) VALUES (?, ?, ?, 'Pending')");
                    $hostStmt->bind_param("iss", $userId, $orgName, $defaultEmail);
                    $hostStmt->execute();
                    $hostOrgId = $this->conn->insert_id;

                    $newHostDetails = [
                        'email'    => $defaultEmail,
                        'orgName'  => $orgName,
                        'username' => $username,
                        'password' => $tempPassword
                    ];
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

            if (isset($newHostDetails)) {
                \App\Core\Mailer::sendHostCredentials($newHostDetails['email'], $newHostDetails['orgName'], $newHostDetails['username'], $newHostDetails['password']);
            }

            return ['success' => true];

        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Deletes an opportunity if it has no dependent applications.
     * 
     * @param int $id
     * @return array Success status or failure reason
     */
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
