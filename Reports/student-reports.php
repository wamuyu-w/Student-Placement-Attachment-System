<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

// Get Active Attachment
$attachmentStmt = $conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Active' LIMIT 1");
$attachmentStmt->bind_param("i", $studentId);
$attachmentStmt->execute();
$attachmentResult = $attachmentStmt->get_result();
$hasActiveAttachment = $attachmentResult->num_rows > 0;
$attachment = $hasActiveAttachment ? $attachmentResult->fetch_assoc() : null;

$report = null;
if ($hasActiveAttachment) {
    $reportStmt = $conn->prepare("SELECT * FROM finalreport WHERE AttachmentID = ?");
    $reportStmt->bind_param("i", $attachment['AttachmentID']);
    $reportStmt->execute();
    $reportResult = $reportStmt->get_result();
    $report = $reportResult->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CUEA Attachment</title>
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        <nav class="sidebar-nav">
             <a href="../Dashboards/student-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Opportunities/student-opportunities.php" class="nav-item">
                <i class="fas fa-briefcase"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Applications/student-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>My Applications</span>
            </a>
            <a href="../Logbook/student-logbook.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="student-reports.php" class="nav-item active">
                <i class="fas fa-file-pdf"></i>
                <span>Reports</span>
            </a>
            <a href="../Supervisor/student-supervisor.php" class="nav-item">
                <i class="fas fa-user-tie"></i>
                <span>Supervisor</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../Settings/student-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="../Login Pages/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <header class="main-header">
            <h1 class="page-title">Final Report</h1>
            <div class="header-actions">
                <div class="user-profile">
                     <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-grid">
            <?php if (!$hasActiveAttachment): ?>
                <div class="empty-state text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-lock text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Reports Unavailable</h3>
                    <p class="text-gray-500 mb-6">You need an active attachment to submit reports.</p>
                </div>
            <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h2 class="text-xl font-bold mb-4">Final Attachment Report</h2>
                    <p class="text-gray-600 mb-6">At the end of your industrial attachment, you are required to submit a comprehensive report detailing your experience.</p>
                    
                    <?php if ($report): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-green-800">Report Submitted</h4>
                                    <p class="text-sm text-green-700">Submitted on <?php echo date('M d, Y', strtotime($report['SubmissionDate'])); ?></p>
                                    <p class="text-sm text-green-700">Status: <strong><?php echo htmlspecialchars($report['Status']); ?></strong></p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="upload-section border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <h3 class="font-medium text-gray-900 mb-2">Upload your Final Report</h3>
                            <p class="text-sm text-gray-500 mb-4">PDF or DOCX (Max 10MB)</p>
                            
                            <form action="process-upload-report.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="attachment_id" value="<?php echo $attachment['AttachmentID']; ?>">
                                <input type="file" name="report_file" id="reportFile" class="hidden" style="display: none;" onchange="this.form.submit()">
                                <label for="reportFile" class="btn-primary" style="background-color: #8B1538; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer; display: inline-block;">
                                    Select File
                                </label>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>
