<?php
namespace App\Models;
use App\Config\Database;
class Logbook {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
        $this->ensureTablesExist();
    }

    //there should be a function to ensure that the logbook is opened only to students whose attachment is active
    
    private function ensureTablesExist() {
        $check = $this->conn->query("SHOW TABLES LIKE 'logbook'");
        if ($check->num_rows == 0) {
            $sql = "CREATE TABLE logbook (
                LogbookID INT AUTO_INCREMENT PRIMARY KEY,
                AttachmentID INT NOT NULL,
                WeekNumber INT NOT NULL,
                StartDate DATE NOT NULL,
                EndDate DATE NOT NULL,
                Activities TEXT NOT NULL,
                Status VARCHAR(20) DEFAULT 'Pending',
                EntryDate DATETIME DEFAULT CURRENT_TIMESTAMP,
                AcademicSupervisorComments TEXT,
                HostSupervisorComments TEXT,
                UNIQUE KEY unique_week (AttachmentID, WeekNumber)
            )";
            $this->conn->query($sql);
        } else {
            // Table exists, check if it has the required columns (Migration logic)
            $colCheck = $this->conn->query("SHOW COLUMNS FROM logbook LIKE 'WeekNumber'");
            if ($colCheck->num_rows == 0) {
                // Missing columns detected. Alter table to match model expectations.
                
                // 1. Drop existing unique index on AttachmentID if it exists (to allow multiple weeks)
                // We attempt this silently as the index name might vary
                try {
                    $this->conn->query("ALTER TABLE logbook DROP INDEX AttachmentID");
                } catch (\Throwable $e) {}

                // 2. Add missing columns and new unique constraint
                $sql = "ALTER TABLE logbook 
                        ADD COLUMN WeekNumber INT NOT NULL DEFAULT 1,
                        ADD COLUMN StartDate DATE NULL,
                        ADD COLUMN EndDate DATE NULL,
                        ADD COLUMN Activities TEXT NULL,
                        ADD COLUMN EntryDate DATETIME DEFAULT CURRENT_TIMESTAMP,
                        ADD COLUMN AcademicSupervisorComments TEXT NULL,
                        ADD COLUMN HostSupervisorComments TEXT NULL,
                        ADD UNIQUE KEY unique_week (AttachmentID, WeekNumber)";
                $this->conn->query($sql);
            }
        }
    }

    // Student / Print: Get entries (returns array for compatibility with print view and foreach loops)
    public function getEntriesByStudent($studentId) {
        // Ensure SubmittedAt column exists for accurate date tracking
        $this->conn->query("ALTER TABLE logbook ADD COLUMN IF NOT EXISTS SubmittedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

        // Get most recent attachment (Ongoing or Completed) for student
        $stmt = $this->conn->prepare(
            "SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus IN ('Ongoing','Completed') ORDER BY StartDate DESC LIMIT 1"
        );
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) return []; // No attachment at all
        
        $attachmentId = $res->fetch_assoc()['AttachmentID'];

        $stmt = $this->conn->prepare(
            "SELECT LogbookID, WeekNumber, StartDate, EndDate, Activities as Description, Status,
                    SubmittedAt as EntryDate, AcademicSupervisorComments, HostSupervisorComments
             FROM logbook WHERE AttachmentID = ? ORDER BY WeekNumber ASC"
        );
        $stmt->bind_param("i", $attachmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Student: Create entry
    public function createEntry($studentId, $data) {
        // Get AttachmentID
        $stmt = $this->conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows === 0) {
            return ['success' => false, 'message' => 'No active attachment found.'];
        }
        $attachmentId = $res->fetch_assoc()['AttachmentID'];

        // Check if week already exists
        $check = $this->conn->prepare("SELECT LogbookID FROM logbook WHERE AttachmentID = ? AND WeekNumber = ?");
        $check->bind_param("ii", $attachmentId, $data['week_number']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Entry for this week already exists.'];
        }

        // Enforce 12-week cap
        if ((int)$data['week_number'] > 12) {
            return ['success' => false, 'message' => 'Logbook is closed. Attachment duration is capped at 12 weeks.'];
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO logbook (AttachmentID, WeekNumber, StartDate, EndDate, Activities, Status, SubmittedAt) 
             VALUES (?, ?, ?, ?, ?, 'Pending', NOW())"
        );
        $stmt->bind_param("iisss", $attachmentId, $data['week_number'], $data['start_date'], $data['end_date'], $data['description']);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => $this->conn->error];
    }

    // Staff: Get pending logbooks
    public function getPendingLogbooksForStaff($staffId) {
        $sql = "SELECT l.LogbookID, l.WeekNumber, l.StartDate, l.EndDate, l.Activities as Description, l.Status, s.FirstName, s.LastName, s.StudentID
                FROM logbook l
                JOIN attachment a ON l.AttachmentID = a.AttachmentID
                JOIN student s ON a.StudentID = s.StudentID
                JOIN supervision sup ON a.AttachmentID = sup.AttachmentID
                WHERE sup.LecturerID = ? AND l.Status = 'Pending'
                ORDER BY l.EntryDate ASC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Database Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $staffId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Host: Get pending logbooks
    public function getPendingLogbooksForHost($hostId) {
        $sql = "SELECT l.LogbookID, l.WeekNumber, l.StartDate, l.EndDate, l.Activities as Description, l.Status, s.FirstName, s.LastName, s.StudentID
                FROM logbook l
                JOIN attachment a ON l.AttachmentID = a.AttachmentID
                JOIN student s ON a.StudentID = s.StudentID
                WHERE a.HostOrgID = ? AND l.Status = 'Pending'
                ORDER BY l.EntryDate ASC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Database Error: " . $this->conn->error);
        }
        $stmt->bind_param("i", $hostId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Shared: Approve entry
    public function reviewEntry($logbookId, $status, $comment, $userType) {
        $sql = "UPDATE logbook SET Status = ?";
        $params = [];
        $params[] = &$status;
        $types = "s";

        if (!empty($comment)) {
            if ($userType === 'staff') {
                $sql .= ", AcademicSupervisorComments = ?";
            } elseif ($userType === 'host_org') {
                $sql .= ", HostSupervisorComments = ?";
            }
            $params[] = &$comment;
            $types .= "s";
        }

        $sql .= " WHERE LogbookID = ?";
        $params[] = &$logbookId;
        $types .= "i";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        array_unshift($params, $types);
        call_user_func_array([$stmt, 'bind_param'], $params);
        return $stmt->execute();
    }
}
