<?php
//setup from config, operations restricted to admin operations
require_once '../../config.php';
requireLogin('admin');

// Helper function to calculate time ago
function getTimeAgo($datetime) {
    if (!$datetime) return 'Unknown';
    
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return $diff . 's ago';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . 'm ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . 'h ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . 'd ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

// Fetch dashboard statistics
$conn = getDBConnection();

// Get pending applications count
$pendingAppsQuery = "SELECT COUNT(*) as count FROM attachmentapplication WHERE ApplicationStatus = 'Pending'";
$pendingAppsResult = $conn->query($pendingAppsQuery);
$pendingApps = $pendingAppsResult->fetch_assoc()['count'] ?? 0;

// Get active placements count
$activePlacementsQuery = "SELECT COUNT(*) as count FROM attachment WHERE AttachmentStatus = 'Active'";
$activePlacementsResult = $conn->query($activePlacementsQuery);
$activePlacements = $activePlacementsResult->fetch_assoc()['count'] ?? 0;

// Get available opportunities count
$opportunitiesQuery = "SELECT COUNT(*) as count FROM attachmentopportunity WHERE Status = 'Active'";
$opportunitiesResult = $conn->query($opportunitiesQuery);
$opportunities = $opportunitiesResult->fetch_assoc()['count'] ?? 0;

// Get unassigned students count (students without active attachments)
$unassignedQuery = "SELECT COUNT(DISTINCT s.StudentID) as count 
                    FROM student s 
                    LEFT JOIN attachment a ON s.StudentID = a.StudentID AND a.AttachmentStatus = 'Active'
                    WHERE a.AttachmentID IS NULL AND s.EligibilityStatus = 'Eligible'";
$unassignedResult = $conn->query($unassignedQuery);
$unassignedStudents = $unassignedResult->fetch_assoc()['count'] ?? 0;

// Fetch recent activities
$activities = [];
try {
    // Get recent applications
    $appsQuery = "SELECT aa.ApplicationID, s.FirstName, s.LastName, aa.ApplicationDate, aa.ApplicationStatus
                  FROM attachmentapplication aa
                  JOIN student s ON aa.StudentID = s.StudentID
                  ORDER BY aa.ApplicationDate DESC
                  LIMIT 4";
    
    $result = $conn->query($appsQuery);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $name = $row['FirstName'] . ' ' . $row['LastName'];
            $status = $row['ApplicationStatus'] ?? 'Pending';
            $timeAgo = getTimeAgo($row['ApplicationDate']);
            
            $activities[] = [
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=8B1538&color=fff&size=128',
                'title' => 'New Application from ' . htmlspecialchars($name),
                'description' => 'Status: ' . htmlspecialchars($status),
                'time' => $timeAgo
            ];
        }
    }
    
    // If we don't have enough activities, add some from opportunities
    if (count($activities) < 4) {
        $oppQuery = "SELECT ao.OpportunityID, ho.OrganizationName, ao.Description, ao.ApplicationStartDate
                     FROM attachmentopportunity ao
                     JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
                     WHERE ao.Status = 'Active'
                     ORDER BY ao.ApplicationStartDate DESC
                     LIMIT " . (4 - count($activities));
        
        $oppResult = $conn->query($oppQuery);
        
        if ($oppResult && $oppResult->num_rows > 0) {
            while ($row = $oppResult->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['OrganizationName']) . '&background=10b981&color=fff&size=128',
                    'title' => htmlspecialchars($row['OrganizationName']) . ' posted a new opportunity',
                    'description' => htmlspecialchars(substr($row['Description'], 0, 50) . '...'),
                    'time' => getTimeAgo($row['ApplicationStartDate'])
                ];
            }
        }
    }
    
    // Add completed placements if available
    if (count($activities) < 4) {
        $placementQuery = "SELECT a.AttachmentID, s.FirstName, s.LastName, ho.OrganizationName, a.EndDate
                            FROM attachment a
                            JOIN student s ON a.StudentID = s.StudentID
                            JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
                            WHERE a.AttachmentStatus = 'Completed'
                            ORDER BY a.EndDate DESC
                            LIMIT " . (4 - count($activities));
        
        $placementResult = $conn->query($placementQuery);
        
        if ($placementResult && $placementResult->num_rows > 0) {
            while ($row = $placementResult->fetch_assoc()) {
                $name = $row['FirstName'] . ' ' . $row['LastName'];
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=3b82f6&color=fff&size=128',
                    'title' => htmlspecialchars($name) . ' completed placement',
                    'description' => 'At ' . htmlspecialchars($row['OrganizationName']),
                    'time' => getTimeAgo($row['EndDate'])
                ];
            }
        }
    }
    
    // Add supervisor assignments if available
    if (count($activities) < 4) {
        $supervisionQuery = "SELECT su.SupervisionID, s.FirstName, s.LastName, l.Name as LecturerName
                             FROM supervision su
                             JOIN attachment a ON su.AttachmentID = a.AttachmentID
                             JOIN student s ON a.StudentID = s.StudentID
                             JOIN lecturer l ON su.LecturerID = l.LecturerID
                             ORDER BY su.SupervisionID DESC
                             LIMIT " . (4 - count($activities));
        
        $supervisionResult = $conn->query($supervisionQuery);
        
        if ($supervisionResult && $supervisionResult->num_rows > 0) {
            while ($row = $supervisionResult->fetch_assoc()) {
                $name = $row['FirstName'] . ' ' . $row['LastName'];
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=f59e0b&color=fff&size=128',
                    'title' => 'Supervisor assigned to ' . htmlspecialchars($name),
                    'description' => htmlspecialchars($row['LecturerName']),
                    'time' => '1d ago'
                ];
            }
        }
    }
    
    // Limit to 4 most recent
    $activities = array_slice($activities, 0, 4);
    
} catch (Exception $e) {
    // If error, use default activities
    $activities = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard - CUEA</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../../assets/css/theme.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    
    <link rel="stylesheet" href="admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo" srcset="">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admin-dashboard.php" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../../Applications/admin-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="../../Opportunities/admin-opportunities-management.php" class="nav-item">
                <i class="fas fa-lightbulb"></i>
                <span>Opportunities</span>
            </a>
            <a href="../../Supervisor/admin-supervisors.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Supervisors</span>
            </a>
            <a href="../../Students/admin-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../../Reports/admin-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="../../Login Pages/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <h1 class="page-title">Administrator Dashboard</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name'] ?? 'Admin'); ?>&background=8B1538&color=fff&size=128" alt="Profile" class="profile-img">
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></div>
                        <div class="profile-role">Coordinator</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-content">
                    <h3>Pending Applications</h3>
                    <p class="card-number"><?php echo $pendingApps; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Active Placements</h3>
                    <p class="card-number"><?php echo $activePlacements; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Available Opportunities</h3>
                    <p class="card-number"><?php echo $opportunities; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Unassigned Students</h3>
                    <p class="card-number"><?php echo $unassignedStudents; ?></p>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Activity -->
            <div class="activity-section">
                <div class="section-header">
                    <h2>Recent Activity</h2>
                    <a href="#" class="view-all-link">View All</a>
                </div>
                <div class="activity-list" id="activityList">
                    <?php if (empty($activities)): ?>
                        <!-- Default activities if database is empty -->
                        <div class="activity-item">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=8B1538&color=fff&size=128" alt="John Doe" class="activity-avatar">
                            <div class="activity-content">
                                <div class="activity-title">New Application from John Doe</div>
                                <div class="activity-description">Applied to Tech Solutions Inc.</div>
                                <div class="activity-time">2m ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <img src="https://ui-avatars.com/api/?name=Innovate+Corp&background=10b981&color=fff&size=128" alt="Innovate Corp" class="activity-avatar">
                            <div class="activity-content">
                                <div class="activity-title">Innovate Corp posted a new opportunity</div>
                                <div class="activity-description">Software Engineering Intern</div>
                                <div class="activity-time">1h ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <img src="https://ui-avatars.com/api/?name=Emily+White&background=3b82f6&color=fff&size=128" alt="Emily White" class="activity-avatar">
                            <div class="activity-content">
                                <div class="activity-title">Emily White completed placement</div>
                                <div class="activity-description">At Data Dynamics Ltd.</div>
                                <div class="activity-time">3h ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <img src="https://ui-avatars.com/api/?name=Michael+Brown&background=f59e0b&color=fff&size=128" alt="Michael Brown" class="activity-avatar">
                            <div class="activity-content">
                                <div class="activity-title">Supervisor assigned to Michael Brown</div>
                                <div class="activity-description">Dr. Alan Grant</div>
                                <div class="activity-time">1d ago</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                            <div class="activity-item">
                                <img src="<?php echo htmlspecialchars($activity['avatar']); ?>" alt="Activity" class="activity-avatar">
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo $activity['title']; ?></div>
                                    <div class="activity-description"><?php echo $activity['description']; ?></div>
                                    <div class="activity-time"><?php echo $activity['time']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-section">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <button class="action-btn primary" onclick="window.location.href='../../Opportunities/admin-opportunities-management.php'">
                        <i class="fas fa-plus"></i>
                        <span>Add New Opportunity</span>
                    </button>
                    <button class="action-btn" onclick="window.location.href='../../Reports/admin-reports.php'">
                        <i class="fas fa-file-alt"></i>
                        <span>Generate Weekly Report</span>
                    </button>
                    <button class="action-btn" onclick="window.location.href='../../Supervisor/admin-supervisors.php'">
                        <i class="fas fa-users"></i>
                        <span>Assign Supervisor</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="admin-dashboard.js"></script>
</body>
</html>
