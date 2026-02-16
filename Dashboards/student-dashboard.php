<?php
require_once '../config.php';
requireLogin('student');

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
$studentID = $_SESSION['student_id'] ?? null;

if (!$studentID) {
    header("Location: ../Login Pages/login-student.php");
    exit();
}

// Get student's applications count
$myApplicationsQuery = "SELECT COUNT(*) as count FROM attachmentapplication WHERE StudentID = ?";
$stmt = $conn->prepare($myApplicationsQuery);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$myApplicationsResult = $stmt->get_result();
$myApplications = $myApplicationsResult->fetch_assoc()['count'] ?? 0;
$stmt->close();

// Get active placement count (should be 0 or 1)
$activePlacementQuery = "SELECT COUNT(*) as count FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Active'";
$stmt = $conn->prepare($activePlacementQuery);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$activePlacementResult = $stmt->get_result();
$activePlacement = $activePlacementResult->fetch_assoc()['count'] ?? 0;
$stmt->close();

// Get available opportunities count (active opportunities where application period hasn't ended)
$availableOppsQuery = "SELECT COUNT(*) as count 
                        FROM attachmentopportunity 
                        WHERE Status = 'Active' 
                        AND ApplicationEndDate >= CURDATE()
                        AND ApplicationStartDate <= CURDATE()";
$availableOppsResult = $conn->query($availableOppsQuery);
$availableOpportunities = $availableOppsResult->fetch_assoc()['count'] ?? 0;

// Get pending tasks count (logbook entries pending, reports due, etc.)
$pendingTasksQuery = "SELECT 
                        (SELECT COUNT(*) FROM logbook lb 
                         JOIN attachment a ON lb.AttachmentID = a.AttachmentID 
                         WHERE a.StudentID = ? AND lb.Status = 'Pending') +
                        (SELECT COUNT(*) FROM finalreport fr 
                         JOIN attachment a ON fr.AttachmentID = a.AttachmentID 
                         WHERE a.StudentID = ? AND fr.Status = 'Pending') as count";
$stmt = $conn->prepare($pendingTasksQuery);
$stmt->bind_param("ii", $studentID, $studentID);
$stmt->execute();
$pendingTasksResult = $stmt->get_result();
$pendingTasks = $pendingTasksResult->fetch_assoc()['count'] ?? 0;
$stmt->close();

// Fetch recent activities
$activities = [];
try {
    // Get student's recent applications
    $appsQuery = "SELECT aa.ApplicationID, aa.ApplicationDate, aa.ApplicationStatus
                  FROM attachmentapplication aa
                  WHERE aa.StudentID = ?
                  ORDER BY aa.ApplicationDate DESC
                  LIMIT 3";
    
    $stmt = $conn->prepare($appsQuery);
    $stmt->bind_param("i", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $status = $row['ApplicationStatus'] ?? 'Pending';
            
            $activities[] = [
                'avatar' => 'https://ui-avatars.com/api/?name=Application&background=8B1538&color=fff&size=128',
                'title' => 'Application submitted',
                'description' => 'Status: ' . htmlspecialchars($status),
                'time' => getTimeAgo($row['ApplicationDate'])
            ];
        }
    }
    $stmt->close();
    
    // Get placement updates if available
    if (count($activities) < 4) {
        $placementQuery = "SELECT a.AttachmentID, ho.OrganizationName, a.StartDate, a.AttachmentStatus
                            FROM attachment a
                            JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
                            WHERE a.StudentID = ?
                            ORDER BY a.StartDate DESC
                            LIMIT " . (4 - count($activities));
        
        $stmt = $conn->prepare($placementQuery);
        $stmt->bind_param("i", $studentID);
        $stmt->execute();
        $placementResult = $stmt->get_result();
        
        if ($placementResult && $placementResult->num_rows > 0) {
            while ($row = $placementResult->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['OrganizationName']) . '&background=10b981&color=fff&size=128',
                    'title' => 'Placement at ' . htmlspecialchars($row['OrganizationName']),
                    'description' => 'Status: ' . htmlspecialchars($row['AttachmentStatus']),
                    'time' => getTimeAgo($row['StartDate'])
                ];
            }
        }
        $stmt->close();
    }
    
    // Get supervisor assignments if available
    if (count($activities) < 4) {
        $supervisionQuery = "SELECT l.Name as LecturerName, a.StartDate
                             FROM supervision su
                             JOIN attachment a ON su.AttachmentID = a.AttachmentID
                             JOIN lecturer l ON su.LecturerID = l.LecturerID
                             WHERE a.StudentID = ?
                             ORDER BY a.StartDate DESC
                             LIMIT " . (4 - count($activities));
        
        $stmt = $conn->prepare($supervisionQuery);
        $stmt->bind_param("i", $studentID);
        $stmt->execute();
        $supervisionResult = $stmt->get_result();
        
        if ($supervisionResult && $supervisionResult->num_rows > 0) {
            while ($row = $supervisionResult->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['LecturerName']) . '&background=f59e0b&color=fff&size=128',
                    'title' => 'Supervisor assigned',
                    'description' => htmlspecialchars($row['LecturerName']),
                    'time' => getTimeAgo($row['StartDate'])
                ];
            }
        }
        $stmt->close();
    }
    
    // Get logbook updates if available
    if (count($activities) < 4) {
        $logbookQuery = "SELECT lb.LogbookID, lb.IssueDate, a.AttachmentID
                         FROM logbook lb
                         JOIN attachment a ON lb.AttachmentID = a.AttachmentID
                         WHERE a.StudentID = ?
                         ORDER BY lb.IssueDate DESC
                         LIMIT " . (4 - count($activities));
        
        $stmt = $conn->prepare($logbookQuery);
        $stmt->bind_param("i", $studentID);
        $stmt->execute();
        $logbookResult = $stmt->get_result();
        
        if ($logbookResult && $logbookResult->num_rows > 0) {
            while ($row = $logbookResult->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=Logbook&background=3b82f6&color=fff&size=128',
                    'title' => 'Logbook issued',
                    'description' => 'Start documenting your activities',
                    'time' => getTimeAgo($row['IssueDate'])
                ];
            }
        }
        $stmt->close();
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
    <title>Student Dashboard - CUEA Placements</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="student-dashboard.css">

</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo" srcset="">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="student-dashboard.php" class="nav-item active">
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <h1 class="page-title">Student Dashboard</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge"><?php echo $pendingTasks > 0 ? $pendingTasks : ''; ?></span>
                </div>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['first_name'] ?? 'Student'); ?>&background=8B1538&color=fff&size=128" alt="Profile" class="profile-img">
                      <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
                        <div class="profile-role">Student</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-content">
                    <h3>My Applications</h3>
                    <p class="card-number"><?php echo $myApplications; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Active Placement</h3>
                    <p class="card-number"><?php echo $activePlacement; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Available Opportunities</h3>
                    <p class="card-number"><?php echo $availableOpportunities; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Pending Tasks</h3>
                    <p class="card-number"><?php echo $pendingTasks; ?></p>
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
                            <img src="https://ui-avatars.com/api/?name=Welcome&background=8B1538&color=fff&size=128" alt="Welcome" class="activity-avatar">
                            <div class="activity-content">
                                <div class="activity-title">Welcome to your dashboard</div>
                                <div class="activity-description">Start by browsing available opportunities</div>
                                <div class="activity-time">Just now</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <img src="https://ui-avatars.com/api/?name=Opportunities&background=10b981&color=fff&size=128" alt="Opportunities" class="activity-avatar">
                            <div class="activity-content">
                                <div class="activity-title">Browse opportunities</div>
                                <div class="activity-description">Find placement opportunities that match your course</div>
                                <div class="activity-time">Get started</div>
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
                    <button class="action-btn primary" onclick="window.location.href='../Opportunities/student-opportunities.php'">
                        <i class="fas fa-search"></i>
                        <span>Browse Opportunities</span>
                    </button>
                    <button class="action-btn" onclick="window.location.href='../Applications/student-applications.php'">
                        <i class="fas fa-file-alt"></i>
                        <span>View My Applications</span>
                    </button>
                    <button class="action-btn" onclick="window.location.href='../Logbook/student-logbook.php'">
                        <i class="fas fa-book"></i>
                        <span>View Logbook</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="student-dashboard.js"></script>
    <script src="../assets/js/dashboard-updates.js"></script>
</body>
</html>
