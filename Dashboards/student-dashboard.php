<?php
require_once '../config.php';
requireLogin('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - CUEA</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="student-dashboard.css">
</head>
<body>
    <div class="header">
        <h1>Student Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="../Login Pages/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Welcome to Your Dashboard</h2>
            <p>Manage your placement and attachment activities from here.</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Student ID</h3>
                <p><?php echo htmlspecialchars($_SESSION['student_id']); ?></p>
            </div>
            <div class="info-card">
                <h3>Email</h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            <div class="info-card">
                <h3>Username</h3>
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
