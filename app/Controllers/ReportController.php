<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;

class ReportController extends Controller {

    public function adminIndex() {
        $this->requireAuth('admin');
        try {
            $reportModel = $this->model('Report');
            $data = [
                'placementStats' => $reportModel->getPlacementStats(),
                'hostStats' => $reportModel->getHostStats(),
                'systemStats' => $reportModel->getSystemStats(),
                'title' => 'System Reports Center',
                'page' => 'reports',
                'page_css' => 'admin-dashboard.css'
            ];
            $this->view('admin/reports', $data);
        } catch (\Throwable $e) {
            error_log("Report Error: " . $e->getMessage());
            echo "<div style='padding:20px;color:red;'>An error occurred. check logs.</div>";
        }
    }

    public function assessmentSchedule() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = [
            'schedule' => $reportModel->getAssessmentSchedule(),
            'title' => 'Assessment Schedule Report',
            'page' => 'reports',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('reports/assessment-schedule', $data);
    }

    public function supervisorStats() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = [
            'stats' => $reportModel->getSupervisorStats(),
            'title' => 'Supervisor Statistics',
            'page' => 'reports',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('reports/supervisor-stats', $data);
    }

    public function staffIndex() {
        $this->requireAuth('staff');
        try {
            $reportModel = $this->model('Report');
            $data = [
                'students' => $reportModel->getSupervisedStats($_SESSION['LecturerID']),
                'title' => 'Supervision Reports',
                'page' => 'reports',
                'page_css' => 'staff-dashboard.css'
            ];
            $this->view('staff/reports', $data);
        } catch (\Throwable $e) {
            error_log("Report Error: " . $e->getMessage());
            echo "<div style='padding:20px;color:red;'>An error occurred.</div>";
        }
    }

    public function lecturerGrades() {
        $this->requireAuth('staff');
        $reportModel = $this->model('Report');
        $data = [
            'grades' => $reportModel->getLecturerGrades($_SESSION['LecturerID']),
            'title' => 'Student Performance Summary',
            'page' => 'reports',
            'page_css' => 'staff-dashboard.css'
        ];
        $this->view('reports/lecturer-grades', $data);
    }

    public function hostIndex() {
        $this->requireAuth('host_org');
        try {
            $reportModel = $this->model('Report');
            $data = [
                'students' => $reportModel->getHostStudentStats($_SESSION['host_org_id']),
                'title' => 'Placement Reports',
                'page' => 'reports',
                'page_css' => 'host-org-dashboard.css'
            ];
            $this->view('host/reports', $data);
        } catch (\Throwable $e) {
            error_log("Report Error: " . $e->getMessage());
            echo "<div style='padding:20px;color:red;'>An error occurred.</div>";
        }
    }

    public function hostPerformance() {
        $this->requireAuth('host_org');
        $reportModel = $this->model('Report');
        $data = [
            'performance' => $reportModel->getHostPerformanceReport($_SESSION['host_org_id']),
            'title' => 'Host Performance Report',
            'page' => 'reports',
            'page_css' => 'host-org-dashboard.css'
        ];
        $this->view('reports/host-performance', $data);
    }

    public function studentIndex() {
        $this->requireAuth('student');
        try {
            $reportModel = $this->model('Report');
            $data = [
                'sessions' => $reportModel->getStudentProgress($_SESSION['student_id']),
                'title' => 'My Progress Reports',
                'page' => 'reports',
                'page_css' => 'student-dashboard.css'
            ];
            $this->view('student/reports', $data);
        } catch (\Throwable $e) {
            error_log("Report Error: " . $e->getMessage());
            echo "<div style='padding:20px;color:red;'>An error occurred.</div>";
        }
    }

    public function upload() {
        $this->requireActiveStudent();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['final_report'])) {
            $this->verifyCsrf();
            try {
                $reportModel = $this->model('Report');
                $result = $reportModel->uploadFinalReport($_SESSION['student_id'], $_FILES['final_report']);
                
                $param = $result['success'] ? 'success=Report uploaded successfully' : 'error=' . urlencode($result['message']);
                header("Location: " . Helpers::baseUrl('/student/reports?' . $param));
            } catch (\Throwable $e) {
                error_log("Report Upload Error: " . $e->getMessage());
                header("Location: " . Helpers::baseUrl('/student/reports?error=' . urlencode('An error occurred during upload.')));
            }
        }
    }

    public function printCompletion() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header("Location: " . Helpers::baseUrl('/')); exit(); }

        $studentId = $_GET['id'] ?? ($_SESSION['user_type'] === 'student' ? $_SESSION['student_id'] : 0);
        $userType  = $_SESSION['user_type'];
        
        // Student: only own record
        if ($userType === 'student' && $studentId != $_SESSION['student_id']) {
            http_response_code(403); exit('Unauthorized access.');
        }

        // Host org: restrict to students attached to their org
        if ($userType === 'host_org') {
            $db = (new \App\Config\Database())->connect();
            $stmt = $db->prepare("SELECT 1 FROM attachment WHERE StudentID = ? AND HostOrgID = ? LIMIT 1");
            $stmt->bind_param("ii", $studentId, $_SESSION['host_org_id']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(403); exit('Unauthorized access.');
            }
        }

        // Staff: restrict to supervised students
        if ($userType === 'staff') {
            $db = (new \App\Config\Database())->connect();
            $stmt = $db->prepare("SELECT 1 FROM supervision sup JOIN attachment a ON sup.AttachmentID = a.AttachmentID WHERE a.StudentID = ? AND sup.LecturerID = ? LIMIT 1");
            $stmt->bind_param("ii", $studentId, $_SESSION['LecturerID']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(403); exit('Unauthorized access.');
            }
        }

        $reportModel = $this->model('Report');
        $studentModel = $this->model('Student');

        $data = ['sessions' => $reportModel->getStudentProgress($studentId), 'student' => $studentModel->getById($studentId)];
        $this->view('reports/print-completion', $data, false);
    }

    public function printSupervisors() {
        $this->requireAuth('admin');
        $supervisorModel = $this->model('Supervisor');
        $data = ['supervisors' => $supervisorModel->getAllSupervisors()];
        $this->view('reports/print-supervisors', $data, false);
    }

    public function printAssessmentSchedule() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = ['schedule' => $reportModel->getAssessmentSchedule()];
        $this->view('reports/print-assessment-schedule', $data, false);
    }

    public function printSupervisorStats() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = ['stats' => $reportModel->getSupervisorStats()];
        $this->view('reports/print-supervisor-stats', $data, false);
    }

    public function assessmentSummary() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = [
            'summary' => $reportModel->getAssessmentSummary(),
            'title' => 'Student Assessment Summary',
            'page' => 'reports',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('reports/assessment-summary', $data);
    }

    public function effectiveness() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = [
            'stats' => $reportModel->getSystemEffectiveness(),
            'lecturerStats' => $reportModel->getLecturerAssessedStats(),
            'title' => 'System Effectiveness Report',
            'page' => 'reports',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('reports/effectiveness', $data);
    }

    public function printAssessmentSummary() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = ['summary' => $reportModel->getAssessmentSummary()];
        $this->view('reports/print-assessment-summary', $data, false);
    }

    public function printEffectiveness() {
        $this->requireAuth('admin');
        $reportModel = $this->model('Report');
        $data = [
            'stats' => $reportModel->getSystemEffectiveness(),
            'lecturerStats' => $reportModel->getLecturerAssessedStats()
        ];
        $this->view('reports/print-effectiveness', $data, false);
    }

    public function printHostPerformance() {
        $this->requireAuth('admin');
        $hostId = $_GET['host_id'] ?? 0;
        $reportModel = $this->model('Report');
        $data = ['performance' => $reportModel->getHostPerformanceReport($hostId)];
        $this->view('reports/print-host-performance', $data, false);
    }
}
