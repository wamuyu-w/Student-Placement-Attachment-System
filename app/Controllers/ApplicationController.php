<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;
// Controller for managing applications (job and program) for students, hosts, and admins
class ApplicationController extends Controller {

    // --- Admin ---
    public function adminIndex() {
        $this->requireAuth('admin');
        $appModel = $this->model('Application');
        
        $data = [
            'jobApplications' => $appModel->getAllJobApplications(),
            'programApplications' => $appModel->getAllProgramApplications(),
            'title' => 'Manage Applications',
            'page' => 'applications',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('admin/applications', $data);
    }

    public function updateProgramStatus() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appId = $_POST['application_id'];
            $status = $_POST['status'];
            
            $appModel = $this->model('Application');
            $appModel->updateProgramStatus($appId, $status);
            
            header("Location: " . Helpers::baseUrl('/admin/applications'));
        }
    }

    public function updateJobStatusAdmin() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oppId = $_POST['opportunity_id'];
            $studentId = $_POST['student_id'];
            $status = $_POST['status'];
            
            $appModel = $this->model('Application');
            $appModel->updateJobStatus($oppId, $studentId, $status);
            
            header("Location: " . Helpers::baseUrl('/admin/applications'));
        }
    }

    // --- Host ---
    public function hostIndex() {
        $this->requireAuth('host_org');
        $appModel = $this->model('Application');
        
        $data = [
            'applications' => $appModel->getHostApplications($_SESSION['host_org_id']),
            'title' => 'Applications',
            'page' => 'applications',
            'page_css' => 'host-org-dashboard.css'
        ];
        $this->view('host/applications', $data);
    }

    public function updateJobStatusHost() {
        $this->requireAuth('host_org');
        
        $oppId = $_POST['opportunity_id'] ?? null;
        $studentId = $_POST['student_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$oppId || !$studentId || !$status) {
            $this->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        $appModel = $this->model('Application');
        // Verify ownership
        $app = $appModel->verifyAndGetApplication($oppId, $studentId, $_SESSION['host_org_id']);
        
        if (!$app) {
            $this->json(['success' => false, 'message' => 'Application not found']);
        }

        if ($appModel->updateJobStatus($oppId, $studentId, $status)) {
            $this->json(['success' => true, 'message' => "Application marked as $status"]);
        } else {
            $this->json(['success' => false, 'message' => 'Database error']);
        }
    }

    // --- Student ---
    public function studentIndex() {
        $this->requireAuth('student');
        $appModel = $this->model('Application');
        $studentId = $_SESSION['student_id'];

        $applications = $appModel->getStudentApplications($studentId);
        $hasPendingOrApproved = $appModel->hasPendingOrApprovedApp($studentId);
        $hasActiveAttachment = $appModel->hasActiveAttachment($studentId);
        
        $studentModel = $this->model('Student');
        $student = $studentModel->getById($studentId);
        $hasActivePlacement = $hasActiveAttachment || ($student['EligibilityStatus'] === 'Cleared');
        
        // Check for approved app to show registration form
        $hasApproved = false;
        foreach ($applications as $app) {
            if ($app['ApplicationStatus'] === 'Approved') {
                $hasApproved = true;
                break;
            }
        }
        // Reset pointer for view
        $applications->data_seek(0);

        $data = [
            'applications' => $applications,
            'hasPendingOrApproved' => $hasPendingOrApproved,
            'hasActiveAttachment' => $hasActiveAttachment,
            'hasActivePlacement' => $hasActivePlacement,
            'hasApproved' => $hasApproved,
            'hosts' => $appModel->getAllHosts(),
            'title' => 'My Applications',
            'page' => 'applications',
            'page_css' => 'student-dashboard.css'
        ];
        $this->view('student/applications', $data);
    }

    public function applySession() {
        $this->requireActiveStudent();
        
        $studentId = $_SESSION['student_id'];
        $appModel = $this->model('Application');
        
        // Block if already has active placement or is cleared
        $hasActiveAttachment = $appModel->hasActiveAttachment($studentId);
        $studentModel = $this->model('Student');
        $student = $studentModel->getById($studentId);
        
        if ($hasActiveAttachment || ($student['EligibilityStatus'] === 'Cleared')) {
            header("Location: " . Helpers::baseUrl('/student/applications?error=You already have an active placement or are cleared.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $appModel->createSessionApplication($_SESSION['student_id'], $_POST);
            
            $param = $result['success'] ? 'success=applied' : 'error=db_error&message=' . urlencode($result['message']);
            header("Location: " . Helpers::baseUrl('/student/applications?' . $param));
        }
    }

    public function registerPlacement() {
        $this->requireActiveStudent();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appModel = $this->model('Application');
            $result = $appModel->registerPlacement($_SESSION['student_id'], $_POST);
            
            $param = $result['success'] ? 'success=registered' : 'error=' . $result['message'];
            header("Location: " . Helpers::baseUrl('/student/applications?' . $param));
        }
    }
}
