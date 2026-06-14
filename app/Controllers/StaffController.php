<?php
namespace App\Controllers;

use App\Core\Controller;
/**
 * Class StaffController
 * 
 * Handles staff-specific endpoints including rendering the academic supervisor's dashboard, 
 * viewing their assigned students, and managing their supervision tasks.
 */
class StaffController extends Controller {
    
    /**
     * Renders the academic supervisor's main dashboard.
     * Fetches top-level statistics and recently submitted logbooks by their students.
     * 
     * @return void
     */
    public function dashboard() {
        $this->requireAuth('staff');
        
        $staffId = $_SESSION['LecturerID'];
        $staffModel = $this->model('Staff');
        
        $data = [
            'stats' => $staffModel->getDashboardStats($staffId),
            'recentLogs' => $staffModel->getRecentLogs($staffId),
            'title' => 'Lecturer Dashboard',
            'page' => 'dashboard',
            'page_css' => 'staff-dashboard.css'
        ];
        
        $this->view('staff/dashboard', $data);
    }

    /**
     * Renders the staff view of all students assigned to them for supervision.
     * 
     * @return void
     */
    public function viewStudents() {
        $this->requireAuth('staff');
        $staffModel = $this->model('Staff');
        $data = [
            'students' => $staffModel->getSupervisedStudents($_SESSION['LecturerID']),
            'title' => 'My Students',
            'page' => 'students',
            'page_css' => 'staff-dashboard.css'
        ];
        $this->view('staff/students', $data);
    }

    /**
     * Renders the staff supervision management view.
     * Allows the lecturer to oversee and manage their assigned cohort of students.
     * 
     * @return void
     */
    public function supervision() {
        $this->requireAuth('staff');
        $staffModel = $this->model('Staff');
        $data = [
            'students' => $staffModel->getSupervisedStudents($_SESSION['LecturerID']),
            'title' => 'Supervision Management',
            'page' => 'supervision',
            'page_css' => 'staff-dashboard.css'
        ];
        $this->view('staff/supervision', $data);
    }
}
