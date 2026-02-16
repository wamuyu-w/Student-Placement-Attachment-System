<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

if (!$studentId) {
    header("Location: ../Login Pages/login-student.php");
    exit();
}

// Check if student has an existing application
$existingAppStmt = $conn->prepare("SELECT * FROM attachmentapplication WHERE StudentID = ? ORDER BY ApplicationDate DESC");
$existingAppStmt->bind_param("i", $studentId);
$existingAppStmt->execute();
$applications = $existingAppStmt->get_result();
$existingAppStmt->close();

$hasPendingOrApproved = false;
$applicationsList = [];
while ($row = $applications->fetch_assoc()) {
    $applicationsList[] = $row;
    if ($row['ApplicationStatus'] === 'Pending' || $row['ApplicationStatus'] === 'Approved') {
        $hasPendingOrApproved = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - CUEA</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .split-layout {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
        }
        
        @media (max-width: 1024px) {
            .split-layout {
                grid-template-columns: 1fr;
            }
        }

        .application-card {
            background: var(--bg-white);
            border-radius: var(--border-radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .section-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Form Styles */
        .register-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            font-size: 0.95rem;
            width: 100%;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1);
        }

        .full-width {
            grid-column: 1 / -1;
        }

        /* History Table */
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th {
            text-align: left;
            padding: 12px;
            background: var(--bg-lighter);
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border-color);
        }

        .history-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius-md);
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--primary-dark);
        }

        .btn-disabled {
            background-color: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }
    </style>
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
            <a href="student-applications.php" class="nav-item active">
                <i class="fas fa-file-alt"></i>
                <span>My Applications</span>
            </a>
            <a href="../Logbook/student-logbook.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="../Reports/student-reports.php" class="nav-item">
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
            <h1 class="page-title">Attachment Session Applications</h1>
            <div class="header-actions">
                <div class="user-profile">
                     <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="split-layout">
            <!-- Left Column: Application Actions -->
            <div class="layout-col">
                <!-- Apply Section -->
                <div class="application-card mb-4">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Apply for Attachment Session</h2>
                            <p class="section-desc">Request clearance from the university to proceed with your industrial attachment.</p>
                        </div>
                    </div>
                    
                    <?php if ($hasPendingOrApproved): ?>
                        <button class="btn-submit btn-disabled" disabled title="You already have an active application" style="width: 100%; justify-content: center;">
                            <i class="fas fa-check-circle"></i> Application Submitted
                        </button>
                    <?php else: ?>
                        <form action="process-apply-session.php" method="POST">
                            <div class="form-group mb-3">
                                <label class="form-label">Intended Host Organization (Optional)</label>
                                <input type="text" name="intended_host" class="form-control" placeholder="E.g. Safaricom PLC, KRA, etc.">
                                <small style="color: var(--text-secondary); font-size: 0.8rem;">If the organization is new, a default account will be created for them.</small>
                            </div>
                            
                            <!-- Contact Details for New Org -->
                            <div class="form-group mb-3">
                                <label class="form-label">Contact Person Name</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="E.g. John Doe (HR Manager)">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Contact Person Email</label>
                                <input type="email" name="contact_email" class="form-control" placeholder="E.g. john.doe@company.com">
                                <small style="color: var(--text-secondary); font-size: 0.8rem;">This email will be used to send login credentials for first time access</small>
                            </div>
                            <button type="submit" class="btn-submit" style="width: 100%; justify-content: center; margin-top: 16px;">
                                <i class="fas fa-paper-plane"></i> Submit Application
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Registration Section -->
                <?php
                // Check if student has an APPROVED application but NO active attachment
                $hasApproved = false;
                foreach($applicationsList as $app) {
                    if ($app['ApplicationStatus'] === 'Approved') {
                        $hasApproved = true;
                        break;
                    }
                }

                // Check for active attachment
                $hasActiveAttachment = false;
                $attStmt = $conn->prepare("SELECT AttachmentID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing'");
                $attStmt->bind_param("i", $studentId);
                $attStmt->execute();
                if ($attStmt->get_result()->num_rows > 0) {
                    $hasActiveAttachment = true;
                }
                $attStmt->close();

                if ($hasApproved && !$hasActiveAttachment): 
                    // Fetch Host Orgs for dropdown
                    $hosts = $conn->query("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY OrganizationName");
                ?>
                <div class="application-card mt-4">
                    <div class="section-header">
                        <div>
                            <h3 class="section-title"><i class="fas fa-briefcase"></i> Register Your Placement</h3>
                            <p class="section-desc">Your application is approved! Please register the organization where you have secured your attachment.</p>
                        </div>
                    </div>
                    
                    <form action="process-register-attachment.php" method="POST" class="register-form">
                        <div class="form-group full-width">
                            <label class="form-label">Host Organization</label>
                            <select name="host_org_id" required class="form-control">
                                <option value="">-- Select Organization --</option>
                                <?php while($host = $hosts->fetch_assoc()): ?>
                                    <option value="<?php echo $host['HostOrgID']; ?>">
                                        <?php echo htmlspecialchars($host['OrganizationName']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">Can't find your org? Contact Admin.</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" required class="form-control">
                        </div>
                        <div class="form-group full-width" style="margin-top: 8px;">
                            <button type="submit" class="btn-submit" style="background-color: #10b981;">
                                <i class="fas fa-save"></i> Register Placement
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: History -->
            <div class="layout-col">
                <div class="application-card">
                    <div class="section-header">
                        <h3 class="section-title">Application History</h3>
                    </div>
                    
                    <?php if (count($applicationsList) > 0): ?>
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applicationsList as $app): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($app['ApplicationDate'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($app['ApplicationStatus']); ?>">
                                                <?php echo htmlspecialchars($app['ApplicationStatus']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($app['ApplicationStatus'] === 'Rejected'): ?>
                                                <span style="color: var(--danger-color); font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($app['RejectionReason'] ?? 'N/A'); ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--text-secondary);">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state" style="text-align: center; padding: 32px 0;">
                            <i class="fas fa-file-signature" style="font-size: 32px; color: var(--text-secondary); margin-bottom: 12px;"></i>
                            <p style="color: var(--text-secondary);">No applications found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php 
$conn->close(); 
?>
