<?php
require_once '../config.php';
requireLogin('staff');

// Get statistics
$conn = getDBConnection();

// Prefer using LecturerID stored at login to avoid extra lookups
$staffID = $_SESSION['LecturerID'] ?? null;

if (!$staffID) {
    // Fallback: try to derive from staff_number if present
    $staffNumber = $_SESSION['staff_number'] ?? null;
    if ($staffNumber) {
        $staffStmt = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
        $staffStmt->bind_param("s", $staffNumber);
        $staffStmt->execute();
        $staffResult = $staffStmt->get_result();
        if ($staffResult->num_rows > 0) {
            $staffData = $staffResult->fetch_assoc();
            $staffID = $staffData['LecturerID'];
        }
        $staffStmt->close();
    }

    if (!$staffID) {
        header("Location: ../Login Pages/login-staff.php");
        exit();
    }
}

// Fetch statistics
// Use supervision table to find attachments supervised by this lecturer
$statsStmt = $conn->prepare("
    SELECT
        (SELECT COUNT(*) FROM attachment a
            WHERE a.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
        ) AS monitored_attachments,
        (SELECT COUNT(*) FROM logbook l
            WHERE l.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
            AND l.Status = 'Pending'
        ) AS pending_reviews,
        (SELECT COUNT(DISTINCT a.StudentID) FROM attachment a
            WHERE a.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
        ) AS students_monitored,
        (SELECT COUNT(*) FROM logbook l
            WHERE l.AttachmentID IN (SELECT s.AttachmentID FROM supervision s WHERE s.LecturerID = ?)
        ) AS total_logbooks
");
$statsStmt->bind_param("iiii", $staffID, $staffID, $staffID, $staffID);
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();
$statsStmt->close();

// Get recent logbooks
$logsStmt = $conn->prepare("
    SELECT
        st.FirstName,
        st.LastName,
        st.Course,
        l.IssueDate,
        l.Status
    FROM supervision sv
    INNER JOIN attachment a ON sv.AttachmentID = a.AttachmentID
    INNER JOIN logbook l ON l.AttachmentID = a.AttachmentID
    INNER JOIN student st ON a.StudentID = st.StudentID
    WHERE sv.LecturerID = ?
    ORDER BY l.IssueDate DESC
    LIMIT 5
");
$logsStmt->bind_param("i", $staffID);
$logsStmt->execute();
$recentLogs = $logsStmt->get_result();
$logsStmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - CUEA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="staff-dashboard.css">
</head>
<body>
    <!-- Sidebar -->
      <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo" srcset="">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>

        <nav class="sidebar-nav">
            <a href="staff-dashboard.php" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Students/staff-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Logbook/staff-logbook.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Logbooks</span>
            </a>
            <a href="../Reports/staff-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="../Supervisor/staff-supervision.php" class="nav-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Supervision</span>
            </a>
            <a href="../Settings/staff-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
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
            <h1 class="page-title">Lecturer Dashboard</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['name'][0], 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                        <div class="profile-role"><?php echo htmlspecialchars($_SESSION['role']); ?></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-content">
                    <h3>Monitored Attachments</h3>
                    <p class="card-number"><?php echo $stats['monitored_attachments'] ?? 0; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Pending Reviews</h3>
                    <p class="card-number"><?php echo $stats['pending_reviews'] ?? 0; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Students Monitored</h3>
                    <p class="card-number"><?php echo $stats['students_monitored'] ?? 0; ?></p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-content">
                    <h3>Total Logbooks</h3>
                    <p class="card-number"><?php echo $stats['total_logbooks'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Activity Section -->
            <div class="activity-section">
                <div class="section-header">
                    <h2>Recent Logbook Submissions</h2>
                    <a href="#" class="view-all-link">View All →</a>
                </div>
                <div class="activity-list">
                    <?php while ($log = $recentLogs->fetch_assoc()): ?>
                    <div class="activity-item">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($log['FirstName'] . ' ' . $log['LastName']); ?>&background=8B1538&color=fff&size=128" alt="Avatar" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($log['FirstName'] . ' ' . $log['LastName']); ?></div>
                            <div class="activity-description"><?php echo htmlspecialchars($log['Course']); ?></div>
                            <div class="activity-time"><?php echo date('M j, Y', strtotime($log['IssueDate'])); ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="quick-actions-section">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <button class="action-btn primary" onclick="handleReviewLogbook()">
                        <i class="fas fa-file-check"></i>
                        <span>Review Logbooks</span>
                    </button>
                    <button class="action-btn" onclick="handleViewStudents()">
                        <i class="fas fa-graduation-cap"></i>
                        <span>View Students</span>
                    </button>
                    <button class="action-btn" onclick="handleGenerateReport()">
                        <i class="fas fa-chart-bar"></i>
                        <span>Generate Report</span>
                    </button>
                    <button class="action-btn" onclick="handleViewAttachments()">
                        <i class="fas fa-briefcase"></i>
                        <span>View Attachments</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="staff-dashboard.js"></script>
    <script src="../assets/js/dashboard-updates.js"></script>
  
    <script>
        function handleReviewLogbook() {
            window.location.href = '../Logbook/staff-logbook.php';
        }
        function handleViewStudents() {
            window.location.href = '../Students/staff-students.php';
        }
        function handleGenerateReport() {
            window.location.href = '../Reports/staff-reports.php';
        }
        function handleViewAttachments() {
            // Assuming attachments view overlaps with students view or a separate page
             window.location.href = '../Students/staff-students.php';
        }
    </script>
</body>
</html>
