<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../../config.php';

// Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated and has permission
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: ../../Login Pages/login.php');
    exit();
}

// Only admin and host_org can add opportunities
if (!in_array($_SESSION['user_type'], ['admin', 'host_org'])) {
    http_response_code(403);
    header('Location: ../admin-dashboard.php');
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../' . ($_SESSION['user_type'] === 'admin' ? 'admin-dashboard.php' : 'host-org-dashboard.php'));
    exit();
}

try {
    $conn = getDBConnection();

    // Sanitize and validate input
    $description = sanitizeInput($_POST['description'] ?? '');
    $eligibility_criteria = sanitizeInput($_POST['eligibility_criteria'] ?? '');
    $application_start_date = $_POST['application_start_date'] ?? '';
    $application_end_date = $_POST['application_end_date'] ?? '';
    $status = sanitizeInput($_POST['status'] ?? 'Active');

    // add this in the future to the database
    $number_of_positions = intval($_POST['number_of_positions'] ?? 1);

    // Validate required fields
    if (empty($description) || empty($eligibility_criteria) || empty($application_start_date) || empty($application_end_date)) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: ../' . ($_SESSION['user_type'] === 'admin' ? 'admin-dashboard.php' : 'host-org-dashboard.php'));
        exit();
    }

    // Validate dates
    $startDate = DateTime::createFromFormat('Y-m-d', $application_start_date);
    $endDate = DateTime::createFromFormat('Y-m-d', $application_end_date);

    if (!$startDate || !$endDate || $startDate >= $endDate) {
        $_SESSION['error'] = 'Application end date must be after start date';
        header('Location: ../' . ($_SESSION['user_type'] === 'admin' ? 'admin-dashboard.php' : 'host-org-dashboard.php'));
        exit();
    }

    // Validate number of positions
    if ($number_of_positions < 1) {
        $_SESSION['error'] = 'Number of positions must be at least 1';
        header('Location: ../' . ($_SESSION['user_type'] === 'admin' ? 'admin-dashboard.php' : 'host-org-dashboard.php'));
        exit();
    }

    // Get HostOrgID
    $hostOrgId = null;

    if ($_SESSION['user_type'] === 'host_org') {

        // Host organization can only add opportunities for their own organization
        $hostOrgId = $_SESSION['host_org_id'] ?? null;
        if (!$hostOrgId) {
            $_SESSION['error'] = 'Organization ID not found in session';
            header('Location: ../host-org-dashboard.php');
            exit();
        }
    } else if ($_SESSION['user_type'] === 'admin') {

        // Admin can add opportunities for any organization - get from request or use a default; they will have to get from the list of host org for this
        $hostOrgId = intval($_POST['host_org_id'] ?? 0);
        if ($hostOrgId === 0) {
            $_SESSION['error'] = 'Organization must be selected';
            header('Location: ../admin-dashboard.php');
            exit();
        }
    }

    // Insert opportunity into database
    $stmt = $conn->prepare("
        INSERT INTO attachmentopportunity 
        (HostOrgID, Description, EligibilityCriteria, ApplicationStartDate, ApplicationEndDate, Status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssss",
        $hostOrgId,
        $description,
        $eligibility_criteria,
        $application_start_date,
        $application_end_date,
        $status
    );

    if ($stmt->execute()) {
        $opportunityId = $conn->insert_id;
        
        // If number of positions is provided, store it as metadata (can be used for limiting applications)
        if ($number_of_positions > 1) {
            error_log("Opportunity created: ID=$opportunityId with $number_of_positions positions");
        }

        $_SESSION['success'] = 'Opportunity added successfully!';
    } else {
        throw new Exception('Error adding opportunity: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log('Error in process-add-opportunity.php: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while adding the opportunity. Please try again.';
}

// Redirect back to dashboard
header('Location: ../' . ($_SESSION['user_type'] === 'admin' ? 'admin-dashboard.php' : 'host-org-dashboard.php'));
exit();
?>
