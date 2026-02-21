<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

// Get Active Attachment
$attachmentStmt = $conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing' LIMIT 1");
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

    <style>
        .custom-container {
            padding: 40px;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow-y: auto;
        }
        .top-nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .top-nav-title {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .main-heading {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
            padding: 0;
        }
        .sub-heading {
            color: #8B1538;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 10px 0;
        }
        .instruction-text {
            color: #6b7280;
            font-size: 0.95rem;
            max-width: 80%;
            line-height: 1.5;
            margin-bottom: 40px;
            font-weight: 500;
        }
        .upload-section-title {
            color: #8B1538;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .drop-zone {
            background-color: #e5e5e5;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        .drop-zone:hover {
            background-color: #d4d4d4;
        }
        .drop-zone i {
            font-size: 2rem;
            color: #374151;
            margin-bottom: 15px;
        }
        .drop-zone p {
            color: #8B1538;
            font-weight: 600;
            margin: 0;
            font-size: 1rem;
        }
        .upload-hint {
            color: #9ca3af;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 40px;
        }
        .certification-box {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 30px;
        }
        .certification-box input[type="checkbox"] {
            margin-top: 4px;
            width: 18px;
            height: 18px;
            accent-color: #6b21a8;
            cursor: pointer;
        }
        .certification-text {
            color: #111827;
            font-weight: 700;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .submit-btn {
            background-color: #8B1538;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background-color: #6b0f2a;
        }
        .submit-btn:disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
        }
        /* User Profile Pill */
        .user-pill {
            display: flex;
            align-items: center;
            background: #e5e7eb;
            border-radius: 30px;
            padding: 5px 15px 5px 5px;
            gap: 10px;
        }
        .pill-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #8B1538;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .pill-info {
            display: flex;
            flex-direction: column;
        }
        .pill-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
        }
        .pill-role {
            font-size: 0.75rem;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
        }
        .notification-icon {
            background: #f3f4f6;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #111827;
            font-size: 1.2rem;
            margin-right: 15px;
            cursor: pointer;
        }
        .flex-row-end {
            display: flex;
            align-items: center;
        }
    </style>

    <div class="main-content" style="padding: 0; background: #ffffff;">
        <div class="custom-container">
            <div class="top-nav-bar">
                <div class="top-nav-title">Final Report Submission</div>
                <div class="flex-row-end">
                    <!-- Notification Icon -->
                    <div class="notification-icon">
                        <i class="far fa-bell"></i>
                    </div>
                    <!-- User Profile Pill -->
                    <div class="user-pill">
                        <div class="pill-avatar">
                            <?php 
                                $fName = $_SESSION['first_name'] ?? 'S';
                                $lName = $_SESSION['last_name'] ?? '';
                                echo strtoupper(substr($fName, 0, 1) . substr($lName, 0, 1)); 
                            ?>
                        </div>
                        <div class="pill-info">
                            <span class="pill-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                            <span class="pill-role">Student</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-section">
                <div>
                    <h1 class="main-heading">Final Attachment Report</h1>
                    <div class="sub-heading">Submission of Final Documents</div>
                    <p class="instruction-text">
                        Submission of the final Industrial Attachment Report is a mandatory requirement for the completion of the attachment period. 
                        Please ensure all fields are completed accurately as all submissions are final and cannot be edited without explicit request from the Industrial Attachment Coordinator
                    </p>
                </div>
            </div>
            <?php if (!$hasActiveAttachment): ?>
                <div style="text-align: center; padding: 50px; background: #f9fafb; border-radius: 12px;">
                    <i class="fas fa-lock" style="font-size: 3rem; color: #9ca3af; margin-bottom: 15px;"></i>
                    <h3 style="color: #374151; font-size: 1.25rem;">Reports Unavailable</h3>
                    <p style="color: #6b7280;">You need an active attachment to submit reports.</p>
                </div>
            <?php else: ?>
                <?php if ($report): ?>
                    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 20px; border-radius: 8px;">
                        <h3 style="color: #065f46; margin-top: 0;"><i class="fas fa-check-circle" style="margin-right: 10px;"></i>Report Submitted Successfully</h3>
                        <p style="color: #047857; margin-bottom: 5px;">Submitted on: <strong><?php echo date('M d, Y', strtotime($report['SubmissionDate'])); ?></strong></p>
                        <p style="color: #047857; margin: 0;">Status: <strong><?php echo htmlspecialchars($report['Status']); ?></strong></p>
                    </div>
                <?php else: ?>
                    <form action="process-upload-report.php" method="POST" enctype="multipart/form-data" id="reportForm">
                        <input type="hidden" name="attachment_id" value="<?php echo $attachment['AttachmentID']; ?>">
                        
                        <div class="upload-section-title">Upload your final Attachment Report</div>
                        
                        <div class="drop-zone" id="dropZone" onclick="document.getElementById('reportFile').click()">
                            <i class="fas fa-upload"></i>
                            <p id="dropText">Only documents below 10MB will be allowed to be uploaded</p>
                            <input type="file" name="report_file" id="reportFile" accept=".pdf" style="display: none;" onchange="updateFileName(this)">
                        </div>
                        <div class="upload-hint">
                            Final Attachment Report (Format: PDF only, naming convention: StudentID_Report.pdf)
                        </div>

                        <div class="certification-box">
                            <input type="checkbox" id="certification" required onchange="toggleSubmitBtn()">
                            <label for="certification" class="certification-text">
                                I hereby certify that all logbook entries are complete and the information provided in this report is accurate.
                            </label>
                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn" disabled>SUBMIT REPORT</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Drag and Drop functionality
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('reportFile');
        const dropText = document.getElementById('dropText');
        const submitBtn = document.getElementById('submitBtn');
        const certCheck = document.getElementById('certification');

        if(dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.style.backgroundColor = '#d4d4d4', false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.style.backgroundColor = '#e5e5e5', false);
            });

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                let dt = e.dataTransfer;
                let files = dt.files;
                if(files.length > 0) {
                    fileInput.files = files;
                    updateFileName(fileInput);
                }
            }
        }

        function updateFileName(input) {
            if (input.files && input.files[0]) {
                dropText.textContent = "Selected file: " + input.files[0].name;
                dropText.style.color = "#111827";
            } else {
                dropText.textContent = "Only documents below 10MB will be allowed to be uploaded";
                dropText.style.color = "#8B1538";
            }
        }

        function toggleSubmitBtn() {
            if (certCheck.checked) {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'true');
            }
        }
    </script>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>
