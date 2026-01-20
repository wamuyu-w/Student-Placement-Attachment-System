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
$firstName = sanitizeInput($data['firstName'] ?? '');
$lastName = sanitizeInput($data['lastName'] ?? '');
$email = sanitizeInput($data['email'] ?? '');
$phoneNumber = sanitizeInput($data['phoneNumber'] ?? '');
$course = sanitizeInput($data['course'] ?? '');
$faculty = sanitizeInput($data['faculty'] ?? '');
$yearOfStudy = intval($data['yearOfStudy'] ?? 0);
$username = sanitizeInput($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$confirmPassword = trim($data['confirmPassword'] ?? '');

// Validation
$errors = [];

if (empty($firstName)) {
    $errors['firstName'] = 'First name is required';
}

if (empty($lastName)) {
    $errors['lastName'] = 'Last name is required';
}

if (empty($email)) {
    $errors['email'] = 'Email address is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address';
}

if (empty($phoneNumber)) {
    $errors['phoneNumber'] = 'Phone number is required';
}

if (empty($course)) {
    $errors['course'] = 'Course is required';
}

if (empty($faculty)) {
    $errors['faculty'] = 'Faculty is required';
}

if ($yearOfStudy <= 0 || $yearOfStudy > 5) {
    $errors['yearOfStudy'] = 'Please select a valid year of study';
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

// Check if email already exists in student table
$stmt = $conn->prepare("SELECT StudentID FROM student WHERE Email = ?");
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

// Set eligibility status - can be customized based on your business logic
$eligibilityStatus = 'Pending';

// Start transaction
$conn->begin_transaction();

try {
    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Student', 'Active')");
    $stmt->bind_param("ss", $username, $hashedPassword);
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting user: " . $stmt->error);
    }
    
    $userID = $conn->insert_id;
    $stmt->close();

        // Insert into student table (let StudentID auto-increment)
        $stmt = $conn->prepare("INSERT INTO student (UserID, FirstName, LastName, Course, Faculty, YearOfStudy, PhoneNumber, Email, EligibilityStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssisss", $userID, $firstName, $lastName, $course, $faculty, $yearOfStudy, $phoneNumber, $email, $eligibilityStatus);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting student: " . $stmt->error);
        }
        $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Student registered successfully'
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
