<?php
require_once '../config.php';
requireLogin('host_org');

// Get statistics
$conn = getDBConnection();
$hostOrgId = $_SESSION['host_org_id'] ?? null;

if (!$hostOrgId) {
    header("Location: ../Login Pages/login-host-org.php");
    exit();
}

// Get opportunities for this host organization
$oppStmt = $conn->prepare("
    SELECT 
        OpportunityID,
        Description,
        EligibilityCriteria,
        ApplicationStartDate,
        ApplicationEndDate,
        Status,
        DATE_ADD(ApplicationEndDate, INTERVAL 0 DAY) as daysUntilExpire
    FROM attachmentopportunity
    WHERE HostOrgID = ?
    ORDER BY ApplicationEndDate DESC
");
$oppStmt->bind_param("i", $hostOrgId);
$oppStmt->execute();
$opportunities = $oppStmt->get_result();
$oppStmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Opportunities - CUEA</title>
    <link rel="stylesheet" href="host-org-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="host-org-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-briefcase"></i>
                <span>Placements</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="opportunities-management.php" class="nav-item active">
                <i class="fas fa-lightbulb"></i>
                <span>Opportunities</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
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
            <h1 class="page-title">Manage Attachment Opportunities</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search opportunities..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['organization_name'][0], 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['organization_name']); ?></div>
                        <div class="profile-role">Host Organization</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Opportunities Flow Section -->
        <?php include '../Opportunities/opportunities-flow.html'; ?>

        <!-- Existing Opportunities List -->
        <div class="opportunities-list-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Your Posted Opportunities</h2>
            </div>
            
            <?php if ($opportunities->num_rows > 0): ?>
                <div class="opportunities-grid">
                    <?php while ($opp = $opportunities->fetch_assoc()): ?>
                        <div class="opportunity-card">
                            <div class="opportunity-header">
                                <h3><?php echo htmlspecialchars(substr($opp['Description'], 0, 100)); ?>...</h3>
                                <span class="status-badge status-<?php echo strtolower($opp['Status']); ?>">
                                    <?php echo htmlspecialchars($opp['Status']); ?>
                                </span>
                                <br>
                            </div>
                            <div class="opportunity-body">
                                <div class="opportunity-info">
                                    <br>
                                    <p class="info-label"><i class="fas fa-calendar-alt"></i> Dates:</p>
                                    <p class="info-value">
                                        <?php echo date('M d, Y', strtotime($opp['ApplicationStartDate'])); ?> 
                                        to 
                                        <?php echo date('M d, Y', strtotime($opp['ApplicationEndDate'])); ?>
                                    </p>
                                </div>
                                <div class="opportunity-info">
                                    <p class="info-label"><i class="fas fa-graduation-cap"></i> Eligibility:</p>
                                    <p class="info-value"><?php echo htmlspecialchars(substr($opp['EligibilityCriteria'], 0, 80)); ?>...</p>
                                </div>
                            </div>
                            <div class="opportunity-footer">
                                <button class="btn-small btn-edit" onclick="editOpportunity(<?php echo $opp['OpportunityID']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-small btn-delete" onclick="deleteOpportunity(<?php echo $opp['OpportunityID']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No opportunities posted yet</p>
                    <p class="text-muted">Click "Add New Opportunity" above to create your first opportunity</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="host-org-dashboard.js"></script>
    <script>
        function editOpportunity(opportunityId) {
            alert('Edit functionality coming soon - Opportunity ID: ' + opportunityId);
        }

        function deleteOpportunity(opportunityId) {
            if (confirm('Are you sure you want to delete this opportunity?')) {
                alert('Delete functionality coming soon - Opportunity ID: ' + opportunityId);
            }
        }
    </script>
</body>
</html>
