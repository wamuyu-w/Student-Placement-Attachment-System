<?php
namespace App\Controllers;

use App\Core\Controller;
// StudentController handles all student-related actions such as dashboard and supervisor view
class StudentController extends Controller {
    
    public function dashboard() {
        $this->requireAuth('student');
        
        $studentId = $_SESSION['student_id'];
        $studentModel = $this->model('Student');
        
        $data = [
            'stats' => $studentModel->getDashboardStats($studentId),
            'activities' => $studentModel->getRecentActivities($studentId),
            'title' => 'Student Dashboard',
            'page' => 'dashboard',
            'page_css' => 'student-dashboard.css'
        ];
        // Load View
        $this->view('student/dashboard', $data);
    }

    public function viewSupervisor() {
        $this->requireAuth('student');
        $studentModel = $this->model('Student');
        $studentId = $_SESSION['student_id'];
        
        $supervisors = $studentModel->getSupervisors($studentId);
        $assessments = $studentModel->getAssessments($studentId);
        
        $data = [
            'supervisors' => $supervisors,
            'assessments' => $assessments,
            'title' => 'My Supervisor',
            'page' => 'supervisor',
            'page_css' => ['student-dashboard.css', 'supervisor.css']
        ];
        $this->view('student/supervisor', $data);
    }
}
