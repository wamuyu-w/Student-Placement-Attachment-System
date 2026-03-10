<?php
namespace App\Models;
use App\Config\Database;
// Student model for handling student-related database operations
// This model includes methods for retrieving student data, registering new students, updating profiles, and getting dashboard statistics and recent activities.
class Student {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAll() {
        // Join with users to get Admission Number (Username)
        return $this->conn->query("SELECT s.StudentID, s.FirstName, s.LastName, s.Course, s.Faculty, s.YearOfStudy, s.Email, s.EligibilityStatus, u.Username as AdmissionNumber FROM student s JOIN users u ON s.UserID = u.UserID ORDER BY s.StudentID DESC");
    }
// this function gets the students attached to a host organization and their details, including the status of their attachment
    public function getById($studentId) {
        $stmt = $this->conn->prepare("SELECT s.*, u.Username as AdmissionNumber FROM student s JOIN users u ON s.UserID = u.UserID WHERE s.StudentID = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT StudentID FROM student WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

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

            $stmt2 = $this->conn->prepare("INSERT INTO student (UserID, FirstName, LastName, Faculty, EligibilityStatus) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt2->bind_param("isss", $userId, $firstName, $lastName, $faculty);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function clearStudent($id) {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE student SET EligibilityStatus = 'Cleared' WHERE StudentID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt2 = $this->conn->prepare("UPDATE attachment SET AttachmentStatus = 'Completed', EndDate = COALESCE(EndDate, CURDATE()) WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $this->conn->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateProfile($studentId, $data) {
        $stmt = $this->conn->prepare("UPDATE student SET PhoneNumber = ?, Email = ? WHERE StudentID = ?");
        $stmt->bind_param("ssi", $data['phone'], $data['email'], $studentId);
        return $stmt->execute();
    }

    public function completeProfile($studentId, $data) {
        $stmt = $this->conn->prepare("UPDATE student SET FirstName=?, LastName=?, Email=?, PhoneNumber=?, Course=?, Faculty=?, YearOfStudy=?, EligibilityStatus='Eligible' WHERE StudentID=?");
        $stmt->bind_param("ssssssii", $data['firstName'], $data['lastName'], $data['email'], $data['phone'], $data['course'], $data['faculty'], $data['yearOfStudy'], $studentId);
        return $stmt->execute();
    }

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

    public function getRecentActivities($studentId) {
        $activities = [];

        // Applications
        $stmt = $this->conn->prepare("SELECT ApplicationDate, ApplicationStatus FROM attachmentapplication WHERE StudentID = ? ORDER BY ApplicationDate DESC LIMIT 3");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $activities[] = [
                'avatar' => 'https://ui-avatars.com/api/?name=Application&background=8B1538&color=fff&size=128',
                'title' => 'Application submitted',
                'description' => 'Status: ' . htmlspecialchars($row['ApplicationStatus'] ?? 'Pending'),
                'time' => $row['ApplicationDate']
            ];
        }
        $stmt->close();

        // Placements
        $stmt = $this->conn->prepare("SELECT ho.OrganizationName, a.StartDate, a.AttachmentStatus FROM attachment a JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID WHERE a.StudentID = ? ORDER BY a.StartDate DESC LIMIT 3");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $activities[] = [
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['OrganizationName']) . '&background=10b981&color=fff&size=128',
                'title' => 'Placement at ' . htmlspecialchars($row['OrganizationName']),
                'description' => 'Status: ' . htmlspecialchars($row['AttachmentStatus'] ?? ''),
                'time' => $row['StartDate']
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
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['LecturerName']) . '&background=f59e0b&color=fff&size=128',
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

    public function getSupervisors($studentId) {
        $stmt = $this->conn->prepare("
            SELECT l.Name, l.Department, l.Faculty, a.StartDate as AssignedDate
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
