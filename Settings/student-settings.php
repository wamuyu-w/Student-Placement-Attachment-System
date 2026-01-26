<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - CUEA Attachment</title>
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        <nav class="sidebar-nav">
             <a href="../Dashboards/student-dashboard.php" class="nav-item">
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
            <a href="student-settings.php" class="nav-item active">
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
            <h1 class="page-title">Account Settings</h1>
            <div class="header-actions">
                <div class="user-profile">
                     <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm max-w-2xl">
                <h2 class="text-lg font-semibold mb-4">Change Password</h2>
                <form action="process-update-password.php" method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                        <input type="password" name="current_password" class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="password" name="new_password" class="w-full p-2 border border-gray-300 rounded" required minlength="6">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="w-full p-2 border border-gray-300 rounded" required minlength="6">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>
