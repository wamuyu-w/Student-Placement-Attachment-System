<?php
namespace App\Models;
use App\Config\Database;

/**
 * Class Application
 * 
 * Manages database operations related to student placement applications.
 * Handles both specific job applications (to host opportunities) and general program 
 * session applications (self-sourced placements).
 */
class Application {
    private $db;
    private $conn;

    /**
     * Initializes the database connection for application operations.
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Retrieves a list of all job applications with comprehensive student and opportunity details.
     * Used primarily by administrators.
     * 
     * @return \mysqli_result|false Result set
     */
    public function getAllJobApplications() {
        $sql = "SELECT ja.OpportunityID, ja.StudentID, ja.ApplicationDate, s.FirstName, s.LastName, ao.Description, h.OrganizationName, ja.Status
                FROM jobapplication ja
                JOIN student s ON ja.StudentID = s.StudentID
                JOIN attachmentopportunity ao ON ja.OpportunityID = ao.OpportunityID
                JOIN hostorganization h ON ja.HostOrgID = h.HostOrgID
                ORDER BY ja.ApplicationDate DESC LIMIT 50";
        return $this->conn->query($sql);
    }

    /**
     * Retrieves all program (session) applications across all students and host organizations.
     * 
     * @return \mysqli_result|false Result set
     */
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

    /**
     * Updates the status of a program application.
     * Optionally records a rejection reason if the application is declined.
     * 
     * @param int $appId The application ID
     * @param string $status The new status (e.g., 'Approved', 'Rejected')
     * @param string|null $rejectionReason Optional reason for rejection
     * @return bool True on success
     */
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

    /**
     * Approves a program application and dynamically creates a corresponding active attachment record.
     * Executes within a transaction to ensure data integrity.
     * 
     * @param int $appId The application ID
     * @param string $financialClearance The financial clearance status
     * @return array Array containing success status and related IDs
     */
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
                 VALUES (?, ?, ?, ?, ?, 'Ongoing', NOW())"
            );
            $ins->bind_param("iisss", $studentId, $hostOrgId, $startDate, $endDate, $financialClearance);
            $ins->execute();

            $this->conn->commit();
            return ['success' => true, 'student_id' => $studentId, 'host_org_id' => $hostOrgId];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Retrieves all job applications submitted to a specific host organization.
     * 
     * @param int $hostOrgId The host organization ID
     * @return \mysqli_result|false Result set
     */
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

    /**
     * Verifies the existence of an application and returns its data row.
     * Ensures authorization by strictly matching Opportunity, Student, and Host IDs.
     * 
     * @param int $opportunityId
     * @param int $studentId
     * @param int $hostOrgId
     * @return array|null The associative array of application data, or null if not found
     */
    public function verifyAndGetApplication($opportunityId, $studentId, $hostOrgId) {
        $stmt = $this->conn->prepare("SELECT * FROM jobapplication WHERE OpportunityID = ? AND StudentID = ? AND HostOrgID = ?");
        $stmt->bind_param("iii", $opportunityId, $studentId, $hostOrgId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Updates the status of a job application.
     * Includes handling for optional rejection reasons.
     * 
     * @param int $opportunityId
     * @param int $studentId
     * @param string $status
     * @param string|null $rejectionReason
     * @return bool True on success
     */
    public function updateJobStatus($opportunityId, $studentId, $status, $rejectionReason = null) {
        $this->ensureJobApplicationCols();
        if ($status === 'Rejected' && $rejectionReason) {
            $stmt = $this->conn->prepare("UPDATE jobapplication SET Status = ?, RejectionReason = ? WHERE OpportunityID = ? AND StudentID = ?");
            $stmt->bind_param("ssii", $status, $rejectionReason, $opportunityId, $studentId);
        } else {
            $stmt = $this->conn->prepare("UPDATE jobapplication SET Status = ? WHERE OpportunityID = ? AND StudentID = ?");
            $stmt->bind_param("sii", $status, $opportunityId, $studentId);
        }
        return $stmt->execute();
    }
    
    /**
     * Ensures the jobapplication table has the RejectionReason column.
     * Automatically adds it if missing to prevent SQL errors on legacy DB structures.
     * 
     * @return void
     */
    private function ensureJobApplicationCols() {
        $colCheck = $this->conn->query("SHOW COLUMNS FROM jobapplication LIKE 'RejectionReason'");
        if ($colCheck && $colCheck->num_rows == 0) {
            try {
                $this->conn->query("ALTER TABLE jobapplication ADD COLUMN RejectionReason TEXT NULL");
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Retrieves all program (session) applications for a specific student.
     * 
     * @param int $studentId
     * @return \mysqli_result|false Result set
     */
    public function getStudentApplications($studentId) {
        $stmt = $this->conn->prepare(
            "SELECT *, RejectionReason, FinancialClearanceStatus 
             FROM attachmentapplication WHERE StudentID = ? ORDER BY ApplicationDate DESC"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Checks if a student currently has an active, ongoing attachment.
     * 
     * @param int $studentId
     * @return bool True if they have an ongoing attachment
     */
    public function hasActiveAttachment($studentId) {
        $stmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Determines if a student has any pending or approved program applications.
     * Used to prevent duplicate concurrent applications.
     * 
     * @param int $studentId
     * @return bool True if they have an active app in progress
     */
    public function hasPendingOrApprovedApp($studentId) {
        $stmt = $this->conn->prepare("SELECT ApplicationID FROM attachmentapplication WHERE StudentID = ? AND (ApplicationStatus = 'Pending' OR ApplicationStatus = 'Approved')");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Processes a new self-sourced placement session application from a student.
     * Dynamically creates a new Host Organization account if one doesn't exist,
     * including generating a temporary password and triggering a welcome email.
     * 
     * @param int $studentId
     * @param array $data Form submission data
     * @return array Success status and message
     */
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
                    $contactPhone = $data['contact_phone'] ?? '';

                    // Generate Username
                    $userStmt = $this->conn->query("SELECT Username FROM users WHERE Role = 'Host Organization' ORDER BY UserID DESC LIMIT 1");
                    $lastUsername = "H000";
                    if ($userStmt->num_rows > 0) {
                        $lastUsername = $userStmt->fetch_assoc()['Username'];
                    }

                    //generating their credentials e.g. username and password
                    $num = intval(substr($lastUsername, 1));
                    $newUsername = 'H' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
                    $rawPassword = bin2hex(random_bytes(8));
                    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

                    //add the host org to the list of active users in the db
                    $insertUser = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
                    $insertUser->bind_param("ss", $newUsername, $hashedPassword);
                    $insertUser->execute();
                    $newUserId = $this->conn->insert_id;


                    //add to the host org table since there's more details that have been recorded
                    $insertHost = $this->conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email, PhoneNumber) VALUES (?, ?, ?, ?, ?)");
                    $insertHost->bind_param("issss", $newUserId, $intendedHost, $contactPerson, $contactEmail, $contactPhone);
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

            // additional field such as 
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
                try {
                    \App\Core\Mailer::sendHostCredentials($newHostDetails['email'], $newHostDetails['orgName'], $newHostDetails['username'], $newHostDetails['password']);
                } catch (\Throwable $mailerEx) {
                    // Fallback: log credentials so admin can retrieve them manually
                    error_log("[HOST CREDENTIALS] Email failed for '{$newHostDetails['orgName']}'. Username: {$newHostDetails['username']} | Password: {$newHostDetails['password']} | Error: " . $mailerEx->getMessage());
                }
            }

            return ['success' => true];

        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Registers a finalized placement for a student, inserting a new attachment record.
     * Strictly blocks execution if an active attachment already exists.
     * 
     * @param int $studentId
     * @param array $data Form data including dates and host ID
     * @return array Success status
     */
    public function registerPlacement($studentId, $data) {
        // Only block if there is an ACTIVE ongoing attachment
        $checkStmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
        $checkStmt->bind_param("i", $studentId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'already_active'];
        }

        $stmt = $this->conn->prepare("INSERT INTO attachment (StudentID, HostOrgID, StartDate, EndDate, ClearanceStatus, AttachmentStatus) VALUES (?, ?, ?, ?, 'Pending', 'Ongoing')");
        $stmt->bind_param("iiss", $studentId, $data['host_org_id'], $data['start_date'], $data['end_date']);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => $this->conn->error];
    }

    /**
     * Retrieves a simplified list of all registered host organizations.
     * 
     * @return \mysqli_result|false Result set
     */
    public function getAllHosts() {
        return $this->conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY OrganizationName");
    }

    /**
     * Retrieves a specific application record by its primary key ID.
     * 
     * @param int $appId
     * @return array|null Associative array of data or null if not found
     */
    public function getApplicationById($appId) {
        $stmt = $this->conn->prepare("SELECT * FROM attachmentapplication WHERE ApplicationID = ?");
        $stmt->bind_param("i", $appId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
