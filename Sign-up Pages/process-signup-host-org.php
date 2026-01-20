<?php
require_once '../config.php';

// Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

// Extract and sanitize input
$organizationName = sanitizeInput($data['organizationName'] ?? '');
$contactPerson = sanitizeInput($data['contactPerson'] ?? '');
$email = sanitizeInput($data['email'] ?? '');
$phoneNumber = sanitizeInput($data['phoneNumber'] ?? '');
$physicalAddress = sanitizeInput($data['physicalAddress'] ?? '');
$username = sanitizeInput($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$confirmPassword = trim($data['confirmPassword'] ?? '');

// Validation
$errors = [];

if (empty($organizationName)) {
    $errors['organizationName'] = 'Organization name is required';
}

if (empty($contactPerson)) {
    $errors['contactPerson'] = 'Contact person name is required';
}

if (empty($email)) {
    $errors['email'] = 'Email address is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address';
}

if (empty($phoneNumber)) {
    $errors['phoneNumber'] = 'Phone number is required';
}

if (empty($physicalAddress)) {
    $errors['physicalAddress'] = 'Physical address is required';
}

if (empty($username) || strlen($username) < 3) {
    $errors['username'] = 'Username must be at least 3 characters long';
}

if (empty($password) || strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters long';
}

if ($password !== $confirmPassword) {
    $errors['confirmPassword'] = 'Passwords do not match';
}

// If validation errors exist, return them
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit();
}

$conn = getDBConnection();

// Check if username already exists
$stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists', 'errors' => ['username' => 'This username is already taken']]);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Check if email already exists in hostorganization table
$stmt = $conn->prepare("SELECT HostOrgID FROM hostorganization WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered', 'errors' => ['email' => 'This email is already registered']]);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Hash the password
$hashedPassword = hashPassword($password);

// Start transaction
$conn->begin_transaction();

try {
    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
    $stmt->bind_param("ss", $username, $hashedPassword);
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting user: " . $stmt->error);
    }
    
    $userID = $conn->insert_id;
    $stmt->close();

    // Insert into hostorganization table with foreign key
    $stmt = $conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email, PhoneNumber, PhysicalAddress) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $userID, $organizationName, $contactPerson, $email, $phoneNumber, $physicalAddress);
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting host organization: " . $stmt->error);
    }
    
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Host organization registered successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
