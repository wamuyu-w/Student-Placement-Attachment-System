<?php
require_once '../config.php';
requireLogin('host_org');

$conn = getDBConnection();
$hostOrgId = $_SESSION['host_org_id'] ?? null;

if (!$hostOrgId) {
    header("Location: ../Login Pages/login-host-org.php");
    exit();
}

// Fetch students currently on attachment at this host org
$stmt = $conn->prepare("
    SELECT a.AttachmentID, s.FirstName, s.LastName, s.Course, a.StartDate, a.EndDate, a.AttachmentStatus, a.AssessmentCode
    FROM attachment a
    JOIN student s ON a.StudentID = s.StudentID
    WHERE a.HostOrgID = ?
    ORDER BY a.StartDate DESC
");
$stmt->bind_param("i", $hostOrgId);
$stmt->execute();
$placements = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervision - Host Organization</title>
    <link rel="stylesheet" href="../Dashboards/host-org-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .code-display {
            background-color: #f3f4f6;
            padding: 6px 12px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 1.1em;
            letter-spacing: 2px;
            font-weight: bold;
            color: #111827;
        }
        .btn-generate {
            background-color: #8B1538;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-generate:hover {
            background-color: #6b0f2a;
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
            <a href="../Dashboards/host-org-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Opportunities/host-management-opportunities.php" class="nav-item">
                <i class="fas fa-briefcase"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Applications/host-org-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="../Students/host-org-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Logbook/host-org-logbook.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="../Reports/host-org-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="host-org-supervision.php" class="nav-item active">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Supervision</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="../Login Pages/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <div class="main-content">
        <header class="main-header">
            <h1 class="page-title">Supervision & Assessment Codes</h1>
             <div class="header-actions">
                 <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['organization_name'][0] ?? 'H', 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['organization_name'] ?? 'Host Organization'); ?></div>
                        <div class="profile-role">Host Organization</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="card">
            <h2>Generate Assessment Codes</h2>
            <p style="color: #4b5563;">Generate a unique code for the university supervisor to start an assessment for each student placed at your organization.</p>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.375rem; margin-top: 1rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                    Action Successful.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.375rem; margin-top: 1rem; margin-bottom: 1rem; border: 1px solid #fecaca;">
                    An error occurred.
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Placement Dates</th>
                        <th>Assessment Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($placements->num_rows > 0): ?>
                        <?php while ($row = $placements->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                <td><?php echo htmlspecialchars($row['Course']); ?></td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($row['StartDate'])); ?> - 
                                    <?php echo date('M j, Y', strtotime($row['EndDate'])); ?>
                                </td>
                                <td>
                                    <?php if ($row['AssessmentCode']): ?>
                                        <span class="code-display"><?php echo htmlspecialchars($row['AssessmentCode']); ?></span>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-style: italic;">Not generated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="process-generate-code.php" method="POST">
                                        <input type="hidden" name="attachment_id" value="<?php echo $row['AttachmentID']; ?>">
                                        <button type="submit" class="btn-generate">
                                            <?php echo $row['AssessmentCode'] ? 'Regenerate Code' : 'Generate Code'; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280; padding: 20px;">No students are currently placed at your organization.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
