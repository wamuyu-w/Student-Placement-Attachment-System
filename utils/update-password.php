<?php
/**
 * Quick script to change a single user's password
 * Handy for password resets or setting up new accounts
 * 
 * Usage: php update-password.php <table> <username> <new_password>
 * Example: php update-password.php students student1 newpassword123
 */

require_once '../config.php';

// Grab the command line args
$table = $argv[1] ?? '';
$username = $argv[2] ?? '';
$newPassword = $argv[3] ?? '';

// Make sure they gave us everything we need
if (empty($table) || empty($username) || empty($newPassword)) {
    echo "Usage: php update-password.php <table> <username> <new_password>\n";
    echo "Tables: students, staff, host_organizations\n";
    echo "Example: php update-password.php students student1 newpassword123\n";
    exit(1);
}

// Check that the table name is valid (security thing)
$validTables = ['students', 'staff', 'host_organizations'];
if (!in_array($table, $validTables)) {
    echo "Error: Invalid table name. Must be one of: " . implode(', ', $validTables) . "\n";
    exit(1);
}

// Password needs to be at least 6 characters
if (strlen($newPassword) < 6) {
    echo "Error: Password must be at least 6 characters long.\n";
    exit(1);
}

$conn = getDBConnection();

// See if the user actually exists
$stmt = $conn->prepare("SELECT id FROM $table WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Error: User '$username' not found in table '$table'.\n";
    $stmt->close();
    $conn->close();
    exit(1);
}

$stmt->close();

// Hash it up
$hashedPassword = hashPassword($newPassword);

// Update the password
$stmt = $conn->prepare("UPDATE $table SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $hashedPassword, $username);

if ($stmt->execute()) {
    echo "✓ Password updated successfully for user '$username' in table '$table'.\n";
} else {
    echo "Error: Failed to update password. " . $conn->error . "\n";
    exit(1);
}

$stmt->close();
$conn->close();
?>
