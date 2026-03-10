<?php
namespace App\Models;
use App\Config\Database;
// supervisor model to handle supervisor related database operations, such as fetching supervisor details for a student, and any other supervisor related functions in the future
class Supervisor {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllSupervisors() {
        $sql = "SELECT l.Name, l.Department, l.Faculty, u.Status 
                FROM lecturer l 
                JOIN users u ON l.UserID = u.UserID 
                WHERE l.Role = 'Supervisor'";
        return $this->conn->query($sql);
    }

    public function staffNumberExists($staffNumber) {
        $stmt = $this->conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
        $stmt->bind_param("s", $staffNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function create($staffNumber) {
        $this->conn->begin_transaction();
        try {
            // 1. Generate L-Username (Find highest Lxxx and increment)
            $result = $this->conn->query("SELECT Username FROM users WHERE Username LIKE 'L%' ORDER BY CAST(SUBSTRING(Username, 2) AS UNSIGNED) DESC LIMIT 1");
            
            $nextNum = 1;
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $nextNum = (int)substr($row['Username'], 1) + 1;
            }
            
            $newUsername = 'L' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            $defaultPassword = 'Changeme123!';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            // 2. Create User Account
            $stmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Lecturer', 'Active')");
            $stmt->bind_param("ss", $newUsername, $hashedPassword);
            if (!$stmt->execute()) throw new \Exception($stmt->error);
            $userId = $this->conn->insert_id;

            // 3. Create Lecturer Record
            $stmt = $this->conn->prepare("INSERT INTO lecturer (UserID, StaffNumber, Role) VALUES (?, ?, 'Supervisor')");
            $stmt->bind_param("is", $userId, $staffNumber);
            if (!$stmt->execute()) throw new \Exception($stmt->error);

            $this->conn->commit();
            return ['success' => true, 'username' => $newUsername, 'password' => $defaultPassword];
        } catch (\Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function getStudentsForAssignment() {
        $sql = "
            SELECT a.AttachmentID, s.FirstName, s.LastName, h.OrganizationName,
                   (SELECT COUNT(*) FROM supervision WHERE AttachmentID = a.AttachmentID) as SupCount,
                   (SELECT COUNT(*) FROM assessment WHERE AttachmentID = a.AttachmentID) as AssessCount
            FROM attachment a
            JOIN student s ON a.StudentID = s.StudentID
            JOIN hostorganization h ON a.HostOrgID = h.HostOrgID
            WHERE a.AttachmentStatus = 'Ongoing'
            HAVING SupCount = 0 OR (SupCount = 1 AND AssessCount >= 1)
        ";
        return $this->conn->query($sql);
    }
    public function getAssignableLecturers() {
        $sql = "SELECT LecturerID, Name FROM lecturer WHERE Role = 'Supervisor' OR Role = 'Admin'";
        return $this->conn->query($sql);
    }
    public function assign($attachmentId, $lecturerId) {
        if (empty($attachmentId) || empty($lecturerId)) {
            return ['success' => false, 'message' => 'Missing data'];
        }

        $checkStmt = $this->conn->prepare("SELECT LecturerID FROM supervision WHERE AttachmentID = ?");
        $checkStmt->bind_param("i", $attachmentId);
        $checkStmt->execute();
        $res = $checkStmt->get_result();
        
        $supervisors = [];
        while ($row = $res->fetch_assoc()) {
            $supervisors[] = $row['LecturerID'];
        }
        $checkStmt->close();

        $assessStmt = $this->conn->prepare("SELECT COUNT(*) as count FROM assessment WHERE AttachmentID = ?");
        $assessStmt->bind_param("i", $attachmentId);
        $assessStmt->execute();
        $assessCount = $assessStmt->get_result()->fetch_assoc()['count'];
        $assessStmt->close();

        $canAssign = false;
        $errorMsg = "";

        if (count($supervisors) == 0) {
            $canAssign = true;
        } elseif (count($supervisors) == 1) {
            if (in_array($lecturerId, $supervisors)) {
                $errorMsg = "Supervisor already assigned";
            } elseif ($assessCount == 0) {
                $errorMsg = "First assessment must be completed before assigning a second supervisor";
            } else {
                $canAssign = true;
            }
        } else {
            $errorMsg = "Maximum of two supervisors already assigned";
        }
        
        if ($canAssign) {
            $stmt = $this->conn->prepare("INSERT INTO supervision (LecturerID, AttachmentID) VALUES (?, ?)");
            $stmt->bind_param("ii", $lecturerId, $attachmentId);
            
            if ($stmt->execute()) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Assignment failed'];
            }
        } else {
            return ['success' => false, 'message' => $errorMsg];
        }
    }
    public function createBulk($file, $faculty) {
        $handle = fopen($file, "r");
        if ($handle === FALSE) return ['successCount' => 0, 'errorCount' => 0];

        $successCount = 0;
        $errorCount = 0;
        $row = 0;
        $defaultPassword = password_hash('Changeme123!', PASSWORD_DEFAULT);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            if ($row == 1 && (strtolower($data[0]) == 'staffnumber' || strtolower($data[0]) == 'staffno')) continue;
            if (count($data) < 2 || empty($data[0])) continue;

            $staffNumber = \App\Core\Helpers::sanitize($data[0]);
            $name = \App\Core\Helpers::sanitize($data[1] ?? '');
            $department = \App\Core\Helpers::sanitize($data[2] ?? '');
            $username = $staffNumber;

            try {
                $this->conn->begin_transaction();
                $userStmt = $this->conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Lecturer', 'Active')");
                $userStmt->bind_param("ss", $username, $defaultPassword);
                $userStmt->execute();
                $userID = $this->conn->insert_id;

                $lecStmt = $this->conn->prepare("INSERT INTO lecturer (UserID, StaffNumber, Name, Department, Faculty, Role) VALUES (?, ?, ?, ?, ?, 'Supervisor')");
                $lecStmt->bind_param("issss", $userID, $staffNumber, $name, $department, $faculty);
                $lecStmt->execute();
                
                $this->conn->commit();
                $successCount++;
            } catch (\Exception $e) {
                $this->conn->rollback();
                $errorCount++;
            }
        }
        fclose($handle);
        return ['successCount' => $successCount, 'errorCount' => $errorCount];
    }
}
