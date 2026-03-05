<?php
namespace App\Controllers;
// The AdminController class manages administrative functions such as viewing the dashboard, managing supervisors and students, and handling bulk uploads. 
//It ensures that only authenticated administrators can access these features and interacts with the corresponding models to retrieve and manipulate data before passing it to the views for rendering.
use App\Core\Controller;
use App\Core\Helpers;

class AdminController extends Controller {
    
    public function dashboard() {
        $this->requireAuth('admin');

        $adminModel = $this->model('Admin');

        $data = [
            'stats' => $adminModel->getDashboardStats(),
            'activities' => $adminModel->getRecentActivities(),
            'title' => 'Administrator Dashboard',
            'page' => 'dashboard',
            'page_css' => 'admin-dashboard.css'
        ];

        $this->view('admin/dashboard', $data);
    }
    
    public function viewSupervisors() {
        $this->requireAuth('admin');

        // Load Model
        $supervisorModel = $this->model('Supervisor');
        
        $data = [
            'supervisors' => $supervisorModel->getAllSupervisors(),
            'assignableStudents' => $supervisorModel->getStudentsForAssignment(),
            'assignableLecturers' => $supervisorModel->getAssignableLecturers(),
            'title' => 'Manage Supervisors',
            'page' => 'supervisors',
            'page_css' => 'admin-dashboard.css'
        ];

        // Load View
        $this->view('admin/supervisors', $data);
    }

    public function createSupervisor() {
        $this->requireAuth('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $staffNumber = Helpers::sanitize($_POST['staffNumber'] ?? '');

            if (empty($staffNumber)) {
                header("Location: ../supervisors?error=" . urlencode("Staff Number is required"));
                exit();
            }

            $supervisorModel = $this->model('Supervisor');

            if ($supervisorModel->staffNumberExists($staffNumber)) {
                header("Location: ../supervisors?error=" . urlencode("Supervisor with this Staff Number already exists"));
                exit();
            }

            $result = $supervisorModel->create($staffNumber);

            if ($result['success']) {
                $msg = "Supervisor added successfully. Login Credentials -> Username: " . $result['username'] . " | Password: " . $result['password'];
                header("Location: ../supervisors?success=" . urlencode($msg));
            } else {
                header("Location: ../supervisors?error=" . urlencode($result['message']));
            }
            exit();
        }
    }

    public function viewStudents() {
        $this->requireAuth('admin');
        $studentModel = $this->model('Student');
        
        $data = [
            'students' => $studentModel->getAll(),
            'title' => 'Manage Students',
            'page' => 'students',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('admin/students', $data);
    }

    public function createStudent() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admNumber = Helpers::sanitize($_POST['admNumber']);
            $studentModel = $this->model('Student');
            
            try {
                $studentModel->createFromAdmin(['admNumber' => $admNumber]);
                header("Location: " . Helpers::baseUrl('/admin/students?success=Student added'));
            } catch (\Exception $e) {
                header("Location: " . Helpers::baseUrl('/admin/students?error=' . urlencode($e->getMessage())));
            }
        }
    }

    public function bulkUploadStudents() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['csvFile']['tmp_name'];
            $handle = fopen($file, "r");
            $studentModel = $this->model('Student');
            $faculty = Helpers::sanitize($_POST['faculty'] ?? '');
            
            $successCount = 0;
            $row = 0;
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                // Skip header if first row looks like header
                if ($row == 1 && (strtolower($data[0]) == 'admissionnumber' || strtolower($data[0]) == 'admno')) continue;
                if (count($data) < 1 || empty($data[0])) continue;

                try {
                    $studentData = [
                        'admNumber' => Helpers::sanitize($data[0]),
                        'firstName' => Helpers::sanitize($data[1] ?? ''),
                        'lastName' => Helpers::sanitize($data[2] ?? ''),
                        'faculty' => $faculty,
                        'is_bulk' => true
                    ];
                    if ($studentModel->createFromAdmin($studentData)) $successCount++;
                } catch (\Exception $e) { continue; }
            }
            fclose($handle);
            header("Location: " . Helpers::baseUrl("/admin/students?success=Imported $successCount students"));
        } else {
            header("Location: " . Helpers::baseUrl('/admin/students?error=Upload failed'));
        }
    }

    public function clearStudent() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['student_id'];
            $studentModel = $this->model('Student');
            $result = $studentModel->clearStudent($id);
            
            $param = $result['success'] ? 'success=Student cleared' : 'error=' . urlencode($result['message']);
            header("Location: " . Helpers::baseUrl('/admin/students?' . $param));
        }
    }
// the function viewStudentProgress gets the progress of a student,
// including their attachment status, report status, assessments,
// and logbook entries, and passes this data to the view for display
    public function viewStudentProgress() {
        $this->requireAuth('admin');
        $studentId = $_GET['id'] ?? null;
        
        if (!$studentId) {
            header("Location: " . Helpers::baseUrl('/admin/students'));
            exit();
        }

        $studentModel = $this->model('Student');
        $reportModel = $this->model('Report'); // Reusing Report model for progress details
        $assessmentModel = $this->model('Assessment');
        $logbookModel = $this->model('Logbook');

        // Get basic student info
        $student = $studentModel->getById($studentId);
        
        // Get attachment & report status
        $progress = $reportModel->getStudentProgress($studentId);

        $data = [
            'student' => $student,
            'progress' => $progress,
            'assessments' => $assessmentModel->getStudentAssessments($studentId),
            'logbookEntries' => $logbookModel->getEntriesByStudent($studentId),
            'title' => 'Student Progress',
            'page' => 'students',
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('admin/student-progress', $data);
    }
// the function assignSupervisor processes the assignment of a supervisor to a student's attachment, ensuring that no more than 2 supervisors are assigned and that the first assessment is completed before allowing a second supervisor to be assigned 
    public function assignSupervisor() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attachmentId = Helpers::sanitize($_POST['attachment_id']);
            $lecturerId = Helpers::sanitize($_POST['lecturer_id']);
            
            $supervisorModel = $this->model('Supervisor');
            $result = $supervisorModel->assign($attachmentId, $lecturerId);

            $param = $result['success'] ? 'success=Supervisor assigned successfully' : 'error=' . urlencode($result['message']);
            header("Location: " . Helpers::baseUrl('/admin/supervisors?' . $param));
        }
    }
// the function bulkUploadSupervisors processes the bulk upload of supervisors from a CSV file, creating user accounts and lecturer records for each valid entry while handling duplicates and errors gracefully, and then redirects back to the supervisor management page with a summary of the results
    public function bulkUploadSupervisors() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
            $file = $_FILES['csvFile']['tmp_name'];
            $faculty = Helpers::sanitize($_POST['faculty'] ?? '');

            $supervisorModel = $this->model('Supervisor');
            $result = $supervisorModel->createBulk($file, $faculty);

            $param = "success=Imported " . $result['successCount'] . " supervisors. Failed/Duplicate: " . $result['errorCount'];
            header("Location: " . Helpers::baseUrl('/admin/supervisors?' . $param));
        } else {
            header("Location: " . Helpers::baseUrl('/admin/supervisors?error=Upload failed'));
        }
    }
}
