<?php
/**
 * One-time script to hash all the plain text passwords in the database
 * 
 * WARNING: This will update passwords in your database!
 * Only run this once when migrating from plain text to hashed passwords.
 * 
 * Run it from command line: php hash-passwords.php
 * Or via browser (but delete this file after for security)
 */

require_once '../config.php';

// Uncomment this if you want to lock it down to staff only
// requireLogin('staff');

echo "Password Hashing Utility\n";
echo "=======================\n\n";

$conn = getDBConnection();
$updated = 0;
$skipped = 0;

// Go through students and hash their passwords
echo "Processing students table...\n";
$result = $conn->query("SELECT id, username, password FROM students");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Bcrypt hashes start with $2y$, $2a$, or $2b$
        if (!preg_match('/^\$2[ayb]\$.{56}$/', $row['password'])) {
            // Looks like plain text, hash it
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

// Same thing for staff
echo "\nProcessing staff table...\n";
$result = $conn->query("SELECT id, username, password FROM staff");
if ($result) {
    while ($row = $result->fetch_assoc()) {
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

// And host organizations too
echo "\nProcessing host_organizations table...\n";
$result = $conn->query("SELECT id, username, password FROM host_organizations");
if ($result) {
    while ($row = $result->fetch_assoc()) {
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
