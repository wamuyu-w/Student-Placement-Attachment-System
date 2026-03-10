<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;
// The LogbookController manages logbook entries for students, allowing them to create and view their entries, while staff and host organizations can review and manage these entries. 
//It also includes functionality for printing logbooks in a user-friendly format.
class LogbookController extends Controller {

    public function studentIndex() {
        $this->requireAuth('student');
        $logbookModel = $this->model('Logbook');
        
        $entries = $logbookModel->getEntriesByStudent($_SESSION['student_id']);
        
        $data = [
            'entries' => $entries,
            'hasAttachment' => ($entries !== null),
            'title' => 'My Logbook',
            'page' => 'logbook',
            'page_css' => ['student-dashboard.css', 'logbook.css']
        ];
        $this->view('student/logbook', $data);
    }

    public function createEntry() {
        $this->requireActiveStudent();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    public function reviewEntry() {
        // Shared for staff and host
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $userType = $_SESSION['user_type'] ?? null;
        if (!in_array($userType, ['staff', 'host_org'])) {
             header("Location: " . Helpers::baseUrl('/'));
             exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logbookId = $_POST['logbook_id'];
            $comment = Helpers::sanitize($_POST['comment'] ?? '');
            $status = $_POST['status'] ?? 'Pending'; // e.g., 'Approved'

            $logbookModel = $this->model('Logbook');
            $logbookModel->reviewEntry($logbookId, $status, $comment, $userType);
            
            $redirect = ($userType === 'staff') ? '/staff/logbook' : '/host/logbook';
            header("Location: " . Helpers::baseUrl($redirect . '?success=Review submitted successfully'));
        }
    }
    // the function printLogbook() allows authorized users (students, staff, or host organizations) to view a printable version of a student's logbook entries 
    //by retrieving the relevant data and rendering it in a print-friendly format without the standard layout
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
