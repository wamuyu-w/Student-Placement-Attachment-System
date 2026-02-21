<?php
require_once '../config.php';
requireLogin('admin');

$conn = getDBConnection();

// 1. Fetch Final Reports
$finalReportsSql = "SELECT 
            fr.ReportID, fr.SubmissionDate, fr.ReportFile, 
            s.FirstName, s.LastName, u.Username as AdmissionNumber, s.Course,
            a.ClearanceStatus, a.AttachmentStatus
        FROM finalreport fr
        JOIN attachment a ON fr.AttachmentID = a.AttachmentID
        JOIN student s ON a.StudentID = s.StudentID
        JOIN users u ON s.UserID = u.UserID
        ORDER BY fr.SubmissionDate DESC";
$finalReportsResult = $conn->query($finalReportsSql);

// 2. Completion Stats
$completionSql = "SELECT 
            u.Username as AdmissionNumber, s.FirstName, s.LastName, s.Course, 
            o.OrganizationName, a.AttachmentStatus, a.ClearanceStatus
        FROM attachment a
        JOIN student s ON a.StudentID = s.StudentID
        JOIN users u ON s.UserID = u.UserID
        LEFT JOIN hostorganization o ON a.HostOrgID = o.HostOrgID
        ORDER BY s.FirstName";
$completionResult = $conn->query($completionSql);

// 3. Assessment Grades
$assessmentSql = "SELECT 
            u.Username as AdmissionNumber, s.FirstName, s.LastName, 
            COUNT(asm.AssessmentID) as AssessmentsTaken,
            AVG(asm.Marks) as AverageMarks,
            MAX(asm.AssessmentDate) as LastAssessmentDate
        FROM assessment asm
        JOIN attachment a ON asm.AttachmentID = a.AttachmentID
        JOIN student s ON a.StudentID = s.StudentID
        JOIN users u ON s.UserID = u.UserID
        GROUP BY a.AttachmentID, u.Username, s.FirstName, s.LastName
        ORDER BY s.FirstName ASC";
$assessmentResult = $conn->query($assessmentSql);

// 4. Printable Logbooks (Students with entries)
$logbooksSql = "SELECT DISTINCT 
            s.StudentID, u.Username as AdmissionNumber, s.FirstName, s.LastName, s.Course
        FROM logbook lb
        JOIN attachment a ON lb.AttachmentID = a.AttachmentID
        JOIN student s ON a.StudentID = s.StudentID
        JOIN users u ON s.UserID = u.UserID
        ORDER BY s.FirstName";
$logbooksResult = $conn->query($logbooksSql);

// 5. Supervisor Stats
$supervisorSql = "SELECT 
            l.Name, l.Department, 
            COUNT(DISTINCT asm.AssessmentID) as AssessmentsConducted, 
            COUNT(DISTINCT a.StudentID) as StudentsAssessed
        FROM lecturer l
        LEFT JOIN assessment asm ON l.LecturerID = asm.LecturerID
        LEFT JOIN attachment a ON a.AttachmentID = asm.AttachmentID
        WHERE l.Role = 'Supervisor'
        GROUP BY l.LecturerID
        ORDER BY l.Name";
$supervisorResult = $conn->query($supervisorSql);

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
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-cleared { background-color: #D1FAE5; color: #059669; }
        .status-ongoing { background-color: #DBEAFE; color: #1E3A8A; }
        .status-pending { background-color: #FEF3C7; color: #D97706; }
        .btn-download {
            background-color: #3B82F6;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85em;
            display: inline-block;
        }
        .btn-download:hover { background-color: #2563EB; }
        
        /* Tabs Styling */
        .tabs {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
            gap: 20px;
        }
        .tab-button {
            background: none;
            border: none;
            padding: 10px 15px;
            font-size: 1rem;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .tab-button:hover { color: #8B1538; }
        .tab-button.active {
            color: #8B1538;
            border-bottom-color: #8B1538;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table th { padding: 12px; text-align: left; background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 600; }
        .report-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .empty-state-card { text-align: center; padding: 40px; color: #64748b; }
        .empty-state-card i { font-size: 48px; margin-bottom: 16px; color: #cbd5e1; }
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
            <h1 class="page-title">System & Administrative Reports</h1>
        </header>

        <div class="tabs">
            <button class="tab-button active" onclick="openTab('tab-final-reports')"><i class="fas fa-file-pdf"></i> Final Reports</button>
            <button class="tab-button" onclick="openTab('tab-completion')"><i class="fas fa-certificate"></i> Completions</button>
            <button class="tab-button" onclick="openTab('tab-grades')"><i class="fas fa-star"></i> Assessment Grades</button>
            <button class="tab-button" onclick="openTab('tab-logbooks')"><i class="fas fa-book"></i> Print Logbooks</button>
            <button class="tab-button" onclick="openTab('tab-supervisors')"><i class="fas fa-chalkboard-teacher"></i> Supervisor Stats</button>
        </div>

        <div class="content-grid" style="grid-template-columns: 1fr;">
            
            <!-- Tab 1: Final Reports -->
            <div id="tab-final-reports" class="tab-content active bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">Submitted Final Reports</h2>
                <?php if ($finalReportsResult && $finalReportsResult->num_rows > 0): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Submission Date</th>
                                <th>Attachment Status</th>
                                <th>Clearance</th>
                                <th>Report File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $finalReportsResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></div>
                                        <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($row['AdmissionNumber']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['Course']); ?></td>
                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($row['SubmissionDate']))); ?></td>
                                    <td><?php echo htmlspecialchars($row['AttachmentStatus']); ?></td>
                                    <td>
                                        <?php if ($row['ClearanceStatus'] === 'Cleared'): ?>
                                            <span class="status-badge status-cleared">Cleared</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending"><?php echo htmlspecialchars($row['ClearanceStatus'] ?? 'Pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="../assets/uploads/reports/<?php echo htmlspecialchars($row['ReportFile']); ?>" class="btn-download" download>
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state-card">
                        <i class="fas fa-folder-open"></i>
                        <p>No final reports submitted yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 2: Completion Stats -->
            <div id="tab-completion" class="tab-content bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Student Placement Completions</h2>
                    <a href="print-completion.php" target="_blank" class="btn-download" style="background-color: #0f172a;"><i class="fas fa-print"></i> Print Report</a>
                </div>
                <?php if ($completionResult && $completionResult->num_rows > 0): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Host Organization</th>
                                <th>Attachment Status</th>
                                <th>Clearance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $completionResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></div>
                                        <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($row['AdmissionNumber'] . ' • ' . $row['Course']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['OrganizationName'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $stat = $row['AttachmentStatus'];
                                            $cls = $stat==='Completed' ? 'status-cleared' : ($stat==='Ongoing' ? 'status-ongoing' : 'status-pending');
                                        ?>
                                        <span class="status-badge <?php echo $cls; ?>"><?php echo htmlspecialchars($stat); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['ClearanceStatus'] === 'Cleared'): ?>
                                            <span class="status-badge status-cleared">Cleared</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending"><?php echo htmlspecialchars($row['ClearanceStatus'] ?? 'Pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state-card">
                        <i class="fas fa-certificate"></i>
                        <p>No attachment records found.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 3: Assessment Grades -->
            <div id="tab-grades" class="tab-content bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Master Assessment Grades</h2>
                    <a href="print-grades.php" target="_blank" class="btn-download" style="background-color: #0f172a;"><i class="fas fa-print"></i> Print Report</a>
                </div>
                <?php if ($assessmentResult && $assessmentResult->num_rows > 0): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Assessments Taken</th>
                                <th>Final Grade (Average)</th>
                                <th>Last Assessment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $assessmentResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></div>
                                        <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($row['AdmissionNumber']); ?></div>
                                    </td>
                                    <td><?php echo $row['AssessmentsTaken']; ?></td>
                                    <td><strong style="color: #8B1538; font-size: 1.1em;"><?php echo number_format($row['AverageMarks'], 1); ?>%</strong></td>
                                    <td><?php echo date('M d, Y', strtotime($row['LastAssessmentDate'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state-card">
                        <i class="fas fa-clipboard-check"></i>
                        <p>No assessments have been graded yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 4: Printable Logbooks -->
            <div id="tab-logbooks" class="tab-content bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">Student Logbook Exports</h2>
                <p style="color: #64748b; margin-bottom: 20px;">Use the Print action to generate a printer-friendly layout of a student's entire logbook history.</p>
                <?php if ($logbooksResult && $logbooksResult->num_rows > 0): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Admission No.</th>
                                <th>Course</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $logbooksResult->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                    <td><?php echo htmlspecialchars($row['AdmissionNumber']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Course']); ?></td>
                                    <td>
                                        <a href="print-logbook.php?student_id=<?php echo $row['StudentID']; ?>" target="_blank" class="btn-download" style="background-color: #0f172a;">
                                            <i class="fas fa-print"></i> View Print Option
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state-card">
                        <i class="fas fa-book-open"></i>
                        <p>No logbook entries exist in the system yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab 5: Supervisor Stats -->
            <div id="tab-supervisors" class="tab-content bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Supervisor Assessment Statistics</h2>
                    <a href="print-supervisors.php" target="_blank" class="btn-download" style="background-color: #0f172a;"><i class="fas fa-print"></i> Print Report</a>
                </div>
                <?php if ($supervisorResult && $supervisorResult->num_rows > 0): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Supervisor Name</th>
                                <th>Department</th>
                                <th>Total Assessments Conducted</th>
                                <th>Unique Students Assessed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $supervisorResult->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight: 500; color: #0f172a;"><i class="fas fa-user-tie" style="color: #94a3b8; margin-right: 8px;"></i> <?php echo htmlspecialchars($row['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Department']); ?></td>
                                    <td><span class="status-badge" style="background: #f1f5f9; color: #0f172a; font-size: 1em; padding: 6px 12px;"><?php echo $row['AssessmentsConducted']; ?></span></td>
                                    <td><?php echo $row['StudentsAssessed']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state-card">
                        <i class="fas fa-users-slash"></i>
                        <p>No supervisors or assessment metrics found.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
        function openTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            // Deactivate all buttons
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            
            // Show new content
            document.getElementById(tabId).classList.add('active');
            // Show new button as active
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
