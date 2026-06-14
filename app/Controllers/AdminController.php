<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;

//intialize the class
class AdminController extends Controller {
    
    //function for loading the admin panel dashboard
    public function dashboard() {
        // Ensure the user is authenticated as admin
        $this->requireAuth('admin');

        
        $adminModel = $this->model('Admin');

        //what to load
        $data = [
            'stats' => $adminModel->getDashboardStats(),
            'activities' => $adminModel->getRecentActivities(),
            'title' => 'Administrator Dashboard',
            'page' => 'dashboard',
            'page_css' => 'admin-dashboard.css'
        ];

        $this->view('admin/dashboard', $data);
    }
    
    //enables the admin to see the list of supervisors/lecturers
    public function viewSupervisors() {
        $this->requireAuth('admin');

        // Load Model
        // Load Supervisor model to fetch supervisors and assignable entities
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

    // admin can add a supervisor to the system
    //details are updated in the db 
    public function createSupervisor() {
        $this->requireAuth('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $staffNumber = Helpers::sanitize($_POST['staffNumber'] ?? '');

            //ensures conditions are met e.g. a staff number exists
            //the same staff-number is not added
            if (empty($staffNumber)) {
                header("Location: " . Helpers::baseUrl('/admin/supervisors?error=' . urlencode('Staff Number is required')));
                exit();
            }

            $supervisorModel = $this->model('Supervisor');

            if ($supervisorModel->staffNumberExists($staffNumber)) {
                header("Location: " . Helpers::baseUrl('/admin/supervisors?error=' . urlencode('Supervisor with this Staff Number already exists')));
                exit();
            }

            $result = $supervisorModel->create($staffNumber);

            //detils are pushed to the db if successful, if not error code generated
            if ($result['success']) {
                $msg = "Supervisor added successfully. Login Credentials -> Username: " . $result['username'] . " | Password: " . $result['password'];
                header("Location: " . Helpers::baseUrl('/admin/supervisors?success=' . urlencode($msg)));
            } else {
                header("Location: " . Helpers::baseUrl('/admin/supervisors?error=' . urlencode($result['message'])));
            }
            exit();
        }
    }

    //allows the admin to see the list of students
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
            //verigy csrf token
            $this->verifyCsrf();

            // add department and faculty when creating students = bulk assignment
            $admNumber = Helpers::sanitize($_POST['admNumber']);
            $department = Helpers::sanitize($_POST['department'] ?? '');
            $faculty = Helpers::sanitize($_POST['faculty'] ?? '');
            $studentModel = $this->model('Student');
            
            try {
                $studentModel->createFromAdmin(['admNumber' => $admNumber, 'department' => $department, 'faculty' => $faculty]);
                header("Location: " . Helpers::baseUrl('/admin/students?success=Student added'));
            } catch (\Exception $e) {
                header("Location: " . Helpers::baseUrl('/admin/students?error=' . urlencode($e->getMessage())));
            }
        }
    }

    public function bulkUploadStudents() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
            $this->verifyCsrf();

            // Validate file type (MIME + extension)
            $allowedMimes = ['text/csv', 'application/vnd.ms-excel', 'text/plain', 'application/csv'];
            $fileMime = mime_content_type($_FILES['csvFile']['tmp_name']);
            $fileExt  = strtolower(pathinfo($_FILES['csvFile']['name'], PATHINFO_EXTENSION));
            if (!in_array($fileMime, $allowedMimes) && $fileExt !== 'csv') {
                header("Location: " . Helpers::baseUrl('/admin/students?error=Only CSV files are allowed.'));
                exit();
            }
            $file = $_FILES['csvFile']['tmp_name'];
            $handle = fopen($file, "r");
            $studentModel = $this->model('Student');
            $faculty = Helpers::sanitize($_POST['faculty'] ?? '');
            $department = Helpers::sanitize($_POST['department'] ?? '');
            
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
                        'department' => $department,
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
            $this->verifyCsrf();
            $id = (int)($_POST['student_id'] ?? 0);
            if ($id <= 0) {
                header("Location: " . Helpers::baseUrl('/admin/students?error=Invalid+student+ID'));
                exit();
            }
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
        
        // Get attachment & report status (extract most recent session)
        $progressArray = $reportModel->getStudentProgress($studentId);
        $progress = !empty($progressArray) ? $progressArray[0] : null;

        $hostOrg = $progress['OrganizationName'] ?? 'Not Assigned';

        // Fetch Supervisor Details if attached
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
            'page_css' => 'admin-dashboard.css'
        ];
        $this->view('admin/student-progress', $data);
    }
// the function assignSupervisor processes the assignment of a supervisor to a student's attachment, ensuring that no more than 2 supervisors are assigned and that the first assessment is completed before allowing a second supervisor to be assigned 
    public function assignSupervisor() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $attachmentId = Helpers::sanitize($_POST['attachment_id']);
            $lecturerId = Helpers::sanitize($_POST['lecturer_id']);
            
            $supervisorModel = $this->model('Supervisor');
            $result = $supervisorModel->assign($attachmentId, $lecturerId);

            if ($result['success']) {
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
                        $lecInfo['Username'] . '@example.com' // Using username as email if real email isn't in DB
                    );
                }
            }

            $param = $result['success'] ? 'success=Supervisor assigned successfully' : 'error=' . urlencode($result['message']);
            header("Location: " . Helpers::baseUrl('/admin/supervisors?' . $param));
        }
    }
// the function bulkUploadSupervisors processes the bulk upload of supervisors from a CSV file, creating user accounts and lecturer records for each valid entry while handling duplicates and errors gracefully, and then redirects back to the supervisor management page with a summary of the results
    public function bulkUploadSupervisors() {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
            $this->verifyCsrf();
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
