<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;

/**
 * Class LogbookController
 * 
 * Manages the creation, viewing, reviewing, and printing of weekly student logbooks.
 * Facilitates interaction between students, academic supervisors, and host organizations.
 */
class LogbookController extends Controller {

    /**
     * Renders the student's logbook dashboard.
     * Fetches all their previous entries and checks if they have an active or completed attachment
     * to determine if they are allowed to submit new entries.
     * 
     * @return void
     */
    public function studentIndex() {
        $this->requireActiveStudent();
        $logbookModel = $this->model('Logbook');
        $appModel = $this->model('Application');
        
        $entries = $logbookModel->getEntriesByStudent($_SESSION['student_id']);
        
        // Include both Ongoing and Completed so students can view their history even after finishing
        $hasAttachment = $appModel->hasActiveAttachment($_SESSION['student_id']);
        if (!$hasAttachment) {
            // Also check for Completed attachment
            $db = (new \App\Config\Database())->connect();
            $stmt = $db->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus IN ('Ongoing','Completed') LIMIT 1");
            $stmt->bind_param("i", $_SESSION['student_id']);
            $stmt->execute();
            $hasAttachment = $stmt->get_result()->num_rows > 0;
        }
        
        $data = [
            'entries' => $entries,
            'hasAttachment' => $hasAttachment,
            'title' => 'My Logbook',
            'page' => 'logbook',
            'page_css' => ['student-dashboard.css', 'logbook.css']
        ];
        $this->view('student/logbook', $data);
    }

    /**
     * Processes a student's submission of a new weekly logbook entry.
     * Parses the daily tasks (Monday-Friday) into a JSON structure and saves it to the database.
     * 
     * @return void
     */
    public function createEntry() {
        $this->requireActiveStudent();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $data = [
                'week_number' => $_POST['week_number'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date']
            ];
            
            // Handle structured daily entries (JSON)
            if (isset($_POST['tasks']) && is_array($_POST['tasks'])) {
                $weeklyData = [];
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                
                foreach ($days as $day) {
                    $weeklyData[$day] = [
                        'task' => Helpers::sanitize($_POST['tasks'][$day] ?? ''),
                        'comment' => Helpers::sanitize($_POST['comments'][$day] ?? '')
                    ];
                }
                $data['description'] = json_encode($weeklyData, JSON_UNESCAPED_UNICODE);
            } else {
                $data['description'] = Helpers::sanitize($_POST['description'] ?? '');
            }
            
            $logbookModel = $this->model('Logbook');
            $result = $logbookModel->createEntry($_SESSION['student_id'], $data);
            
            $param = $result['success'] ? 'success=Entry added successfully' : 'error=' . urlencode($result['message']);
            header("Location: " . Helpers::baseUrl('/student/logbook?' . $param));
        }
    }

    /**
     * Renders the staff (lecturer) view for reviewing pending logbooks.
     * Fetches logbooks submitted by students assigned to the logged-in lecturer.
     * 
     * @return void
     */
    public function staffIndex() {
        $this->requireAuth('staff');
        try {
            $logbookModel = $this->model('Logbook');
            
            $data = [
                'entries' => $logbookModel->getPendingLogbooksForStaff($_SESSION['LecturerID']),
                'title' => 'Review Logbooks',
                'page' => 'logbook',
                'page_css' => 'staff-dashboard.css'
            ];
            $this->view('staff/logbook', $data);
        } catch (\Throwable $e) {
            error_log("Logbook Error: " . $e->getMessage());
            echo "<div style='padding:20px;color:red;'>An error occurred while loading logbooks. Please check the logs.</div>";
        }
    }

    /**
     * Renders the host organization's view for reviewing pending logbooks.
     * Fetches logbooks submitted by students attached to this organization.
     * 
     * @return void
     */
    public function hostIndex() {
        $this->requireAuth('host_org');
        try {
            $logbookModel = $this->model('Logbook');
            
            $data = [
                'entries' => $logbookModel->getPendingLogbooksForHost($_SESSION['host_org_id']),
                'title' => 'Review Logbooks',
                'page' => 'logbook',
                'page_css' => 'host-org-dashboard.css'
            ];
            $this->view('host/logbook', $data);
        } catch (\Throwable $e) {
            error_log("Logbook Error: " . $e->getMessage());
            echo "<div style='padding:20px;color:red;'>An error occurred while loading logbooks. Please check the logs.</div>";
        }
    }

    /**
     * Processes logbook reviews (approvals or rejections) with comments.
     * Shared method utilized by both academic staff and host organizations.
     * 
     * @return void
     */
    public function reviewEntry() {
        // Shared for staff and host
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $userType = $_SESSION['user_type'] ?? null;
        if (!in_array($userType, ['staff', 'host_org'])) {
             header("Location: " . Helpers::baseUrl('/'));
             exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $logbookId = $_POST['logbook_id'];
            $comment = Helpers::sanitize($_POST['comment'] ?? '');
            $status = $_POST['status'] ?? 'Pending'; // e.g., 'Approved'

            $logbookModel = $this->model('Logbook');
            $logbookModel->reviewEntry($logbookId, $status, $comment, $userType);
            
            $redirect = ($userType === 'staff') ? '/staff/logbook' : '/host/logbook';
            header("Location: " . Helpers::baseUrl($redirect . '?success=Review submitted successfully'));
        }
    }
    /**
     * Processes ad-hoc comments added by a Host Organization to a specific logbook entry
     * directly from the student's progress view.
     * 
     * @return void
     */
    public function addComment() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userType = $_SESSION['user_type'] ?? null;
        if ($userType !== 'host_org') {
             header("Location: " . Helpers::baseUrl('/'));
             exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $logbookId = $_POST['logbook_id'] ?? 0;
            $studentId = $_POST['student_id'] ?? 0;
            $comment = Helpers::sanitize($_POST['comment'] ?? '');

            if ($logbookId && $comment) {
                $logbookModel = $this->model('Logbook');
                $logbookModel->updateHostComment($logbookId, $comment);
            }
            
            header("Location: " . Helpers::baseUrl('/host/students/progress?id=' . $studentId . '&success=Comment+added+successfully'));
            exit();
        }
    }

    /**
     * Generates a printable, layout-free view of a student's entire logbook history.
     * Restricts host organizations from printing to maintain student data privacy based on requirements.
     * 
     * @return void
     */
    public function printLogbook() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header("Location: " . Helpers::baseUrl('/')); exit(); }

        $studentId = $_GET['id'] ?? ($_SESSION['user_type'] === 'student' ? $_SESSION['student_id'] : 0);
        
        // Restricted Access Control: No Host Orgs allowed to print full logbooks per user request
        if ($_SESSION['user_type'] === 'host_org' || ($_SESSION['user_type'] === 'student' && $studentId != $_SESSION['student_id'])) {
            die("Unauthorized access. Host organizations cannot print student logbooks.");
        }

        $logbookModel = $this->model('Logbook');
        $studentModel = $this->model('Student');
        
        $data = ['entries' => $logbookModel->getEntriesByStudent($studentId), 'student' => $studentModel->getById($studentId)];
        $this->view('reports/print-logbook', $data, false);
    }
}
