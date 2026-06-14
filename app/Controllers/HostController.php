<?php
namespace App\Controllers;
/**
 * Class HostController
 * 
 * Manages the host organization's dashboard, allowing them to view their dashboard, 
 * see attached students, manage supervision, generate unique codes for student assessments, 
 * and verify final reports. Ensures that only authenticated host organizations can access 
 * these functionalities.
 */
use App\Core\Controller;
use App\Core\Helpers;

class HostController extends Controller {
    
    /**
     * Renders the main dashboard for the host organization.
     * Fetches top-level statistics and recent student placements.
     * 
     * @return void
     */
    public function dashboard() {
        $this->requireAuth('host_org');
        
        $hostOrgId = $_SESSION['host_org_id'];
        $hostModel = $this->model('Host');
        
        $data = [
            'stats' => $hostModel->getDashboardStats($hostOrgId),
            'recentApps' => $hostModel->getRecentPlacements($hostOrgId),
            'title' => 'Host Organization Dashboard',
            'page' => 'dashboard',
            'page_css' => 'host-org-dashboard.css'
        ];
        
        $this->view('host/dashboard', $data);
    }
    /**
     * Renders the attached students view.
     * Fetches a list of all students currently placed within this organization.
     * 
     * @return void
     */
    public function viewStudents() {
        $this->requireAuth('host_org');
        $hostModel = $this->model('Host');
        
        $data = [
            'students' => $hostModel->getAttachedStudents($_SESSION['host_org_id']),
            'title' => 'Attached Students', 
            'page' => 'students', 
            'page_css' => 'host-org-dashboard.css'
        ];
        $this->view('host/students', $data);
    }
    /**
     * Renders the supervision management view.
     * Allows the host organization to oversee students under their supervision
     * and generate verification codes for assessments.
     * 
     * @return void
     */
    public function supervision() {
        $this->requireAuth('host_org');
        $hostModel = $this->model('Host');
        
        $data = [
            'students' => $hostModel->getAttachedStudents($_SESSION['host_org_id']),
            'title' => 'Supervision & Codes', 
            'page' => 'supervision', 
            'page_css' => 'host-org-dashboard.css'
        ];
        $this->view('host/supervision', $data);
    }
    /**
     * Processes the generation of a unique 6-digit assessment verification code.
     * The code is required by academic supervisors before they can conduct an assessment.
     * 
     * @return void
     */
    public function generateCode() {
        $this->requireAuth('host_org');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $attachmentId = $_POST['attachment_id'];
            $hostModel = $this->model('Host');
            
            $result = $hostModel->generateAssessmentCode($attachmentId, $_SESSION['host_org_id']);
            $param = $result ? 'success=Code generated successfully' : 'error=Failed to generate code';
            
            header("Location: " . Helpers::baseUrl('/host/supervision?' . $param));
        }
    }

    /**
     * Renders the detailed progress view for a specific student attached to the organization.
     * Displays their logbooks, assessment history, and final report status.
     * Strictly enforces that the student belongs to the logged-in host organization.
     * 
     * @return void
     */
    public function viewStudentProgress() {
        $this->requireAuth('host_org');
        $studentId = $_GET['id'] ?? null;
        
        if (!$studentId) {
            header("Location: " . Helpers::baseUrl('/host/students'));
            exit();
        }

        $hostModel = $this->model('Host');
        
        // Verify this student is actually attached to this host organization
        $isAttached = false;
        $attachedStudents = $hostModel->getAttachedStudents($_SESSION['host_org_id']);
        if ($attachedStudents) {
            while ($row = $attachedStudents->fetch_assoc()) {
                if ($row['StudentID'] == $studentId) {
                    $isAttached = true;
                    break;
                }
            }
        }

        if (!$isAttached) {
            die("Unauthorized access. This student is not attached to your organization.");
        }

        $studentModel = $this->model('Student');
        $reportModel = $this->model('Report');
        $assessmentModel = $this->model('Assessment');
        $logbookModel = $this->model('Logbook');

        // Get basic student info
        $student = $studentModel->getById($studentId);
        
        // Get attachment & report status
        $progressArray = $reportModel->getStudentProgress($studentId);
        $progress = !empty($progressArray) ? $progressArray[0] : null;

        $hostOrg = $progress['OrganizationName'] ?? 'Not Assigned';
        $supervisor = 'Not Assigned';
        if ($progress && !empty($progress['AttachmentID'])) {
            $db = (new \App\Config\Database())->connect();
            $stmt = $db->prepare("SELECT l.Name FROM supervision s JOIN lecturer l ON s.LecturerID = l.LecturerID WHERE s.AttachmentID = ?");
            $stmt->bind_param("i", $progress['AttachmentID']);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $supervisor = $row['Name'];
            }
        }

        $data = [
            'student' => $student,
            'progress' => $progress,
            'hostOrgName' => $hostOrg,
            'supervisorName' => $supervisor,
            'assessments' => $assessmentModel->getStudentAssessments($studentId),
            'logbookEntries' => $logbookModel->getEntriesByStudent($studentId),
            'title' => 'Student Progress',
            'page' => 'students',
            'page_css' => 'host-org-dashboard.css'
        ];
        
        $this->view('host/student-progress', $data);
    }

    /**
     * Processes the host organization's verification (approval or rejection) of a student's Final Report.
     * Verifies ownership of the attachment before updating the report status.
     * 
     * @return void
     */
    public function verifyReport() {
        $this->requireAuth('host_org');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            
            $attachmentId = $_POST['attachment_id'] ?? null;
            $studentId = $_POST['student_id'] ?? null;
            $action = $_POST['action'] ?? null; // 'Approve' or 'Reject'
            
            if (!$attachmentId || !$studentId || !in_array($action, ['Approve', 'Reject'])) {
                header("Location: " . Helpers::baseUrl("/host/students/progress?id={$studentId}&error=Invalid+request"));
                exit();
            }

            // Verify this host owns the attachment
            $hostModel = $this->model('Host');
            $attachedStudents = $hostModel->getAttachedStudents($_SESSION['host_org_id']);
            $isAttached = false;
            if ($attachedStudents) {
                while ($row = $attachedStudents->fetch_assoc()) {
                    if ($row['StudentID'] == $studentId) {
                        $isAttached = true;
                        break;
                    }
                }
            }

            if (!$isAttached) {
                die("Unauthorized access. This student is not attached to your organization.");
            }

            $reportModel = $this->model('Report');
            $status = ($action === 'Approve') ? 'Approved' : 'Rejected';
            
            if ($reportModel->updateFinalReportStatus($attachmentId, $status)) {
                header("Location: " . Helpers::baseUrl("/host/students/progress?id={$studentId}&success=Report+status+updated+to+{$status}"));
            } else {
                header("Location: " . Helpers::baseUrl("/host/students/progress?id={$studentId}&error=Failed+to+update+report+status"));
            }
            exit();
        }
    }
}
