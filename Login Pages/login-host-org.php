<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit();
}

$conn = getDBConnection();

// Prepare and execute query
$stmt = $conn->prepare("SELECT id, username, password, organization_name, email, contact_person, phone FROM host_organizations WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = 'host_org';
        $_SESSION['username'] = $user['username'];
        $_SESSION['organization_name'] = $user['organization_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['contact_person'] = $user['contact_person'];
        $_SESSION['phone'] = $user['phone'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'redirect' => '../Dashboards/host-org-dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}

$stmt->close();
$conn->close();
?>
