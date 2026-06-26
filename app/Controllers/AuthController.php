<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Helpers;

/**
 * Class AuthController
 * 
 * Handles all authentication and authorization workflows including login, registration, 
 * password resets, session management, and logout functionality.
 */
class AuthController extends Controller {
    /**
     * Registers a new student via an asynchronous JSON POST request.
     * Validates input fields and ensures the email does not already exist.
     * 
     * @return void JSON response
     */
    public function registerStudent() {
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid method']);
        }

        // Get JSON input
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation Logic
        $errors = [];
        if (empty($data['email'])) $errors['email'] = 'Email required';
        
        //if not, bring an error
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


    /**
     * Renders the student login page.
     * 
     * @return void
     */
    public function loginStudent() {
        $this->view('auth/login-student', ['title' => 'Student Login'], 'auth');
    }
    /**
     * Renders the staff (lecturer/admin) login page.
     * Note: Admins and lecturers share the same portal endpoint but are routed based on DB roles.
     * 
     * @return void
     */
    public function loginStaff() {
        $this->view('auth/login-staff', ['title' => 'Staff Login'], 'auth');
    }

    /**
     * Renders the host organization login page.
     * 
     * @return void
     */
    public function loginHost() {
        $this->view('auth/login-host', ['title' => 'Host Organization Login'], 'auth');
    }

    /**
     * Renders the host organization registration page.
     * 
     * @return void
     */
    public function registerHost() {
        $this->view('auth/register-host', ['title' => 'Host Organization Registration'], 'auth');
    }

    /**
     * Core login processor for all roles.
     * Validates CSRF tokens, enforces 10-minute rate limiting against brute force attacks,
     * verifies passwords, normalizes sessions, and redirects to appropriate dashboards.
     * 
     * @return void
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/'));
            exit();
        }

        //verify CSRF Token - prevent CSRF Attacks
        $this->verifyCsrf();

        //fetch the username and passwords, plus the role
        $username = Helpers::sanitize($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? ''; // this can either be'student', 'staff', 'host_org'

        // --- Rate Limiting ---
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'login_fail_' . md5($ip);
        if (!isset($_SESSION[$key])) $_SESSION[$key] = ['count' => 0, 'time' => time()];
        // Reset window every 10 minutes
        if (time() - $_SESSION[$key]['time'] > 600) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        if ($_SESSION[$key]['count'] >= 10) {
            $this->redirectWithError($role, 'Too many failed login attempts. Please try again later.');
        }

        $userModel = $this->model('User');
        $user = $userModel->findUserByUsername($username);

        if ($user && ($this->migrateHardcodedPassword($user['UserID'], $password, $user['Password']) || password_verify($password, $user['Password']))) {
            // Reset failed login counter on success
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $key = 'login_fail_' . md5($ip);
            unset($_SESSION[$key]);

            $this->regenerateSession();
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
            $dbRole = $user['Role'];
            $isValidRole = false;

            if ($role === 'student' && $dbRole === 'Student') $isValidRole = true;
            if ($role === 'host_org' && $dbRole === 'Host Organization') $isValidRole = true;
            if ($role === 'staff' && ($dbRole === 'Lecturer' || $dbRole === 'Admin' || $dbRole === 'Supervisor')) $isValidRole = true;

            if (!$isValidRole) {
                $this->redirectWithError($role, "Invalid role for this user.");
                return; // explicit guard — redirectWithError exits, but be explicit
            }

            // Set Session
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            
            // Normalize user_type for session
            if ($dbRole === 'Lecturer' || $dbRole === 'Supervisor') {
                $_SESSION['user_type'] = 'staff';
            } elseif ($dbRole === 'Host Organization') {
                $_SESSION['user_type'] = 'host_org';
            } else {
                $_SESSION['user_type'] = strtolower($dbRole);
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
                if (!$profile || empty($profile['HostOrgID'])) {
                    $this->redirectWithError($role, 'Host organization profile not found. Please contact the administrator.');
                }
                $_SESSION['host_org_id'] = $profile['HostOrgID'];
                $_SESSION['organization_name'] = $profile['OrganizationName'];
                $redirectUrl = Helpers::baseUrl('/host/dashboard');
            }

            // Check for default or temporary password
            if ($password === 'Changeme123!' || strpos($password, 'TEMP_') === 0) {
                $_SESSION['force_password_change'] = true;
                $redirectUrl = Helpers::baseUrl('/auth/first-login?role=' . urlencode($_SESSION['user_type']));
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
            // Increment failed login counter
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $key = 'login_fail_' . md5($ip);
            if (!isset($_SESSION[$key])) $_SESSION[$key] = ['count' => 0, 'time' => time()];
            $_SESSION[$key]['count']++;
            $this->redirectWithError($role, "Invalid username or password.");
        }
    }

    /**
     * Processes a Host Organization's registration submission.
     * Validates input, creates the host record, and automatically triggers
     * a welcome email containing their login credentials.
     * 
     * @return void
     */
    public function processRegisterHost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/register/host'));
            exit();
        }

        $this->verifyCsrf();

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
            // Send welcome email with credentials
            \App\Core\Mailer::sendHostCredentials(
                $data['email'], 
                $data['org_name'], 
                $data['username'], 
                $data['password']
            );
            
            header("Location: " . Helpers::baseUrl('/login/host?success=' . urlencode('Registration successful. Please login.')));
        } else {
            header("Location: " . Helpers::baseUrl('/register/host?error=' . urlencode($result['message'])));
        }
        exit();
    }

    /**
     * Renders the "Forgot Password" request form.
     * 
     * @return void
     */
    public function forgotPassword() {
        $this->view('auth/forgot-password', ['title' => 'Forgot Password'], 'auth');
    }

    /**
     * Handles the forgot password form submission.
     * Validates email, enforces rate limiting, generates a temporary password,
     * updates the database, and emails the user the new temporary credentials.
     * 
     * @return void
     */
    public function processForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/auth/forgot-password'));
            exit();
        }

        $this->verifyCsrf();
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Valid email is required.']);
            }
            header("Location: " . Helpers::baseUrl('/auth/forgot-password?error=' . urlencode('Valid email is required.')));
            exit();
        }

        // --- Rate Limiting ---
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'forgot_pw_' . md5($ip);
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        // Reset window every 15 minutes
        if (time() - $_SESSION[$key]['time'] > 900) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        if ($_SESSION[$key]['count'] >= 3) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Too many requests. Please try again later.']);
            }
            header("Location: " . Helpers::baseUrl('/auth/forgot-password?error=' . urlencode('Too many requests. Please try again later.')));
            exit();
        }

        $_SESSION[$key]['count']++;

        $userModel = $this->model('User');
        $userObj = $userModel->findUserByEmail($email);

        if ($userObj) {
            $randomString = bin2hex(random_bytes(4)); // 8 characters
            $tempPassword = 'TEMP_' . strtoupper($randomString);
            
            // Update password in database directly
            $userModel->updatePassword($userObj['UserID'], $tempPassword);
            
            // Send email with temporary password
            $sent = \App\Core\Mailer::sendDefaultPassword($email, $userObj['Name'], $tempPassword);
            
            if ($sent) {
                if ($this->isAjax()) {
                    $this->json(['success' => true, 'message' => 'A temporary password has been sent to your email address.']);
                }
                header("Location: " . Helpers::baseUrl('/auth/forgot-password?success=' . urlencode('A temporary password has been sent to your email address.')));
            } else {
                if ($this->isAjax()) {
                    $this->json(['success' => false, 'message' => 'Failed to send email. Please contact support.']);
                }
                header("Location: " . Helpers::baseUrl('/auth/forgot-password?error=' . urlencode('Failed to send email. Please contact support.')));
            }
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'No account found with that email address.']);
            }
            header("Location: " . Helpers::baseUrl('/auth/forgot-password?error=' . urlencode('No account found with that email address.')));
        }
        exit();
    }

    /**
     * Displays the password reset form when a valid reset token is provided in the URL.
     * 
     * @return void
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            header("Location: " . Helpers::baseUrl('/?error=' . urlencode('Invalid reset token.')));
            exit();
        }

        $userModel = $this->model('User');
        $user = $userModel->findUserByResetToken($token);

        if (!$user || strtotime($user['ResetTokenExpiry']) < time()) {
            header("Location: " . Helpers::baseUrl('/?error=' . urlencode('Invalid or expired reset token.')));
            exit();
        }

        $this->view('auth/reset-password', ['title' => 'Reset Password', 'token' => $token], 'auth');
    }

    /**
     * Processes the submission of a new password from the reset form.
     * Validates token and password complexity, updates the user's password,
     * clears the token, and redirects them to the appropriate login page.
     * 
     * @return void
     */
    public function processResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . Helpers::baseUrl('/'));
            exit();
        }

        $this->verifyCsrf();

        $token = $_POST['token'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($newPass) || empty($confirmPass)) {
            header("Location: " . Helpers::baseUrl('/auth/reset-password?token=' . urlencode($token) . '&error=' . urlencode('All fields are required.')));
            exit();
        }

        if ($newPass !== $confirmPass) {
            header("Location: " . Helpers::baseUrl('/auth/reset-password?token=' . urlencode($token) . '&error=' . urlencode('Passwords do not match.')));
            exit();
        }

        if (strlen($newPass) < 6) {
            header("Location: " . Helpers::baseUrl('/auth/reset-password?token=' . urlencode($token) . '&error=' . urlencode('Password must be at least 6 characters long.')));
            exit();
        }

        $userModel = $this->model('User');
        $user = $userModel->findUserByResetToken($token);

        if (!$user || strtotime($user['ResetTokenExpiry']) < time()) {
            header("Location: " . Helpers::baseUrl('/?error=' . urlencode('Invalid or expired reset token.')));
            exit();
        }

        if ($userModel->updatePassword($user['UserID'], $newPass)) {
            $userModel->clearPasswordResetToken($user['UserID']);
            
            // Redirect based on role
            $dbRole = $user['Role'];
            $redirectRole = 'student';
            if ($dbRole === 'Lecturer' || $dbRole === 'Admin' || $dbRole === 'Supervisor') $redirectRole = 'staff';
            if ($dbRole === 'Host Organization') $redirectRole = 'host';

            header('Location: ' . Helpers::baseUrl('/?success=' . urlencode('Password reset successful. Please log in.')));
        } else {
            header("Location: " . Helpers::baseUrl('/auth/reset-password?token=' . urlencode($token) . '&error=' . urlencode('An error occurred. Please try again.')));
        }
        exit();
    }

    /**
     * Helper utility to redirect users back to their role-specific login page
     * with an attached URL-encoded error message.
     * 
     * @param string $role The user's intended role
     * @param string $message The error message to display
     * @return void
     */
    private function redirectWithError($role, $message) {
        $route = '';
        switch($role) {
            case 'student': $route = '/login/student'; break;
            case 'staff': $route = '/login/staff'; break;
            case 'host_org': $route = '/login/host'; break;
            default: $route = '/';
        }
        
        if ($this->isAjax()) {
            $this->json(['success' => false, 'message' => $message]); // exits internally
            return; // explicit guard for future safety
        }

        header("Location: " . Helpers::baseUrl($route . "?error=" . urlencode($message)));
        exit();
    }

    /**
     * Destroys the current session, deletes session cookies, 
     * and logs the user completely out of the system.
     * 
     * @return void
     */
    public function logout() {
        // Ensure a session exists
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Clear all session data
        $_SESSION = [];
        // Delete the session cookie if used
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        // Destroy the session
        session_destroy();
        // Redirect to the student login page after logout
        header('Location: ' . Helpers::baseUrl('/?success=' . urlencode('You have successfully logged out.')));
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
    // Migrates legacy plain-text passwords to hashed versions if needed
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

    /**
     * Determines if the current HTTP request is an AJAX/Fetch request.
     * 
     * @return bool True if AJAX, False otherwise
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
