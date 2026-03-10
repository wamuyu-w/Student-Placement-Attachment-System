<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;

class AuthController extends Controller {
    
    public function registerStudent() {
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method']);
        }

        // Get JSON input
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation Logic (Moved from process-signup-student.php)
        $errors = [];
        if (empty($data['email'])) $errors['email'] = 'Email required';
        // ... more validation

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors]);
        }

        // Call Model
        $studentModel = $this->model('Student');
        if ($studentModel->emailExists($data['email'])) {
            $this->json(['success' => false, 'message' => 'Email exists']);
        }

        // Proceed to register...
    }

    public function loginStudent() {
        $this->view('auth/login-student', ['title' => 'Student Login'], 'auth');
    }

    public function loginStaff() {
        $this->view('auth/login-staff', ['title' => 'Staff Login'], 'auth');
    }

    public function loginHost() {
        $this->view('auth/login-host', ['title' => 'Host Organization Login'], 'auth');
    }

    public function registerHost() {
        $this->view('auth/register-host', ['title' => 'Host Organization Registration'], 'auth');
    }

    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/'));
            exit();
        }

        $username = Helpers::sanitize($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? ''; // 'student', 'staff', 'host_org'

        $userModel = $this->model('User');
        $user = $userModel->findUserByUsername($username);

        if ($user && ($this->migrateHardcodedPassword($user['UserID'], $password, $user['Password']) || password_verify($password, $user['Password']))) {
            $redirectUrl = '';
            
            // Check Status
            if ($user['Status'] !== 'Active') {
                if ($role === 'student' && $user['Status'] === 'Inactive') {
                    // Allow login but set read-only flag
                    $_SESSION['status'] = 'Inactive';
                } else {
                    $this->redirectWithError($role, "Account is inactive.");
                }
            } else {
                $_SESSION['status'] = 'Active';
            }

            // Verify Role Match (Prevent Student logging in as Staff)
            // Note: Staff login handles both 'Lecturer' and 'Admin' roles in DB
            $dbRole = strtolower($user['Role']);
            $isValidRole = false;

            if ($role === 'student' && $dbRole === 'student') $isValidRole = true;
            if ($role === 'host_org' && $dbRole === 'host organization') $isValidRole = true;
            if ($role === 'staff' && ($dbRole === 'lecturer' || $dbRole === 'admin' || $dbRole === 'supervisor')) $isValidRole = true;

            if (!$isValidRole) {
                $this->redirectWithError($role, "Invalid role for this user.");
            }

            // Set Session
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            
            // Normalize user_type for session
            if ($dbRole === 'lecturer' || $dbRole === 'supervisor') {
                $_SESSION['user_type'] = 'staff';
            } elseif ($dbRole === 'host organization') {
                $_SESSION['user_type'] = 'host_org';
            } else {
                $_SESSION['user_type'] = $dbRole;
            }
            
            // Fetch Profile Data & Redirect
            if ($role === 'student') {
                $profile = $userModel->getStudentProfile($user['UserID']);
                $_SESSION['student_id'] = $profile['StudentID'];
                $_SESSION['first_name'] = $profile['FirstName'];
                $_SESSION['last_name'] = $profile['LastName'];
                $_SESSION['email'] = $profile['Email'];
                $_SESSION['phone'] = $profile['PhoneNumber'];
                $_SESSION['course'] = $profile['Course'];
                $_SESSION['faculty'] = $profile['Faculty'];
                $_SESSION['year_of_study'] = $profile['YearOfStudy'];
                $redirectUrl = Helpers::baseUrl('/student/dashboard');
            } 
            elseif ($role === 'staff') {
                $profile = $userModel->getStaffProfile($user['UserID']);
                $_SESSION['LecturerID'] = $profile['LecturerID'];
                $_SESSION['staff_number'] = $profile['StaffNumber'];
                $_SESSION['name'] = $profile['Name'];
                $_SESSION['role'] = $profile['Role']; // 'Admin' or 'Supervisor'
                
                // Override user_type for Admin
                if (strtolower($profile['Role']) === 'admin') {
                    $_SESSION['user_type'] = 'admin';
                    $redirectUrl = Helpers::baseUrl('/admin/dashboard');
                } else {
                    $redirectUrl = Helpers::baseUrl('/staff/dashboard');
                }
            }
            elseif ($role === 'host_org') {
                $profile = $userModel->getHostProfile($user['UserID']);
                $_SESSION['host_org_id'] = $profile['HostOrgID'];
                $_SESSION['organization_name'] = $profile['OrganizationName'];
                $redirectUrl = Helpers::baseUrl('/host/dashboard');
            }

            // Check for default password
            if ($password === 'Changeme123!') {
                $_SESSION['force_password_change'] = true;
                $redirectUrl = Helpers::baseUrl('/auth/first-login');
                if ($this->isAjax()) {
                    $this->json(['success' => true, 'redirect' => $redirectUrl]);
                } else {
                    header("Location: " . $redirectUrl);
                    exit();
                }
            }

            if ($this->isAjax()) {
                $this->json(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                header("Location: " . $redirectUrl);
                exit();
            }

        } else {
            $this->redirectWithError($role, "Invalid username or password.");
        }
    }

    public function processRegisterHost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/register/host'));
            exit();
        }

        $data = [
            'org_name' => Helpers::sanitize($_POST['org_name'] ?? ''),
            'contact_person' => Helpers::sanitize($_POST['contact_person'] ?? ''),
            'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'phone' => Helpers::sanitize($_POST['phone'] ?? ''),
            'username' => Helpers::sanitize($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];

        // Basic validation
        if (empty($data['org_name']) || empty($data['email']) || empty($data['username']) || empty($data['password'])) {
            header("Location: " . Helpers::baseUrl('/register/host?error=All fields are required.'));
            exit();
        }

        $hostModel = $this->model('Host');
        $userModel = $this->model('User');

        if ($userModel->findUserByUsername($data['username'])) {
            header("Location: " . Helpers::baseUrl('/register/host?error=Username already taken.'));
            exit();
        }

        $result = $hostModel->createFromRegistration($data);

        if ($result['success']) {
            header("Location: " . Helpers::baseUrl('/login/host?success=Registration successful. Please login.'));
        } else {
            header("Location: " . Helpers::baseUrl('/register/host?error=' . urlencode($result['message'])));
        }
        exit();
    }

    private function redirectWithError($role, $message) {
        $route = '';
        switch($role) {
            case 'student': $route = '/login/student'; break;
            case 'staff': $route = '/login/staff'; break;
            case 'host_org': $route = '/login/host'; break;
            default: $route = '/';
        }
        
        if ($this->isAjax()) {
            $this->json(['success' => false, 'message' => $message]);
        }

        header("Location: " . Helpers::baseUrl($route . "?error=" . urlencode($message)));
        exit();
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: " . Helpers::baseUrl('/'));
        exit();
    }

    /**
     * Compares an input password against a potentially hardcoded (plain-text) password.
     * If they match and the stored password is not yet hashed, it hashes and updates it.
     * 
     * @param int $userId The ID of the user
     * @param string $inputPassword The password provided during login
     * @param string $storedPassword The password currently in the database
     * @return bool True if migration was needed and successful, false otherwise
     */
    private function migrateHardcodedPassword($userId, $inputPassword, $storedPassword) {
        // If stored password is already a Bcrypt hash, skip migration
        if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPassword)) {
            return false;
        }

        // Compare plain text
        if ($inputPassword === $storedPassword) {
            $userModel = $this->model('User');
            // updatePassword hashes the input automatically
            return $userModel->updatePassword($userId, $inputPassword);
        }

        return false;
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
