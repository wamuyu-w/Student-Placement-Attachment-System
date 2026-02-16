<?php
require_once '../config.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Default password to check against
    $defaultPassword = 'Changeme123!';
    
    // Validation
    if (empty($newPassword) || empty($confirmPassword)) {
        header("Location: first-login-update.php?error=" . urlencode("All password fields are required"));
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        header("Location: first-login-update.php?error=" . urlencode("Passwords do not match"));
        exit();
    }
    
    if ($newPassword === $defaultPassword) {
        header("Location: first-login-update.php?error=" . urlencode("You cannot use the default password. Please choose a new one."));
        exit();
    }
    
    // Start Transaction
    $conn->begin_transaction();
    
    try {
        // 1. Update Password
        $hashedPassword = hashPassword($newPassword);
        $updateUser = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
        $updateUser->bind_param("si", $hashedPassword, $userId);
        
        if (!$updateUser->execute()) {
            throw new Exception("Error updating password");
        }
        $updateUser->close();
        
        // 2. Update Profile Details if Student
        if ($userType === 'student') {
            $studentId = $_SESSION['student_id'];
            
            // Get all fields
            $firstName = sanitizeInput($_POST['firstName']);
            $lastName = sanitizeInput($_POST['lastName']);
            $email = sanitizeInput($_POST['email']);
            $phone = sanitizeInput($_POST['phone']);
            $course = sanitizeInput($_POST['course']);
            $faculty = sanitizeInput($_POST['faculty']);
            $yearOfStudy = (int)$_POST['yearOfStudy'];
            
            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($course) || empty($faculty) || empty($yearOfStudy)) {
                throw new Exception("All profile fields are required");
            }
            
            $updateStudent = $conn->prepare("UPDATE student SET FirstName=?, LastName=?, Email=?, PhoneNumber=?, Course=?, Faculty=?, YearOfStudy=?, EligibilityStatus='Eligible' WHERE StudentID=?");
            $updateStudent->bind_param("ssssssii", $firstName, $lastName, $email, $phone, $course, $faculty, $yearOfStudy, $studentId);
            
            if (!$updateStudent->execute()) {
                throw new Exception("Error updating profile details: " . $studentStudent->error);
            }
            $updateStudent->close();
            
            // Update Session Data with new info
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['name'] = $firstName . ' ' . $lastName;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['course'] = $course;
            $_SESSION['faculty'] = $faculty;
            $_SESSION['year_of_study'] = $yearOfStudy;
        } else if ($userType === 'host_org') {
            $hostOrgId = $_SESSION['host_org_id'];
            
            $orgName = sanitizeInput($_POST['organization_name']);
            $contactPerson = sanitizeInput($_POST['contact_person']);
            $email = sanitizeInput($_POST['email']);
            $phone = sanitizeInput($_POST['phone_number']);
            $address = sanitizeInput($_POST['physical_address']);
            
            if (empty($orgName) || empty($contactPerson) || empty($email) || empty($phone) || empty($address)) {
                throw new Exception("All profile fields are required");
            }
            
            $updateHost = $conn->prepare("UPDATE hostorganization SET OrganizationName=?, ContactPerson=?, Email=?, PhoneNumber=?, PhysicalAddress=? WHERE HostOrgID=?");
            $updateHost->bind_param("sssssi", $orgName, $contactPerson, $email, $phone, $address, $hostOrgId);
            
            if (!$updateHost->execute()) {
                throw new Exception("Error updating profile details");
            }
            $updateHost->close();
            
            // Update Session Data
            $_SESSION['organization_name'] = $orgName;
            $_SESSION['contact_person'] = $contactPerson;
            $_SESSION['email'] = $email;
            $_SESSION['phone_number'] = $phone;
            $_SESSION['physical_address'] = $address;
        } else if ($userType === 'staff' || $userType === 'admin') {
            $lecturerId = $_SESSION['LecturerID'];
            
            $name = sanitizeInput($_POST['name']);
            $department = sanitizeInput($_POST['department']);
            $faculty = sanitizeInput($_POST['faculty']);
            
            if (empty($name) || empty($department) || empty($faculty)) {
                throw new Exception("All profile fields are required");
            }
            
            $updateLecturer = $conn->prepare("UPDATE lecturer SET Name=?, Department=?, Faculty=? WHERE LecturerID=?");
            $updateLecturer->bind_param("sssi", $name, $department, $faculty, $lecturerId);
            
            if (!$updateLecturer->execute()) {
                throw new Exception("Error updating profile details");
            }
            $updateLecturer->close();
            
            // Update Session Data
            $_SESSION['name'] = $name;
            $_SESSION['department'] = $department;
            $_SESSION['faculty'] = $faculty;
        }
        
        // Commit
        $conn->commit();
        
        // Clear the force flag
        unset($_SESSION['force_password_change']);
        
        // Redirect to Dashboard
        if ($userType === 'student') {
            header("Location: ../Dashboards/student-dashboard.php");
        } else if ($userType === 'admin') {
             header("Location: ../Dashboards/Admin/admin-dashboard.php");
        } else if ($userType === 'host_org') {
             header("Location: ../Dashboards/host-org-dashboard.php");
        } else {
             header("Location: ../Dashboards/staff-dashboard.php");
        }
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: first-login-update.php?error=" . urlencode($e->getMessage()));
        exit();
    }
    
    $conn->close();
} else {
    header("Location: first-login-update.php");
    exit();
}
?>
