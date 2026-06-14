<?php
namespace App\Controllers;

use App\Core\Controller;
/**
 * Class StudentController
 * 
 * Handles student-specific endpoints, such as rendering the main student dashboard
 * and viewing their assigned academic supervisors and assessment status.
 */
class StudentController extends Controller {
    
    /**
     * Renders the primary student dashboard.
     * Fetches top-level placement statistics and recent timeline activities.
     * 
     * @return void
     */
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

    /**
     * Renders the student's supervisor view.
     * Displays details about their assigned academic supervisor(s) and any completed
     * or pending assessments.
     * 
     * @return void
     */
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
