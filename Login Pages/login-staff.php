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

// Check the users table for staff accounts
$stmt = $conn->prepare("SELECT u.UserID, u.Username, u.Password, u.Role, u.Status, 
                        l.LecturerID, l.StaffNumber, l.Name, l.Department, l.Faculty, l.Role AS LecturerRole
                        FROM users u
                        INNER JOIN lecturer l ON u.UserID = l.UserID
                        WHERE u.Username = ? AND u.Status = 'Active'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $storedPassword = $user['Password'];
    $passwordValid = verifyAndMigratePassword($conn, $user['UserID'], $password, $storedPassword);
    
    if ($passwordValid) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Determine if user is admin
        $isAdmin = (isset($user['LecturerRole']) && $user['LecturerRole'] === 'Admin') ||
                   (isset($user['Role']) && $user['Role'] === 'Admin');

        // Store all their info in the session (include DB primary key)
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['LecturerID'] = $user['LecturerID'];
        $_SESSION['user_type'] = $isAdmin ? 'admin' : 'staff';
        $_SESSION['username'] = $user['Username'];
        $_SESSION['staff_number'] = $user['StaffNumber'];
        $_SESSION['name'] = $user['Name'];
        $_SESSION['department'] = $user['Department'];
        $_SESSION['faculty'] = $user['Faculty'];
        $_SESSION['role'] = $user['LecturerRole'];
        $_SESSION['role'] = $user['LecturerRole'];
        $_SESSION['user_role'] = $user['Role'];
        
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
        
        // Route to admin dashboard if admin, else staff dashboard
        if ($isAdmin) {
            $redirectUrl = $basePath . '/Dashboards/Admin/admin-dashboard.php';
        } else {
            $redirectUrl = $basePath . '/Dashboards/staff-dashboard.php';
        }
        
        error_log('Staff login - basePath: ' . $basePath . ', role: ' . $user['LecturerRole'] . ', redirectUrl: ' . $redirectUrl);
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
    error_log('Staff login error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
