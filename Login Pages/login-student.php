<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors as HTML
ini_set('log_errors', 1);

require_once '../config.php';

// Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Strictly enforce POST method only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    header('Allow: POST');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

//initialise username and password variables for validation
$username = sanitizeInput($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Ensure both fields are provided
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit();
}

try {
    $conn = getDBConnection();

// Check the users table for student accounts
$stmt = $conn->prepare("SELECT u.UserID, u.Username, u.Password, u.Role, u.Status, 
                        s.StudentID, s.FirstName, s.LastName, s.Course, s.Faculty, 
                        s.YearOfStudy, s.PhoneNumber, s.Email, s.EligibilityStatus
                        FROM users u
                        INNER JOIN student s ON u.UserID = s.UserID
                        WHERE u.Username = ? AND u.Role = 'Student' AND u.Status = 'Active'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $storedPassword = $user['Password'];
    // Use a helper function to check password and migrate if needed
    $passwordValid = verifyAndMigratePassword($conn, $user['UserID'], $password, $storedPassword);
    
    if ($passwordValid) {
        // Store all their info in the session
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_type'] = 'student';
        $_SESSION['username'] = $user['Username'];
        $_SESSION['student_id'] = $user['StudentID'];
        $_SESSION['first_name'] = $user['FirstName'];
        $_SESSION['last_name'] = $user['LastName'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['phone'] = $user['PhoneNumber'];
        $_SESSION['faculty'] = $user['Faculty'];
        $_SESSION['course'] = $user['Course'];
        $_SESSION['course'] = $user['Course'];
        $_SESSION['year_of_study'] = $user['YearOfStudy'];
        
        // Check for default password
        if (password_verify('Changeme123!', $storedPassword)) {
            $_SESSION['force_password_change'] = true;
            $basePath = getBasePath();
            $redirectUrl = $basePath . '/Settings/first-login-update.php';
            echo json_encode([
                'success' => true, 
                'message' => 'First login - Update required',
                'redirect' => $redirectUrl
            ]);
            exit();
        }
        
        $basePath = getBasePath();
        $redirectUrl = $basePath . '/Dashboards/student-dashboard.php';
        error_log('Student login - basePath: ' . $basePath . ', redirectUrl: ' . $redirectUrl);
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'redirect' => $redirectUrl
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}

$stmt->close();
$conn->close();

} catch (Exception $e) {
    error_log('Student login error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
