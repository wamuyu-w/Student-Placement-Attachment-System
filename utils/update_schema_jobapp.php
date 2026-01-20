<?php
require_once __DIR__ . '/../config.php';

$conn = getDBConnection();

echo "Checking jobapplication table schema...\n";

// Check if columns exist
$checkQuery = "SHOW COLUMNS FROM jobapplication LIKE 'ResumePath'";
$result = $conn->query($checkQuery);

if ($result->num_rows == 0) {
    echo "Adding ResumePath column...\n";
    $sql = "ALTER TABLE jobapplication ADD COLUMN ResumePath VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "ResumePath added successfully.\n";
    } else {
        echo "Error adding ResumePath: " . $conn->error . "\n";
    }
} else {
    echo "ResumePath already exists.\n";
}

$checkQuery = "SHOW COLUMNS FROM jobapplication LIKE 'ResumeLink'";
$result = $conn->query($checkQuery);

if ($result->num_rows == 0) {
    echo "Adding ResumeLink column...\n";
    $sql = "ALTER TABLE jobapplication ADD COLUMN ResumeLink VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "ResumeLink added successfully.\n";
    } else {
        echo "Error adding ResumeLink: " . $conn->error . "\n";
    }
} else {
    echo "ResumeLink already exists.\n";
}

$checkQuery = "SHOW COLUMNS FROM jobapplication LIKE 'Motivation'";
$result = $conn->query($checkQuery);

if ($result->num_rows == 0) {
    echo "Adding Motivation column...\n";
    $sql = "ALTER TABLE jobapplication ADD COLUMN Motivation TEXT DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Motivation added successfully.\n";
    } else {
        echo "Error adding Motivation: " . $conn->error . "\n";
    }
} else {
    echo "Motivation already exists.\n";
}

$conn->close();
echo "Schema update completed.\n";
?>
