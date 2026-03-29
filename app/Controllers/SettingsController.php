<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;
// It includes methods for displaying settings pages, updating profile information, changing passwords, and handling the first login process where users must complete their profile and change their default password.
class SettingsController extends Controller {

    public function studentIndex() {
        $this->requireAuth('student');
        $userModel = $this->model('User');
        $profile = $userModel->getStudentProfile($_SESSION['user_id']);
        
        $this->view('student/settings', ['profile' => $profile, 'title' => 'Settings', 'page' => 'settings', 'page_css' => 'student-dashboard.css']);
    }

    public function staffIndex() {
        $this->requireAuth('staff');
        $userModel = $this->model('User');
        $profile = $userModel->getStaffProfile($_SESSION['user_id']);
        
        $this->view('staff/settings', ['profile' => $profile, 'title' => 'Settings', 'page' => 'settings', 'page_css' => 'staff-dashboard.css']);
    }
    
    public function adminIndex() {
        $this->requireAuth('admin');
        $userModel = $this->model('User');
        $profile = $userModel->getStaffProfile($_SESSION['user_id']);
        
        $this->view('admin/settings', ['profile' => $profile, 'title' => 'Settings', 'page' => 'settings', 'page_css' => 'admin-dashboard.css']);
    }

    public function hostIndex() {
        $this->requireAuth('host_org');
        $userModel = $this->model('User');
        $profile = $userModel->getHostProfile($_SESSION['user_id']);
        
        $this->view('host/settings', ['profile' => $profile, 'title' => 'Settings', 'page' => 'settings', 'page_css' => 'host-org-dashboard.css']);
    }

    public function updateProfile() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $role = $_SESSION['user_type'] ?? '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $success = false;
            
            if ($role === 'student') {
                $model = $this->model('Student');
                $data = [
                    'email' => Helpers::sanitize($_POST['email']),
                    'phone' => Helpers::sanitize($_POST['phone'])
                ];
                $success = $model->updateProfile($_SESSION['student_id'], $data);
                if ($success) {
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['phone'] = $data['phone'];
                }
                $redirect = '/student/settings';
            } elseif ($role === 'staff' || $role === 'admin') {
                $model = $this->model('Staff');
                $data = [
                    'name' => Helpers::sanitize($_POST['name']),
                    'department' => Helpers::sanitize($_POST['department'])
                ];
                $success = $model->updateProfile($_SESSION['LecturerID'], $data);
                if ($success) {
                    $_SESSION['name'] = $data['name'];
                    $_SESSION['department'] = $data['department'];
                }
                $redirect = ($role === 'admin') ? '/admin/settings' : '/staff/settings';
            } elseif ($role === 'host_org') {
                $model = $this->model('Host');
                $data = [
                    'org_name' => Helpers::sanitize($_POST['org_name']),
                    'contact_person' => Helpers::sanitize($_POST['contact_person']),
                    'email' => Helpers::sanitize($_POST['email']),
                    'phone' => Helpers::sanitize($_POST['phone'])
                ];
                $success = $model->updateProfile($_SESSION['host_org_id'], $data);
                if ($success) {
                    $_SESSION['organization_name'] = $data['org_name'];
                    $_SESSION['contact_person'] = $data['contact_person'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['phone_number'] = $data['phone'];
                }
                $redirect = '/host/settings';
            }
            
            if ($success) {
                header("Location: " . Helpers::baseUrl($redirect . '?success=Profile updated'));
            } else {
                header("Location: " . Helpers::baseUrl($redirect . '?error=Update failed'));
            }
        }
    }

    public function updatePassword() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
            $role = $_SESSION['user_type'];
            $redirect = match($role) {
                'student' => '/student/settings',
                'staff' => '/staff/settings',
                'admin' => '/admin/settings',
                'host_org' => '/host/settings',
                default => '/'
            };

            if ($new !== $confirm) {
                header("Location: " . Helpers::baseUrl($redirect . '?error=Passwords do not match'));
                exit();
            }

            $userModel = $this->model('User');
            if ($userModel->verifyPassword($_SESSION['user_id'], $current)) {
                if ($userModel->updatePassword($_SESSION['user_id'], $new)) {
                    header("Location: " . Helpers::baseUrl($redirect . '?success=Password changed'));
                } else {
                    header("Location: " . Helpers::baseUrl($redirect . '?error=Database error'));
                }
            } else {
                header("Location: " . Helpers::baseUrl($redirect . '?error=Incorrect current password'));
            }
        }
    }
    //then loads the first-login view with the appropriate data and layout for the user to complete their profile and change their default password
    public function firstLogin() {
        $role = $_SESSION['user_type'] ?? null;
        if (!$role) {
            header("Location: " . Helpers::baseUrl('/'));
            exit();
        }
        $this->requireAuth($role);
        $userModel = $this->model('User');
        $profile = [];
        
        if ($role === 'student') {
            $profile = $userModel->getStudentProfile($_SESSION['user_id']);
        } elseif ($role === 'staff' || $role === 'admin') {
            $profile = $userModel->getStaffProfile($_SESSION['user_id']);
        } elseif ($role === 'host_org') {
            $profile = $userModel->getHostProfile($_SESSION['user_id']);
        }
        
        $this->view('auth/first-login', ['profile' => $profile, 'role' => $role], 'auth');
    }
    // updating the user's password and profile information based on their role, and then redirects them to their respective dashboard upon successful completion
    public function processFirstLogin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/auth/first-login'));
            exit();
        }

        $this->verifyCsrf();

        $role = $_SESSION['user_type'];
        $userId = $_SESSION['user_id'];
        
        // Password Update
        $newPass = $_POST['new_password'];
        $confirmPass = $_POST['confirm_password'];
        
        if ($newPass === 'Changeme123!') {
             header("Location: " . Helpers::baseUrl('/auth/first-login?error=Please choose a different password'));
             exit();
        }
        
        if ($newPass !== $confirmPass) {
             header("Location: " . Helpers::baseUrl('/auth/first-login?error=Passwords do not match'));
             exit();
        }
        
        $userModel = $this->model('User');
        $userModel->updatePassword($userId, $newPass);
        
        // Profile Update
        $success = false;
        
        // Ensure Session ID exists
        if (!isset($_SESSION['user_type']) || empty($_SESSION['user_id'])) {
             header("Location: " . Helpers::baseUrl('/auth/first-login?error=Session expired. Please login again.'));
             exit();
        }

        // Fetch current profile to ensure immutable fields aren't changed maliciously
        $profile = [];
        if ($role === 'student') {
            $profile = $userModel->getStudentProfile($userId);
        } elseif ($role === 'staff' || $role === 'admin') {
            $profile = $userModel->getStaffProfile($userId);
        } elseif ($role === 'host_org') {
            $profile = $userModel->getHostProfile($userId);
        }

        if ($role === 'student' && isset($_SESSION['student_id'])) {
            $model = $this->model('Student');
            $data = [
                //make some details of the student added immutable (since they were obtained from the .csv file in bulk upload)
                'firstName' => !empty($profile['FirstName']) ? $profile['FirstName'] : Helpers::sanitize($_POST['firstName']),
                'lastName' => !empty($profile['LastName']) ? $profile['LastName'] : Helpers::sanitize($_POST['lastName']),
                'email' => Helpers::sanitize($_POST['email']),
                'phone' => Helpers::sanitize($_POST['phone']),
                'course' => Helpers::sanitize($_POST['course']),
                'faculty' => Helpers::sanitize($_POST['faculty']),
                'yearOfStudy' => (int)$_POST['yearOfStudy']
            ];
            $success = $model->completeProfile($_SESSION['student_id'], $data);
            if ($success) {
                $_SESSION['first_name'] = $data['firstName'];
                $_SESSION['last_name'] = $data['lastName'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['phone'] = $data['phone'];
                $_SESSION['course'] = $data['course'];
                $_SESSION['faculty'] = $data['faculty'];
                $_SESSION['year_of_study'] = $data['yearOfStudy'];
            }
        } elseif (($role === 'staff' || $role === 'admin') && isset($_SESSION['LecturerID'])) {
            $model = $this->model('Staff');
            $data = [
                'name' => !empty($profile['Name']) ? $profile['Name'] : Helpers::sanitize($_POST['name']),
                'department' => Helpers::sanitize($_POST['department']),
                'faculty' => Helpers::sanitize($_POST['faculty'])
            ];
            $success = $model->completeProfile($_SESSION['LecturerID'], $data);
            if ($success) {
                $_SESSION['name'] = $data['name'];
                $_SESSION['department'] = $data['department'];
            }
        } elseif ($role === 'host_org' && isset($_SESSION['host_org_id'])) {
            $model = $this->model('Host');
            $data = [
                'org_name' => Helpers::sanitize($_POST['organization_name']),
                'contact_person' => Helpers::sanitize($_POST['contact_person']),
                'email' => Helpers::sanitize($_POST['email']),
                'phone' => Helpers::sanitize($_POST['phone_number'])
            ];
            $success = $model->completeProfile($_SESSION['host_org_id'], $data);
            if ($success) {
                $_SESSION['organization_name'] = $data['org_name'];
                $_SESSION['contact_person'] = $data['contact_person'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['phone_number'] = $data['phone'];
            }
        }
        
        if ($success) {
            unset($_SESSION['force_password_change']);
            $redirect = match($role) {
                'student' => '/student/dashboard',
                'staff' => '/staff/dashboard',
                'admin' => '/admin/dashboard',
                'host_org' => '/host/dashboard',
                default => '/'
            };
            header("Location: " . Helpers::baseUrl($redirect));
        } else {
            header("Location: " . Helpers::baseUrl('/auth/first-login?error=Profile update failed'));
        }
    }
}
