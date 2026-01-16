<?php
/**
 * Utility script to hash existing plain text passwords in the database
 * 
 * WARNING: This script will update all passwords in the database.
 * Only run this once if you have plain text passwords that need to be hashed.
 * 
 * Usage: Run this script from command line or via browser (remove after use for security)
 * php hash-passwords.php
 */

require_once '../config.php';

// Security check - uncomment this line to require authentication
// requireLogin('staff'); // Only allow staff to run this

echo "Password Hashing Utility\n";
echo "=======================\n\n";

$conn = getDBConnection();
$updated = 0;
$skipped = 0;

// Hash passwords in students table
echo "Processing students table...\n";
$result = $conn->query("SELECT id, username, password FROM students");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Check if password is already hashed (bcrypt hashes start with $2y$)
        if (!preg_match('/^\$2[ayb]\$.{56}$/', $row['password'])) {
            // Password appears to be plain text, hash it
            $hashedPassword = hashPassword($row['password']);
            $stmt = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $row['id']);
            if ($stmt->execute()) {
                echo "  ✓ Hashed password for student: {$row['username']}\n";
                $updated++;
            }
            $stmt->close();
        } else {
            echo "  - Skipped (already hashed): {$row['username']}\n";
            $skipped++;
        }
    }
    $result->close();
}

// Hash passwords in staff table
echo "\nProcessing staff table...\n";
$result = $conn->query("SELECT id, username, password FROM staff");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Check if password is already hashed
        if (!preg_match('/^\$2[ayb]\$.{56}$/', $row['password'])) {
            $hashedPassword = hashPassword($row['password']);
            $stmt = $conn->prepare("UPDATE staff SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $row['id']);
            if ($stmt->execute()) {
                echo "  ✓ Hashed password for staff: {$row['username']}\n";
                $updated++;
            }
            $stmt->close();
        } else {
            echo "  - Skipped (already hashed): {$row['username']}\n";
            $skipped++;
        }
    }
    $result->close();
}

// Hash passwords in host_organizations table
echo "\nProcessing host_organizations table...\n";
$result = $conn->query("SELECT id, username, password FROM host_organizations");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Check if password is already hashed
        if (!preg_match('/^\$2[ayb]\$.{56}$/', $row['password'])) {
            $hashedPassword = hashPassword($row['password']);
            $stmt = $conn->prepare("UPDATE host_organizations SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $row['id']);
            if ($stmt->execute()) {
                echo "  ✓ Hashed password for organization: {$row['username']}\n";
                $updated++;
            }
            $stmt->close();
        } else {
            echo "  - Skipped (already hashed): {$row['username']}\n";
            $skipped++;
        }
    }
    $result->close();
}

echo "\n=======================\n";
echo "Summary:\n";
echo "  Updated: $updated passwords\n";
echo "  Skipped: $skipped passwords (already hashed)\n";
echo "\nDone!\n";

$conn->close();
?>
