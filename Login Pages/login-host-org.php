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

// Check the users table for host org accounts
$stmt = $conn->prepare("SELECT u.UserID, u.Username, u.Password, u.Role, u.Status
                        FROM users u
                        WHERE u.Username = ? AND u.Role = 'Host Organization' AND u.Status = 'Active'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $storedPassword = $user['Password'];
    $passwordValid = verifyAndMigratePassword($conn, $user['UserID'], $password, $storedPassword);
    
        if ($passwordValid) {
            // Check for default password
            if ($password === 'Changeme123!') {
                 $_SESSION['force_password_change'] = true;
                 $redirectUrl = $basePath . '../Settings/first-login-update.php';
            } else {
                 $redirectUrl = $basePath . '../Dashboards/host-org-dashboard.php';
            }

            // Fetch organization details using the foreign key relationship (UserID)
            $orgStmt = $conn->prepare("SELECT HostOrgID, OrganizationName, ContactPerson, Email, PhoneNumber, PhysicalAddress
                                       FROM hostorganization
                                       WHERE UserID = ?");
            $orgStmt->bind_param("i", $user['UserID']);
            $orgStmt->execute();
            $orgResult = $orgStmt->get_result();
            
            if ($orgResult->num_rows > 0) {
                $org = $orgResult->fetch_assoc();
                
                // Store all their info in the session
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['user_type'] = 'host_org';
                $_SESSION['username'] = $user['Username'];
                $_SESSION['host_org_id'] = $org['HostOrgID'];
                $_SESSION['organization_name'] = $org['OrganizationName'];
                $_SESSION['email'] = $org['Email'];
                $_SESSION['contact_person'] = $org['ContactPerson'];
                $_SESSION['phone_number'] = $org['PhoneNumber'];
                $_SESSION['physical_address'] = $org['PhysicalAddress'];
                
                $orgStmt->close();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful',
                    'redirect' => $redirectUrl
                ]);
        } else {
            // No organization record found for this user ID
            echo json_encode(['success' => false, 'message' => 'Organization record not found']);
            $orgStmt->close();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}

$stmt->close();
$conn->close();

} catch (Exception $e) {
    error_log('Host org login error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
