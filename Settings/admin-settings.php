<?php
require_once '../config.php';
requireLogin('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <link rel="stylesheet" href="../Dashboards/admin-dashboard.css">
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
            <a href="../Dashboards/admin-dashboard.php" class="nav-item">
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
            <a href="../Reports/admin-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
        <div class="sidebar-footer">
             <a href="admin-settings.php" class="nav-item active">
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
            <h1 class="page-title">Settings</h1>
        </header>
        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <p>Settings Interface - Under Construction</p>
            </div>
        </div>
    </div>
</body>
</html>
