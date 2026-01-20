<?php
/**
 * Quick tool to see what format passwords are stored in
 * Helps you figure out if you still have plain text passwords floating around
 * 
 * Run it from command line: php diagnose-passwords.php
 * Or via browser
 */

require_once '../config.php';

// Uncomment this if you want to lock it down to staff only
// requireLogin('staff');

echo "Password Format Diagnostic Tool\n";
echo "==============================\n\n";

$conn = getDBConnection();

// Check the Users table
echo "Checking Users table...\n";
echo str_repeat("-", 50) . "\n";

$result = $conn->query("SELECT UserID, Username, Password, Role, Status FROM Users LIMIT 20");
if ($result) {
    $hashedCount = 0;
    $plainTextCount = 0;
    $emptyCount = 0;
    
    while ($row = $result->fetch_assoc()) {
        $password = $row['Password'];
        $isHashed = preg_match('/^\$2[ayb]\$.{56}$/', $password);
        
        if (empty($password)) {
            $emptyCount++;
            echo "  [EMPTY] UserID: {$row['UserID']}, Username: {$row['Username']}, Role: {$row['Role']}\n";
        } elseif ($isHashed) {
            $hashedCount++;
            echo "  [HASHED] UserID: {$row['UserID']}, Username: {$row['Username']}, Role: {$row['Role']}\n";
        } else {
            $plainTextCount++;
            $passwordPreview = strlen($password) > 20 ? substr($password, 0, 20) . '...' : $password;
            echo "  [PLAIN TEXT] UserID: {$row['UserID']}, Username: {$row['Username']}, Role: {$row['Role']}, Password: {$passwordPreview}\n";
        }
    }
    
    echo "\nSummary:\n";
    echo "  Hashed passwords: $hashedCount\n";
    echo "  Plain text passwords: $plainTextCount\n";
    echo "  Empty passwords: $emptyCount\n";
    
    $result->close();
} else {
    echo "Error querying Users table: " . $conn->error . "\n";
}

// If they passed a username, check that specific user
if (isset($argv[1])) {
    $username = $argv[1];
    echo "\n\nChecking specific user: $username\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $conn->prepare("SELECT UserID, Username, Password, Role, Status FROM Users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $password = $row['Password'];
        $isHashed = preg_match('/^\$2[ayb]\$.{56}$/', $password);
        
        echo "UserID: {$row['UserID']}\n";
        echo "Username: {$row['Username']}\n";
        echo "Role: {$row['Role']}\n";
        echo "Status: {$row['Status']}\n";
        echo "Password Format: " . ($isHashed ? "HASHED (bcrypt)" : "PLAIN TEXT") . "\n";
        if (!$isHashed && !empty($password)) {
            echo "Password Value: " . (strlen($password) > 50 ? substr($password, 0, 50) . '...' : $password) . "\n";
        }
    } else {
        echo "User not found: $username\n";
    }
    
    $stmt->close();
}

echo "\n==============================\n";
echo "Diagnostic complete!\n";
echo "\nIf you see plain text passwords, you should:\n";
echo "1. Run: php utils/hash-passwords.php (if it exists for your schema)\n";
echo "2. Or manually hash passwords using: password_hash(\$password, PASSWORD_DEFAULT)\n";
echo "3. The login scripts now support both formats for backward compatibility\n";

$conn->close();
?>
