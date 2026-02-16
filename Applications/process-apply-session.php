<?php
require_once '../config.php';
requireLogin('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student-applications.php");
    exit();
}

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

if (!$studentId) {
    header("Location: ../Login Pages/login-student.php");
    exit();
}

// Double check if student has an existing pending or approved application
$checkStmt = $conn->prepare("SELECT ApplicationID FROM attachmentapplication WHERE StudentID = ? AND (ApplicationStatus = 'Pending' OR ApplicationStatus = 'Approved')");
$checkStmt->bind_param("i", $studentId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    // Already has active application
    header("Location: student-applications.php?error=already_applied");
    exit();
}
$checkStmt->close();

// Check/Create Host Organization
$hostOrgId = null;
$intendedHost = isset($_POST['intended_host']) ? trim($_POST['intended_host']) : null;
$contactPerson = isset($_POST['contact_person']) ? trim($_POST['contact_person']) : null;
$contactEmail = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : null;

if ($intendedHost) {
    // Start Transaction
    $conn->begin_transaction();

    try {
        // 1. Check if Host Org already exists
        $hostStmt = $conn->prepare("SELECT HostOrgID FROM hostorganization WHERE OrganizationName = ?");
        $hostStmt->bind_param("s", $intendedHost);
        $hostStmt->execute();
        $hostResult = $hostStmt->get_result();

        if ($hostResult->num_rows > 0) {
            $hostRow = $hostResult->fetch_assoc();
            $hostOrgId = $hostRow['HostOrgID'];
        } else {
            // 2. Create New Host Organization User
            
            // Validate Contact Info for new Org
            if (empty($contactPerson) || empty($contactEmail)) {
                throw new Exception("Contact Person and Email are required for new organizations.");
            }
            if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid contact email format.");
            }

            // Generate Username (Hxxx)
            $userStmt = $conn->query("SELECT Username FROM users WHERE Role = 'Host Organization' ORDER BY UserID DESC LIMIT 1");
            $lastUsername = "H000";
            if ($userStmt->num_rows > 0) {
                $lastUser = $userStmt->fetch_assoc();
                $lastUsername = $lastUser['Username'];
            }
            // Extract number and increment
            $num = intval(substr($lastUsername, 1));
            $newUsername = 'H' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
            $rawPassword = 'Changeme123!';
            $defaultPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

            // Insert User
            $insertUser = $conn->prepare("INSERT INTO users (Username, Password, Role, Status) VALUES (?, ?, 'Host Organization', 'Active')");
            $insertUser->bind_param("ss", $newUsername, $defaultPassword);
            if (!$insertUser->execute()) {
                throw new Exception("Failed to create user account");
            }
            $newUserId = $conn->insert_id;
            $insertUser->close();

            // Insert Host Org
            $insertHost = $conn->prepare("INSERT INTO hostorganization (UserID, OrganizationName, ContactPerson, Email) VALUES (?, ?, ?, ?)");
            $insertHost->bind_param("isss", $newUserId, $intendedHost, $contactPerson, $contactEmail);
            if (!$insertHost->execute()) {
                throw new Exception("Failed to create host organization profile");
            }
            $hostOrgId = $conn->insert_id;
            $insertHost->close();

            // Send Email to Contact Person
            $subject = "CUEA Attachment System - Partner Account Created";
            $message = "Dear $contactPerson,\n\n";
            $message .= "An account has been created for $intendedHost on the CUEA Student Placement & Attachment System.\n\n";
            $message .= "This will allow you to manage student applications and logbooks.\n\n";
            $message .= "Login Credentials:\n";
            $message .= "URL: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'], 2) . "/Login Pages/host-organization-login.php\n";
            $message .= "Username: $newUsername\n";
            $message .= "Password: $rawPassword\n\n";
            $message .= "Please log in and update your password immediately.\n\n";
            $message .= "Regards,\nCUEA Attachment Office";
            $headers = "From: no-reply@cuea.edu";

            // Attempt to send email (suppress errors if local mail server not configured)
            @mail($contactEmail, $subject, $message, $headers);
        }
        $hostStmt->close();

        // 3. Create Application
        $stmt = $conn->prepare("INSERT INTO attachmentapplication (StudentID, ApplicationDate, ApplicationStatus, IntendedHostOrg, HostOrgID) VALUES (?, CURDATE(), 'Pending', ?, ?)");
        $stmt->bind_param("isi", $studentId, $intendedHost, $hostOrgId);

        if (!$stmt->execute()) {
            throw new Exception("Failed to submit application");
        }
        $stmt->close();

        $conn->commit();
        header("Location: student-applications.php?success=applied");

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: student-applications.php?error=db_error&message=" . urlencode($e->getMessage()));
    }
} else {
    // Handle case where no host is provided? Though field is optional in UI, logic implies it might be needed.
    // Use fallback without HostID if allowed, or error. Assuming optional means standard flow.
    // For now, if optional, we just insert as before but IntendedHost is null.
    // Actually, user said "Intended Host" is the trigger.
    
    $stmt = $conn->prepare("INSERT INTO attachmentapplication (StudentID, ApplicationDate, ApplicationStatus) VALUES (?, CURDATE(), 'Pending')");
    $stmt->bind_param("i", $studentId);
    
    if ($stmt->execute()) {
        header("Location: student-applications.php?success=applied");
    } else {
        header("Location: student-applications.php?error=db_error");
    }
    $stmt->close();
}

$conn->close();
?>
