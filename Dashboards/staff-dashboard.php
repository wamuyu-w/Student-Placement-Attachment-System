<?php
require_once '../config.php';
requireLogin('staff');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - CUEA</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="staff-dashboard.css">
</head>
<body>
    <div class="header">
        <h1>Staff Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="../Login Pages/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Welcome to Your Dashboard</h2>
            <p>Manage student placements and attachments from here.</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Staff ID</h3>
                <p><?php echo htmlspecialchars($_SESSION['staff_id']); ?></p>
            </div>
            <div class="info-card">
                <h3>Department</h3>
                <p><?php echo htmlspecialchars($_SESSION['department']); ?></p>
            </div>
            <div class="info-card">
                <h3>Role</h3>
                <p><?php echo htmlspecialchars($_SESSION['role']); ?></p>
            </div>
            <div class="info-card">
                <h3>Email</h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
