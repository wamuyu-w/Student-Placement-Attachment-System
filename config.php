
<?php
// DB config 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'attachmentmanagementsystem');

// DB Connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo "Connection Failed";
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// establish a sesion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Returns the user to the login page if not logged in
function requireLogin($userType) {
    if (!isLoggedIn() || $_SESSION['user_type'] !== $userType) {
        // Figure out which login page to send them to
        $loginPages = [
            'student' => 'student-login.php',
            'staff' => 'staff-login.php',
            'admin' => 'staff-login.php', // Admins are also in the staff login page
            'host_org' => 'login-host-org.php'
        ];
        $loginPage = $loginPages[$userType] ?? 'student-login.php';
        header("Location: ../Login Pages/" . $loginPage);
        exit();
    }
}

// Cleaning up user input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Hash passwords with bcrypt
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verifies password and migrates legacy plain text passwords to bcrypt
function verifyAndMigratePassword($conn, $userId, $password, $storedPassword) {

    // Bcrypt hash grep pattern
    if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPassword)) {

        // If hashed, verify normally
        return password_verify($password, $storedPassword);
    } else {
        // if plain text - compare directly then hash and update
        $passwordValid = ($password === $storedPassword);

        // If it matches, hash it now so next time it's secure
        if ($passwordValid) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
            $updateStmt->bind_param("si", $hashedPassword, $userId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        return $passwordValid;
    }
}
// Gets the base path for redirects - needed because we have files in different folders
function getBasePath() {
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = str_replace('\\', '/', $scriptPath);
    
    // If we're inside "Login Pages" folder, go up one level to get the root
    if (strpos($basePath, '/Login Pages') !== false) {
        $basePath = dirname($basePath);
    }
    
    // Make sure it starts with a slash for absolute paths
    if (substr($basePath, 0, 1) !== '/') {
        $basePath = '/' . $basePath;
    }
    
    // Clean up any trailing slashes
    $basePath = rtrim($basePath, '/');
    
    return $basePath;
}
?>
