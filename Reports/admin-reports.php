<?php
require_once '../config.php';
requireLogin('admin');

$conn = getDBConnection();

// Fetch reports with student and attachment details
$sql = "SELECT 
            fr.ReportID, 
            fr.SubmissionDate, 
            fr.ReportFile, 
            s.FirstName, 
            s.LastName, 
            s.AdmissionNumber, -- Assuming this column exists or we use Username
            s.Course,
            a.ClearanceStatus,
            a.AttachmentStatus
        FROM finalreport fr
        JOIN attachment a ON fr.AttachmentID = a.AttachmentID
        JOIN student s ON a.StudentID = s.StudentID
        ORDER BY fr.SubmissionDate DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    
    <!-- Admin Dashboard Styles -->
    <link rel="stylesheet" href="../Dashboards/Admin/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-cleared { background-color: #D1FAE5; color: #059669; }
        .status-pending { background-color: #FEF3C7; color: #D97706; }
        .btn-download {
            background-color: #3B82F6;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
        }
        .btn-download:hover {
            background-color: #2563EB;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="../Dashboards/Admin/admin-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Applications/admin-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="../Opportunities/admin-opportunities-management.php" class="nav-item">
                <i class="fas fa-lightbulb"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Supervisor/admin-supervisors.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Supervisors</span>
            </a>
            <a href="../Students/admin-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="admin-reports.php" class="nav-item active">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
        <div class="sidebar-footer">
             <a href="../Settings/admin-settings.php" class="nav-item">
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
            <h1 class="page-title">Submitted Final Reports</h1>
        </header>
        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <?php if ($result && $result->num_rows > 0): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                                <th style="padding: 12px;">Student Name</th>
                                <th style="padding: 12px;">Course</th>
                                <th style="padding: 12px;">Submission Date</th>
                                <th style="padding: 12px;">Attachment Status</th>
                                <th style="padding: 12px;">Clearance</th>
                                <th style="padding: 12px;">Report</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 500;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></div>
                                    </td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars(date('M d, Y', strtotime($row['SubmissionDate']))); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['AttachmentStatus']); ?></td>
                                    <td style="padding: 12px;">
                                        <?php if ($row['ClearanceStatus'] === 'Cleared'): ?>
                                            <span class="status-badge status-cleared">Cleared</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending"><?php echo htmlspecialchars($row['ClearanceStatus'] ?? 'Pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <a href="../assets/uploads/reports/<?php echo htmlspecialchars($row['ReportFile']); ?>" class="btn-download" download>
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-file-invoice" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e1;"></i>
                        <p>No final reports submitted yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
