<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;
/**
 * Class OpportunityController
 * 
 * Manages all attachment opportunities (job postings).
 * Allows admins and host organizations to create/manage postings, 
 * and allows eligible students to apply for them.
 */
class OpportunityController extends Controller {

    /**
     * Renders the student's view of all active attachment opportunities.
     * Determines if the student is eligible to apply based on their current placement status.
     * 
     * @return void
     */
    public function index() {
        $this->requireActiveStudent();

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


    /**
     * Processes a student's application to a specific opportunity.
     * Handles file uploads for resumes (up to 2MB PDF) or external links.
     * Validates eligibility via AJAX before recording the application.
     * 
     * @return void JSON response
     */
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

        //fetch the Student ID for this session
        $studentId = $_SESSION['student_id'] ?? null;

        //fetch from the models and retreive student data based on their Student ID
        $appModel = $this->model('Application');
        $studentModel = $this->model('Student');
        $student = $studentModel->getById($studentId);

        //if model finds out you are an active student with a placement, denies application
        if ($appModel->hasActiveAttachment($studentId) || ($student['EligibilityStatus'] === 'Cleared')) {
            $this->json(['success' => false, 'message' => 'You already have an active placement or are cleared.']);
            return;
        }

        $opportunityId = filter_input(INPUT_POST, 'opportunity_id', FILTER_SANITIZE_NUMBER_INT);
        $motivation = Helpers::sanitize($_POST['motivation'] ?? '');
        $studentId = $_SESSION['student_id'] ?? null;

        // Handle resume: either uploaded file or external link
        $resumePath = null;
        $resumeLink = null;

        // Check if a file was uploaded
        if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] === UPLOAD_ERR_OK) {
            $allowedMime = 'application/pdf';
            $maxSize = 2 * 1024 * 1024; // 2MB

            if ($_FILES['resume_file']['type'] !== $allowedMime) {
                $this->json(['success' => false, 'message' => 'Only PDF resumes are allowed.']);
                return;
            }
            if ($_FILES['resume_file']['size'] > $maxSize) {
                $this->json(['success' => false, 'message' => 'Resume file exceeds maximum size of 2MB.']);
                return;
            }

            $uploadDir = __DIR__ . '/../../public/uploads/resumes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uniqueName = uniqid('resume_') . '.pdf';
            $destination = $uploadDir . $uniqueName;
            if (!move_uploaded_file($_FILES['resume_file']['tmp_name'], $destination)) {
                $this->json(['success' => false, 'message' => 'Failed to save uploaded resume.']);
                return;
            }
            // Store relative path for DB
            $resumePath = 'uploads/resumes/' . $uniqueName;
        } else {
            // Fallback to resume link if provided
            $resumeLink = filter_input(INPUT_POST, 'resume_link', FILTER_SANITIZE_URL);
        }

        if (!$opportunityId || !$studentId || !$motivation || (empty($resumePath) && empty($resumeLink))) {
            $this->json(['success' => false, 'message' => 'Missing required fields. Provide motivation and a resume (file or link).']);
            return;
        }

        $applicationData = [
            'opportunity_id' => $opportunityId,
            'student_id' => $studentId,
            'motivation' => $motivation,
            'resume_path' => $resumePath,
            'resume_link' => $resumeLink,
            'status_updated_at' => date('Y-m-d H:i:s')
        ];

        $opportunityModel = $this->model('Opportunity');
        $result = $opportunityModel->createApplication($applicationData);

        $this->json($result);
    }

    /**
     * Determines if the current HTTP request is an AJAX/Fetch request.
     * 
     * @return bool
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Renders the administrator's opportunity management dashboard.
     * Shows all opportunities across all host organizations.
     * 
     * @return void
     */
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

    /**
     * Renders the host organization's opportunity management dashboard.
     * Restricts the view to only show opportunities created by the logged-in host.
     * 
     * @return void
     */
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

    /**
     * Processes the creation or updating (saving) of an opportunity.
     * Allows admins to create opportunities on behalf of new or existing hosts.
     * Ensures host organizations can only edit their own opportunities.
     * 
     * @return void
     */
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

            $oppModel = $this->model('Opportunity');

            if ($_SESSION['user_type'] === 'admin') {
                if (!empty($_POST['organization_name'])) {
                    $data['create_new_org'] = true;
                    $data['organization_name'] = Helpers::sanitize($_POST['organization_name']);
                } else {
                    $data['host_org_id'] = $_POST['host_org_id'];
                }
            } else {
                // Host org user: pull ID from session
                $hostOrgId = $_SESSION['host_org_id'] ?? null;
                if (!$hostOrgId) {
                    header("Location: " . Helpers::baseUrl('/host/opportunities?error=' . urlencode('Session error: Host Organization ID missing. Please log out and log back in.')));
                    exit();
                }
                $data['host_org_id'] = $hostOrgId;

                // Ownership verification
                if (isset($data['opportunity_id']) && !empty($data['opportunity_id'])) {
                    $opp = $oppModel->findById($data['opportunity_id']);
                    if (!$opp || (int)$opp['HostOrgID'] !== (int)$hostOrgId) {
                        header("Location: " . Helpers::baseUrl('/host/opportunities?error=' . urlencode('Unauthorized: You do not own this posting.')));
                        exit();
                    }
                }
            }

            $result = $oppModel->save($data);
            
            $redirect = ($_SESSION['user_type'] === 'admin') ? '/admin/opportunities' : '/host/opportunities';
            $param = $result['success'] ? 'success=Saved successfully' : 'error=' . urlencode($result['message']);
            
            header("Location: " . Helpers::baseUrl($redirect . '?' . $param));
            exit();
        }
    }

    /**
     * Deletes a specific opportunity posting.
     * Enforces ownership authorization before deletion.
     * 
     * @return void
     */
    public function delete() {
        // Auth check (Admin or Host)
        if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['admin', 'host_org'])) {
            header("Location: " . Helpers::baseUrl('/'));
            exit();
        }
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        $oppModel = $this->model('Opportunity');

        // Ownership verification for host organizations
        if ($_SESSION['user_type'] === 'host_org') {
            $opp = $oppModel->findById($id);
            if (!$opp || (int)$opp['HostOrgID'] !== (int)$_SESSION['host_org_id']) {
                header("Location: " . Helpers::baseUrl('/host/opportunities?error=' . urlencode('Unauthorized: You do not own this posting.')));
                exit();
            }
        }

        $result = $oppModel->delete($id);
        // Redirect logic similar to save...
        $redirect = ($_SESSION['user_type'] === 'admin') ? '/admin/opportunities' : '/host/opportunities';
        $param = $result['success'] ? 'success=Deleted' : 'error=' . urlencode($result['message']);
        header("Location: " . Helpers::baseUrl($redirect . '?' . $param));
        exit();
    }
}
