<?php
require_once '../config.php';
requireLogin('staff');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Staff</title>
    <link rel="stylesheet" href="../Dashboards/staff-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="../Dashboards/staff-dashboard.php" class="nav-item">
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
            <a href="staff-reports.php" class="nav-item active">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
             <a href="../Settings/staff-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        
            <a href="../Supervisor/staff-supervision.php" class="nav-item">
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
            <h1 class="page-title">Reports</h1>
             <div class="header-actions">
                 <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['name'][0] ?? 'S', 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Staff'); ?></div>
                        <div class="profile-role">Lecturer</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <p>Reports Interface - Under Construction</p>
                <p style="margin-top: 10px; color: #666;">View assessment statistics and student progress reports here.</p>
            </div>
        </div>
    </div>
</body>
</html>
