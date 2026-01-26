<?php
require_once '../../config.php';
requireLogin('admin');
$conn = getDBConnection();

// Fetch supervisors
$sql = "SELECT l.Name, l.Department, l.Faculty, u.Status 
        FROM lecturer l 
        JOIN users u ON l.UserID = u.UserID 
        WHERE l.Role = 'Supervisor'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supervisors - Admin</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../../assets/css/theme.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    
    <!-- Admin Dashboard Styles -->
    <link rel="stylesheet" href="admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admin-dashboard.php" class="nav-item">
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
            <a href="../Supervisor/admin-supervisors.php" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Supervisors</span>
            </a>
            <a href="../Students/admin-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Reports/admin-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="#" class="nav-item">
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
            <h1 class="page-title">Manage Supervisors</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search supervisors..." id="searchInput">
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

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Faculty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Department']); ?></td>
                                <td><?php echo htmlspecialchars($row['Faculty']); ?></td>
                                <td>
                                    <?php 
                                        $statusClass = strtolower($row['Status']) == 'active' ? 'status-active' : 'status-pending';
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['Status']); ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No supervisors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
