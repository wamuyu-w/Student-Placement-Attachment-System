<?php
require_once '../config.php';
requireLogin('host_org');

// Get statistics
$conn = getDBConnection();
$hostOrgId = $_SESSION['host_org_id'] ?? null;

if (!$hostOrgId) {
    header("Location: ../Login Pages/login-host-org.php");
    exit();
}

// Queries to get active placements, student attached and pending logbooks
$statsStmt = $conn->prepare("
    SELECT 
        (SELECT COUNT(*) FROM attachmentopportunity WHERE HostOrgID = ?) as active_placements,
        (SELECT COUNT(*) FROM attachment WHERE HostOrgID = ? AND AttachmentStatus = 'Active') as students_attached,
        (SELECT COUNT(*) FROM attachment a 
         INNER JOIN logbook l ON a.AttachmentID = l.LogbookID 
         WHERE a.HostOrgID = ? AND l.Status = 'Pending') as pending_logbooks
");

$statsStmt->bind_param("iii", $hostOrgId, $hostOrgId, $hostOrgId);
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();
$statsStmt->close();

// Get recent applications
$appsStmt = $conn->prepare("
    SELECT 
        s.FirstName,
        s.LastName,
        s.Course,
        ao.Description as position_applied,
        a.StartDate,
        a.AttachmentStatus as Status
    FROM attachment a
    INNER JOIN attachmentopportunity ao ON a.AttachmentID = ao.OpportunityID
    INNER JOIN student s ON a.StudentID = s.StudentID
    WHERE a.HostOrgID = ?
    ORDER BY a.StartDate DESC
    LIMIT 5
");
$appsStmt->bind_param("i", $hostOrgId);
$appsStmt->execute();
$recentApps = $appsStmt->get_result();
$appsStmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Organization Dashboard - CUEA</title>
    <link rel="stylesheet" href="host-org-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
      <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo" srcset="">
            </div>
            <h1>CUEA Attachment System </h1>
        </div>
        
        <nav class="sidebar-nav">
            <a href="host-org-dashboard.php" class="nav-item active">
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
            <a href="../Reports/host-org-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../Login Pages/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <h1 class="page-title">Host Organization Dashboard</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['organization_name'][0], 0, 1)); ?></div>
                    <div class="profile-info">

                           <!-- Customised for each host organization -->
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['organization_name']); ?></div>
                        <div class="profile-role">Host Organization</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-content">
                    <h3>Active Placements</h3>
                    <p class="card-number"><?php echo $stats['active_placements'] ?? 0; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>New Applications</h3>
                    <p class="card-number">12</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Students on Attachment</h3>
                    <p class="card-number"><?php echo $stats['students_attached'] ?? 0; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Pending Logbook Entries</h3>
                    <p class="card-number"><?php echo $stats['pending_logbooks'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Activity Section -->
            <div class="activity-section">
                <div class="section-header">
                    <h2>Recent Applications</h2>
                    <a href="#" class="view-all-link">View All →</a>
                </div>
                <div class="activity-list">
                    <?php while ($app = $recentApps->fetch_assoc()): ?>
                    <div class="activity-item">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($app['FirstName'] . ' ' . $app['LastName']); ?>&background=8B1538&color=fff&size=128" alt="Avatar" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($app['FirstName'] . ' ' . $app['LastName']); ?></div>
                            <div class="activity-description"><?php echo htmlspecialchars($app['Course']); ?> - <?php echo htmlspecialchars($app['position_applied']); ?></div>
                            <div class="activity-time"><?php echo date('M j, Y', strtotime($app['StartDate'])); ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="quick-actions-section">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <button class="action-btn primary" onclick="handlePostPlacement()">
                        <i class="fas fa-plus"></i>
                        <span>Post New Placement</span>
                    </button>
                    <button class="action-btn" onclick="handleViewApplications()">
                        <i class="fas fa-file-alt"></i>
                        <span>View All Applications</span>
                    </button>
                    <button class="action-btn" onclick="handleManageStudents()">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Manage Students</span>
                    </button>
                    <button class="action-btn" onclick="handleViewReports()">
                        <i class="fas fa-chart-bar"></i>
                        <span>View Reports</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="host-org-dashboard.js"></script>
    <script>
        function handlePostPlacement() {
            //redirect to opportunities page
            window.location.href = 'host-org-opportunities-management.php';
        }
        function handleViewApplications() {
            window.location.href = '../Applications/host-org-applications.php';
        }
        function handleManageStudents() {
            window.location.href = '../Students/host-org-students.php';
        }
        function handleViewReports() {
            window.location.href = '../Reports/host-org-reports.php';
        }
    </script>
</body>
</html>

