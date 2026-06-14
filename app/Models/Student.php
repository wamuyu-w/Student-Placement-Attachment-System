<?php
namespace App\Models;
use App\Config\Database;
/**
 * Class Student
 * 
 * Model for handling database operations related to Students.
 * Manages registration, profile updates, clearance processes, and fetching dashboards.
 */
class Student {
    private $db;
    private $conn;

    /**
     * Initializes the database connection for student operations.
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Retrieves all registered students.
     * Joined with the `users` table to fetch the Admission Number (Username).
     * 
     * @return \mysqli_result|false Result set
     */
    public function getAll() {
        // Join with users to get Admission Number (Username)
        return $this->conn->query("SELECT s.StudentID, s.FirstName, s.LastName, s.Course, s.Faculty, s.Department, s.YearOfStudy, s.Email, s.EligibilityStatus, u.Username as AdmissionNumber FROM student s JOIN users u ON s.UserID = u.UserID ORDER BY s.StudentID DESC");
    }

    /**
     * Retrieves a specific student's full details by ID.
     * 
     * @param int $studentId
     * @return array|null Associative array of student data or null if not found
     */
    public function getById($studentId) {
        $stmt = $this->conn->prepare("SELECT s.*, u.Username as AdmissionNumber FROM student s JOIN users u ON s.UserID = u.UserID WHERE s.StudentID = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Checks if a given email is already associated with an existing student.
     * 
     * @param string $email
     * @return bool True if the email is taken
     */
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT StudentID FROM student WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Incomplete/deprecated method. Handles legacy student registration logic.
     * 
     * @param array $userData
     * @param array $studentData
     * @return bool
     */
    public function register($userData, $studentData) {
        $this->conn->begin_transaction();
        try {
            // Insert User
            $stmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Student', 'Active')");
            $stmt->bind_param("ss", $userData['username'], $userData['password']);
            $stmt->execute();
            $userId = $this->conn->insert_id;

            // Insert Student (Logic from process-signup-student.php)
            // ... (Add the rest of the insert logic here)
            
            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * Creates a new student record and associated user account.
     * Typically used by Administrators for manual or bulk addition.
     * 
     * @param array $data Contains admNumber, firstName, lastName, etc.
     * @return bool True on successful creation, false if skipped (in bulk)
     * @throws \Exception If duplicate or DB error occurs during single creation
     */
    public function createFromAdmin($data) {
        $this->conn->begin_transaction();
        try {
            // Check if user exists
            $check = $this->conn->prepare("SELECT UserID FROM users WHERE Username = ?");
            $check->bind_param("s", $data['admNumber']);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                // If bulk upload, we might want to skip instead of throw, but for single add we throw
                if (empty($data['is_bulk'])) {
                    throw new \Exception("Student with this Admission Number already exists.");
                }
                return false; // Skip duplicate in bulk
            }

            $defaultPassword = password_hash('Changeme123!', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Student', 'Active')");
            $stmt->bind_param("ss", $data['admNumber'], $defaultPassword);
            $stmt->execute();
            $userId = $this->conn->insert_id;

            $firstName = $data['firstName'] ?? 'New';
            $lastName = $data['lastName'] ?? 'Student';
            $faculty = $data['faculty'] ?? 'Unknown';
            $department = $data['department'] ?? 'Unknown';

            $stmt2 = $this->conn->prepare("INSERT INTO student (UserID, FirstName, LastName, Faculty, Department, EligibilityStatus) VALUES (?, ?, ?, ?, ?, 'Pending')");
            $stmt2->bind_param("issss", $userId, $firstName, $lastName, $faculty, $department);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * Clears a student after successful completion of their attachment.
     * Updates student eligibility, attachment status, and deactivates their user account.
     * 
     * @param int $id The student ID
     * @return array Success status and optional error message
     */
    public function clearStudent($id) {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE student SET EligibilityStatus = 'Cleared' WHERE StudentID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt2 = $this->conn->prepare("UPDATE attachment SET AttachmentStatus = 'Completed', EndDate = COALESCE(EndDate, CURDATE()) WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $stmt3 = $this->conn->prepare("UPDATE users u JOIN student s ON u.UserID = s.UserID SET u.Status = 'Inactive' WHERE s.StudentID = ?");
            $stmt3->bind_param("i", $id);
            $stmt3->execute();

            $this->conn->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Updates a student's basic contact information.
     * 
     * @param int $studentId
     * @param array $data Form data
     * @return bool True on success
     */
    public function updateProfile($studentId, $data) {
        $stmt = $this->conn->prepare("UPDATE student SET PhoneNumber = ?, Email = ? WHERE StudentID = ?");
        $stmt->bind_param("ssi", $data['phone'], $data['email'], $studentId);
        return $stmt->execute();
    }

    /**
     * Completes a student's profile during their first login.
     * Transitions their EligibilityStatus to 'Eligible'.
     * 
     * @param int $studentId
     * @param array $data Form data
     * @return bool True on success
     */
    public function completeProfile($studentId, $data) {
        $stmt = $this->conn->prepare("UPDATE student SET FirstName=?, LastName=?, Email=?, PhoneNumber=?, Course=?, Faculty=?, YearOfStudy=?, EligibilityStatus='Eligible' WHERE StudentID=?");
        $stmt->bind_param("ssssssii", $data['firstName'], $data['lastName'], $data['email'], $data['phone'], $data['course'], $data['faculty'], $data['yearOfStudy'], $studentId);
        return $stmt->execute();
    }

    /**
     * Retrieves aggregated statistics for a student's dashboard.
     * 
     * @param int $studentId
     * @return array Associative array (myApplications, activePlacement, availableOpportunities, pendingTasks)
     */
    public function getDashboardStats($studentId) {
        $stats = [];

        // 1. Applications Count
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM attachmentapplication WHERE StudentID = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $stats['myApplications'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
        $stmt->close();

        // 2. Active Placement
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Active'");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $stats['activePlacement'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
        $stmt->close();

        // 3. Available Opportunities
        $result = $this->conn->query("SELECT COUNT(*) as count FROM attachmentopportunity WHERE Status = 'Active' AND ApplicationEndDate >= CURDATE() AND ApplicationStartDate <= CURDATE()");
        $stats['availableOpportunities'] = $result->fetch_assoc()['count'] ?? 0;

        // 4. Pending Tasks
        $stmt = $this->conn->prepare("SELECT 
            (SELECT COUNT(*) FROM logbook lb JOIN attachment a ON lb.AttachmentID = a.AttachmentID WHERE a.StudentID = ? AND lb.Status = 'Pending') +
            (SELECT COUNT(*) FROM finalreport fr JOIN attachment a ON fr.AttachmentID = a.AttachmentID WHERE a.StudentID = ? AND fr.Status = 'Pending') as count");
        $stmt->bind_param("ii", $studentId, $studentId);
        $stmt->execute();
        $stats['pendingTasks'] = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
        $stmt->close();

        return $stats;
    }

    /**
     * Compiles a chronological timeline of a student's recent activities.
     * Combines job applications, placement updates, and supervisor assignments.
     * 
     * @param int $studentId
     * @return array Array of formatted activity entries
     */
    public function getRecentActivities($studentId) {
        $activities = [];

        // Applications
        $stmt = $this->conn->prepare("SELECT ApplicationDate, ApplicationStatus FROM attachmentapplication WHERE StudentID = ? ORDER BY ApplicationDate DESC LIMIT 3");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $activities[] = [
                'avatar' => \App\Core\Helpers::getAvatar('Application', '#8B1538', '#fff', 'activity-avatar'),
                'title' => 'Application submitted',
                'description' => 'Status: ' . htmlspecialchars($row['ApplicationStatus'] ?? 'Pending'),
                'time' => $row['ApplicationDate']
            ];
        }
        $stmt->close();

        // Placements
        $stmt = $this->conn->prepare(
            "SELECT ho.OrganizationName, a.StartDate, a.AttachmentStatus, a.ClearedAt FROM attachment a JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID WHERE a.StudentID = ? ORDER BY a.StartDate DESC LIMIT 3"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $timestamp = $row['ClearedAt'] ?? $row['StartDate'];
            $activities[] = [
                'avatar' => \App\Core\Helpers::getAvatar($row['OrganizationName'], '#10b981', '#fff', 'activity-avatar'),
                'title' => 'Placement at ' . htmlspecialchars($row['OrganizationName']),
                'description' => 'Status: ' . htmlspecialchars($row['AttachmentStatus'] ?? ''),
                'time' => $timestamp
            ];
        }
        $stmt->close();

        // Supervision
        $stmt = $this->conn->prepare("SELECT l.Name as LecturerName, a.StartDate FROM supervision su JOIN attachment a ON su.AttachmentID = a.AttachmentID JOIN lecturer l ON su.LecturerID = l.LecturerID WHERE a.StudentID = ? ORDER BY a.StartDate DESC LIMIT 3");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $activities[] = [
                'avatar' => \App\Core\Helpers::getAvatar($row['LecturerName'], '#f59e0b', '#fff', 'activity-avatar'),
                'title' => 'Supervisor assigned',
                'description' => htmlspecialchars($row['LecturerName'] ?? ''),
                'time' => $row['StartDate']
            ];
        }
        $stmt->close();

        // Sort by time descending and limit to 4
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 4);
    }

    /**
     * Retrieves the academic supervisors assigned to a student's active attachment.
     * 
     * @param int $studentId
     * @return array Array of supervisor details
     */
    public function getSupervisors($studentId) {
        $stmt = $this->conn->prepare("
            SELECT l.Name, l.Department, l.Faculty, l.StaffNumber, a.StartDate as AssignedDate
            FROM supervision s
            JOIN lecturer l ON s.LecturerID = l.LecturerID
            JOIN attachment a ON s.AttachmentID = a.AttachmentID
            WHERE a.StudentID = ? AND a.AttachmentStatus = 'Ongoing'
        ");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $supervisors = [];
        while ($row = $result->fetch_assoc()) {
            $supervisors[] = $row;
        }
        return $supervisors;
    }

    /**
     * Retrieves all assessments (Scheduled and Completed) linked to a student's active attachment.
     * 
     * @param int $studentId
     * @return array Array of assessment details
     */
    public function getAssessments($studentId) {
        $stmt = $this->conn->prepare("
            SELECT ass.*, l.Name as LecturerName
            FROM assessment ass
            JOIN attachment att ON ass.AttachmentID = att.AttachmentID
            LEFT JOIN lecturer l ON ass.LecturerID = l.LecturerID
            WHERE att.StudentID = ? AND att.AttachmentStatus = 'Ongoing'
            ORDER BY ass.AssessmentDate DESC
        ");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $assessments = [];
        while ($row = $result->fetch_assoc()) {
            $assessments[] = $row;
        }
        return $assessments;
    }
}
