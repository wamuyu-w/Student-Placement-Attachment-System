<?php
namespace App\Controllers;
// this controller manages the host organization's dashboard, allowing them to view their dashboard, see attached students, manage supervision, and generate unique codes for student attachments. 
// It ensures that only authenticated host organizations can access these functionalities and interacts with the Host model to retrieve and manipulate data as needed.
use App\Core\Controller;
use App\Core\Helpers;

class HostController extends Controller {
    
    public function dashboard() {
        $this->requireAuth('host_org');
        
        $hostOrgId = $_SESSION['host_org_id'];
        $hostModel = $this->model('Host');
        
        $data = [
            'stats' => $hostModel->getDashboardStats($hostOrgId),
            'recentApps' => $hostModel->getRecentPlacements($hostOrgId),
            'title' => 'Host Organization Dashboard',
            'page' => 'dashboard',
            'page_css' => 'host-org-dashboard.css'
        ];
        
        $this->view('host/dashboard', $data);
    }
// this function gets the students attached to a host organization and their details, including the status of their attachment and passes it to the view
    public function viewStudents() {
        $this->requireAuth('host_org');
        $hostModel = $this->model('Host');
        
        $data = [
            'students' => $hostModel->getAttachedStudents($_SESSION['host_org_id']),
            'title' => 'Attached Students', 
            'page' => 'students', 
            'page_css' => 'host-org-dashboard.css'
        ];
        $this->view('host/students', $data);
    }
    // this function supervision() retrieves the students attached to a host organization along with their attachment details
    // and passes this information to the supervision view,
    // allowing the host organization to manage and oversee the students under their supervision effectively
    public function supervision() {
        $this->requireAuth('host_org');
        $hostModel = $this->model('Host');
        
        $data = [
            'students' => $hostModel->getAttachedStudents($_SESSION['host_org_id']),
            'title' => 'Supervision & Codes', 
            'page' => 'supervision', 
            'page_css' => 'host-org-dashboard.css'
        ];
        $this->view('host/supervision', $data);
    }
    // this function generates a unique 6-character alphanumeric code for an attachment, ensuring that the attachment belongs to the host organization before updating the record with the new code
    public function generateCode() {
        $this->requireAuth('host_org');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $attachmentId = $_POST['attachment_id'];
            $hostModel = $this->model('Host');
            
            $result = $hostModel->generateAssessmentCode($attachmentId, $_SESSION['host_org_id']);
            $param = $result ? 'success=Code generated successfully' : 'error=Failed to generate code';
            
            header("Location: " . Helpers::baseUrl('/host/supervision?' . $param));
        }
    }
}
