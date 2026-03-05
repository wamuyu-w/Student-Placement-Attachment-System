<?php
namespace App\Controllers;

use App\Core\Controller;
// StaffController handles all staff-related actions such as dashboard, student management, and supervision.
class StaffController extends Controller {
    
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
