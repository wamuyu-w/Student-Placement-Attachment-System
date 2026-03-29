<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Helpers;

class BulkSupervisionController extends Controller {
    
    public function index() {
        $this->requireAuth('admin');
        
        $supervisorModel = $this->model('Supervisor');
        
        $data = [
            'students' => $this->getStudentsNeedingSupervision(),
            'lecturers' => $supervisorModel->getAssignableLecturers(),
            'title' => 'Bulk Supervision Assignment',
            'page' => 'bulk-supervision',
            'page_css' => 'admin-dashboard.css'
        ];
        
        $this->view('admin/supervision/bulk-assign', $data);
    }
    
    /**
     * Fetches students who have an ongoing attachment and need a supervisor,
     * along with their previous supervisor history to prevent duplicates.
     */
    private function getStudentsNeedingSupervision() {
        $db = (new \App\Config\Database())->connect();
        
        $sql = "
            SELECT 
                a.AttachmentID, 
                s.StudentID,
                s.FirstName, 
                s.LastName, 
                u.Username as AdmNumber,
                ho.OrganizationName,
                (SELECT COUNT(*) FROM supervision WHERE AttachmentID = a.AttachmentID) as SupCount,
                (SELECT COUNT(*) FROM assessment WHERE AttachmentID = a.AttachmentID) as AssessCount,
                (SELECT GROUP_CONCAT(sup_old.LecturerID) 
                 FROM supervision sup_old 
                 JOIN attachment a_old ON sup_old.AttachmentID = a_old.AttachmentID 
                 WHERE a_old.StudentID = s.StudentID) as PastSupervisors
            FROM attachment a
            JOIN student s ON a.StudentID = s.StudentID
            JOIN users u ON s.UserID = u.UserID
            JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
            WHERE a.AttachmentStatus = 'Ongoing'
            GROUP BY a.AttachmentID
            HAVING SupCount = 0 OR (SupCount = 1 AND AssessCount >= 1)
        ";
        
        $result = $db->query($sql);
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $row['PastSupervisors'] = $row['PastSupervisors'] ? explode(',', $row['PastSupervisors']) : [];
            $students[] = $row;
        }
        return $students;
    }
    
    public function processAssignment() {
        $this->requireAuth('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/admin/supervision/bulk'));
            exit();
        }
        $this->verifyCsrf();
        
        $studentAttachmentIds = $_POST['student_attachments'] ?? [];
        $lecturerIds = $_POST['lecturer_ids'] ?? [];
        
        if (empty($studentAttachmentIds) || empty($lecturerIds)) {
            header("Location: " . Helpers::baseUrl('/admin/supervision/bulk?error=Please select both students and lecturers'));
            exit();
        }
        
        $supervisorModel = $this->model('Supervisor');
        $successCount = 0;
        $errorCount = 0;
        
        // Shuffle lecturers for random assignment
        shuffle($lecturerIds);
        $lecCount = count($lecturerIds);
        
        foreach ($studentAttachmentIds as $index => $attachmentId) {
            // Pick a lecturer (round-robin for even distribution)
            // But we must respect the "unique supervisor" rule
            $assigned = false;
            $attempts = 0;
            
            while (!$assigned && $attempts < $lecCount) {
                $lecturerId = $lecturerIds[($index + $attempts) % $lecCount];
                
                // Check if this lecturer has supervised this student before
                if (!$this->hasSupervisedBefore($attachmentId, $lecturerId)) {
                    $result = $supervisorModel->assign($attachmentId, $lecturerId);
                    if ($result['success']) {
                        $successCount++;
                        $assigned = true;

                        // Fetch details and send email
                        $db = (new \App\Config\Database())->connect();
                        // Student Details
                        $stmt = $db->prepare("SELECT s.Email, s.FirstName, s.LastName FROM student s JOIN attachment a ON s.StudentID = a.StudentID WHERE a.AttachmentID = ?");
                        $stmt->bind_param("i", $attachmentId);
                        $stmt->execute();
                        $studentInfo = $stmt->get_result()->fetch_assoc();
                        
                        // Lecturer Details
                        $stmtL = $db->prepare("SELECT u.Username, l.Name FROM lecturer l JOIN users u ON l.UserID = u.UserID WHERE l.LecturerID = ?");
                        $stmtL->bind_param("i", $lecturerId);
                        $stmtL->execute();
                        $lecInfo = $stmtL->get_result()->fetch_assoc();
                        
                        if ($studentInfo && $lecInfo && !empty($studentInfo['Email'])) {
                            \App\Core\Mailer::notifySupervisorAssigned(
                                $studentInfo['Email'],
                                trim($studentInfo['FirstName'] . ' ' . $studentInfo['LastName']),
                                $lecInfo['Name'],
                                $lecInfo['Username'] . '@example.com' // Placeholder email
                            );
                        }
                    }
                }
                $attempts++;
            }
            
            if (!$assigned) {
                $errorCount++;
            }
        }
        
        $msg = "Successfully assigned $successCount students.";
        if ($errorCount > 0) {
            $msg .= " Could not assign $errorCount students due to supervisor conflicts.";
        }
        
        header("Location: " . Helpers::baseUrl('/admin/supervision/bulk?success=' . urlencode($msg)));
        exit();
    }
    
    private function hasSupervisedBefore($attachmentId, $lecturerId) {
        $db = (new \App\Config\Database())->connect();
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM supervision sup
            JOIN attachment a ON sup.AttachmentID = a.AttachmentID
            WHERE a.StudentID = (SELECT StudentID FROM attachment WHERE AttachmentID = ?)
            AND sup.LecturerID = ?
        ");
        $stmt->bind_param("ii", $attachmentId, $lecturerId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_row();
        return $res[0] > 0;
    }
}
