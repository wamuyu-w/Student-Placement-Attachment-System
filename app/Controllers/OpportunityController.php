<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;
class OpportunityController extends Controller {

    public function index() {
        $this->requireAuth('student');

        $opportunityModel = $this->model('Opportunity');
        $appModel = $this->model('Application');
        $studentModel = $this->model('Student');
        $studentId = $_SESSION['student_id'];

        $student = $studentModel->getById($studentId);
        $hasActivePlacement = $appModel->hasActiveAttachment($studentId) || (!empty($student) && $student['EligibilityStatus'] === 'Cleared');
        
        $data = [
            'opportunities' => $opportunityModel->getAllActive(),
            'hasActivePlacement' => $hasActivePlacement,
            'title' => 'Available Opportunities',
            'page' => 'opportunities',
            'page_css' => 'student-dashboard.css'
        ];

        $this->view('student/opportunities', $data);
    }

    public function apply() {
        $this->requireActiveStudent();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isAjax()) {
            $this->json(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        // CSRF check — return JSON error instead of dying to preserve AJAX contract
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token.']);
            return;
        }

        $studentId = $_SESSION['student_id'] ?? null;
        $appModel = $this->model('Application');
        $studentModel = $this->model('Student');
        $student = $studentModel->getById($studentId);

        if ($appModel->hasActiveAttachment($studentId) || ($student['EligibilityStatus'] === 'Cleared')) {
            $this->json(['success' => false, 'message' => 'You already have an active placement or are cleared.']);
            return;
        }

        $opportunityId = filter_input(INPUT_POST, 'opportunity_id', FILTER_SANITIZE_NUMBER_INT);
        $motivation = Helpers::sanitize($_POST['motivation'] ?? '');
        $resumeLink = filter_input(INPUT_POST, 'resume_link', FILTER_SANITIZE_URL);
        $studentId = $_SESSION['student_id'] ?? null;

        if (!$opportunityId || !$studentId || !$motivation) {
            $this->json(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }
        
        $hasFile = isset($_FILES['resume']) && $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE;
        if (!$hasFile && empty($resumeLink)) {
            $this->json(['success' => false, 'message' => 'Please upload a resume or provide a link.']);
            return;
        }

        $applicationData = [
            'opportunity_id' => $opportunityId,
            'student_id' => $studentId,
            'motivation' => $motivation,
            'resume_link' => $resumeLink,
            'resume_file' => $_FILES['resume'] ?? null
        ];

        $opportunityModel = $this->model('Opportunity');
        $result = $opportunityModel->createApplication($applicationData);

        $this->json($result);
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function adminManage() {
        $this->requireAuth('admin');
        $oppModel = $this->model('Opportunity');
        $data = [
            'opportunities' => $oppModel->getAll(),
            'hostOrganizations' => $oppModel->getHostOrganizations(),
            'title' => 'Manage Opportunities',
            'page' => 'opportunities',
            'page_css' => ['admin-dashboard.css', 'opportunities.css']
        ];
        $this->view('admin/opportunities', $data);
    }

    public function hostManage() {
        $this->requireAuth('host_org');
        $oppModel = $this->model('Opportunity');
        $data = [
            'opportunities' => $oppModel->getByHost($_SESSION['host_org_id']),
            'title' => 'My Opportunities',
            'page' => 'opportunities',
            'page_css' => ['host-org-dashboard.css', 'opportunities.css']
        ];
        $this->view('host/opportunities', $data);
    }

    public function save() {
        // Auth check (Admin or Host)
        if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'host_org'])) {
            header("Location: " . Helpers::baseUrl('/'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $data = [
                'opportunity_id' => $_POST['opportunity_id'] ?? null,
                'description' => Helpers::sanitize($_POST['description']),
                'eligibility_criteria' => Helpers::sanitize($_POST['eligibility_criteria']),
                'start_date' => $_POST['application_start_date'],
                'end_date' => $_POST['application_end_date'],
                'status' => $_POST['status'] ?? 'Active'
            ];

            if ($_SESSION['user_type'] === 'admin') {
                if (!empty($_POST['organization_name'])) {
                    $data['create_new_org'] = true;
                    $data['organization_name'] = Helpers::sanitize($_POST['organization_name']);
                } else {
                    $data['host_org_id'] = $_POST['host_org_id'];
                }
            } else {
                $data['host_org_id'] = $_SESSION['host_org_id'];
            }

            $oppModel = $this->model('Opportunity');
            $result = $oppModel->save($data);
            
            $redirect = ($_SESSION['user_type'] === 'admin') ? '/admin/opportunities' : '/host/opportunities';
            $param = $result['success'] ? 'success=Saved successfully' : 'error=' . urlencode($result['message']);
            
            header("Location: " . Helpers::baseUrl($redirect . '?' . $param));
        }
    }

    public function delete() {
        // Similar auth check...
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        $oppModel = $this->model('Opportunity');
        $result = $oppModel->delete($id);
        // Redirect logic similar to save...
        $redirect = ($_SESSION['user_type'] === 'admin') ? '/admin/opportunities' : '/host/opportunities';
        $param = $result['success'] ? 'success=Deleted' : 'error=' . urlencode($result['message']);
        header("Location: " . Helpers::baseUrl($redirect . '?' . $param));
    }
}
