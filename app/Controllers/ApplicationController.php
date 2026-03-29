<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;
use App\Core\Mailer;

class ApplicationController extends Controller {

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
        // the admin will be required in this session
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $appId  = $_POST['application_id'];
            $status = $_POST['status'];
            $rejectionReason = Helpers::sanitize($_POST['rejection_reason'] ?? '');
            $financialClearance = $_POST['financial_clearance'] ?? 'Cleared';

            $appModel = $this->model('Application');

            if ($status === 'Approved') {
                $result = $appModel->approveAndCreateAttachment($appId, $financialClearance);

                if ($result['success']) {
                    $studentModel = $this->model('Student');
                    $student = $studentModel->getById($result['student_id']);
                    
                    // Fetch Host Org Details
                    $db = (new \App\Config\Database())->connect();
                    $stmtHost = $db->prepare("SELECT OrganizationName, Email FROM hostorganization WHERE HostOrgID = ?");
                    $stmtHost->bind_param("i", $result['host_org_id']);
                    $stmtHost->execute();
                    $host = $stmtHost->get_result()->fetch_assoc();

                    // Get Dates from Application or fallback
                    $appModel = $this->model('Application');
                    $appData = $appModel->getApplicationById($appId);
                    $startDate = $appData['StartDate'] ?: date('Y-m-d');
                    $endDate   = $appData['EndDate']   ?: date('Y-m-d', strtotime($startDate . ' +84 days'));

                    $orgName = $host ? $host['OrganizationName'] : ($_POST['org_name'] ?? 'your host organization');

                    // 1. Notify Student
                    if ($student && !empty($student['Email'])) {
                        Mailer::notifyStudentApproved($student['Email'], $student['FirstName'] . ' ' . $student['LastName'], $orgName);
                        error_log("Student Approval Email Sent to " . $student['Email']);
                    } else {
                        error_log("Failed to send Student Approval Email: Student object missing or Email empty. Student ID: " . $result['student_id']);
                    }

                    // 2. Notify Host Organization
                    if ($host && !empty($host['Email']) && $student) {
                        Mailer::notifyHostPlacement(
                            $host['Email'], 
                            $host['OrganizationName'], 
                            $student['FirstName'] . ' ' . $student['LastName'], 
                            $startDate, 
                            $endDate
                        );
                    }

                    header("Location: " . Helpers::baseUrl('/admin/applications?success=Application+approved+and+attachment+created'));
                } else {
                    header("Location: " . Helpers::baseUrl('/admin/applications?error=' . urlencode($result['message'])));
                }
            } else {
                $appModel->updateProgramStatus($appId, $status, $rejectionReason);

                $studentModel = $this->model('Student');
                $appData = $appModel->getApplicationById($appId);
                if ($appData) {
                    $student = $studentModel->getById($appData['StudentID']);
                    if ($student && !empty($student['Email'])) {
                        $reason = $rejectionReason ?: 'No specific reason provided.';
                        Mailer::notifyStudentRejected($student['Email'], $student['FirstName'] . ' ' . $student['LastName'], $reason);
                    }
                }
                header("Location: " . Helpers::baseUrl('/admin/applications?success=Status+updated'));
            }
        }
    }

    public function updateJobStatusAdmin() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $oppId = $_POST['opportunity_id'];
            $studentId = $_POST['student_id'];
            $status = $_POST['status'];
            
            $appModel = $this->model('Application');
            $appModel->updateJobStatus($oppId, $studentId, $status);
            
            header("Location: " . Helpers::baseUrl('/admin/applications'));
        }
    }

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
        
        if (!empty($student) && ($hasActiveAttachment || $student['EligibilityStatus'] === 'Cleared')) {
            header("Location: " . Helpers::baseUrl('/student/applications?error=You already have an active placement or are cleared.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            // Validate financial clearance is declared
            $allowedStatuses = ['Cleared', 'Pending', 'Not Cleared'];
            $financialStatus = $_POST['financial_clearance_status'] ?? '';
            if (!in_array($financialStatus, $allowedStatuses)) {
                header("Location: " . Helpers::baseUrl('/student/applications?error=Please+declare+your+financial+clearance+status.'));
                exit();
            }

            // Validate attachment duration: 45 days min, 90 days max
            $startDate = $_POST['start_date'] ?? '';
            $endDate   = $_POST['end_date']   ?? '';
            if ($startDate && $endDate) {
                $diff = (new \DateTime($startDate))->diff(new \DateTime($endDate))->days;
                if ($diff < 45) {
                    header("Location: " . Helpers::baseUrl('/student/applications?error=Attachment+period+must+be+at+least+1.5+months+(45+days).'));
                    exit();
                }
                if ($diff > 90) {
                    header("Location: " . Helpers::baseUrl('/student/applications?error=Attachment+period+cannot+exceed+3+months+(90+days).'));
                    exit();
                }
            }

            $result = $appModel->createSessionApplication($_SESSION['student_id'], $_POST);
            
            $param = $result['success'] ? 'success=Application submitted successfully' : 'error=db_error&message=' . urlencode($result['message']);
            header("Location: " . Helpers::baseUrl('/student/applications?' . $param));
        }
    }

    public function registerPlacement() {
        $this->requireActiveStudent();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $appModel = $this->model('Application');
            $result = $appModel->registerPlacement($_SESSION['student_id'], $_POST);
            
            $param = $result['success'] ? 'success=registered' : 'error=' . $result['message'];
            header("Location: " . Helpers::baseUrl('/student/applications?' . $param));
        }
    }
}
