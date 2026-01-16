<?php
require_once '../config.php';
requireLogin('host_org');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Organization Dashboard - CUEA</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="host-org-dashboard.css">
</head>
<body>
    <div class="header">
        <h1>Host Organization Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['organization_name']); ?></span>
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
                <h3>Organization Name</h3>
                <p><?php echo htmlspecialchars($_SESSION['organization_name']); ?></p>
            </div>
            <div class="info-card">
                <h3>Contact Person</h3>
                <p><?php echo htmlspecialchars($_SESSION['contact_person']); ?></p>
            </div>
            <div class="info-card">
                <h3>Email</h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            <div class="info-card">
                <h3>Phone</h3>
                <p><?php echo htmlspecialchars($_SESSION['phone']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
