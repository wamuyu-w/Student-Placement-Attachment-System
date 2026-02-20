<?php
require_once '../config.php';
requireLogin('admin');

// Fetch dashboard statistics
$conn = getDBConnection();

// Get all opportunities
$oppStmt = $conn->prepare("
    SELECT 
        ao.OpportunityID,
        ao.HostOrgID,
        ao.Description,
        ao.EligibilityCriteria,
        ao.ApplicationStartDate,
        ao.ApplicationEndDate,
        ao.Status,
        COALESCE(ho.OrganizationName, 'Unknown Organization') as OrganizationName
    FROM attachmentopportunity ao
    LEFT JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
    ORDER BY ao.ApplicationEndDate DESC
");
$oppStmt->execute();
$opportunities = $oppStmt->get_result();
$oppStmt->close();

// Get all host organizations for selection
$hostOrgStmt = $conn->prepare("SELECT HostOrgID, OrganizationName FROM hostorganization ORDER BY OrganizationName");
$hostOrgStmt->execute();
$hostOrganizations = $hostOrgStmt->get_result();
$hostOrgStmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Opportunities - CUEA</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    
    <!-- Admin Dashboard Styles -->
    <link rel="stylesheet" href="../Dashboards/Admin/admin-dashboard.css">
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
            <a href="../Dashboards/Admin/admin-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Applications/admin-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="admin-opportunities-management.php" class="nav-item active">
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
            <h1 class="page-title">Manage Attachment Opportunities</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search opportunities..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
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

        <!-- Opportunities Flow Section (Admin Version) -->
        <?php include '../Opportunities/opportunities-flow-admin.html'; ?>

        <!-- Existing Opportunities List -->
        <div class="opportunities-list-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> All Opportunities</h2>
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
                            </div>
                            <div class="opportunity-body">
                                <div class="opportunity-info">
                                    <p class="info-label"><i class="fas fa-building"></i> Organization:</p>
                                    <p class="info-value"><?php echo htmlspecialchars($opp['OrganizationName']); ?></p>
                                </div>
                                <div class="opportunity-info">
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
                                <button class="btn-small btn-edit" 
                                        onclick="editOpportunity(this)"
                                        data-id="<?php echo $opp['OpportunityID']; ?>"
                                        data-host="<?php echo $opp['HostOrgID']; ?>"
                                        data-desc="<?php echo htmlspecialchars($opp['Description']); ?>"
                                        data-criteria="<?php echo htmlspecialchars($opp['EligibilityCriteria']); ?>"
                                        data-start="<?php echo $opp['ApplicationStartDate']; ?>"
                                        data-end="<?php echo $opp['ApplicationEndDate']; ?>"
                                        data-status="<?php echo $opp['Status']; ?>">
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
                    <p class="text-muted">Click "Add New Opportunity" above to create an opportunity</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Edit Modal -->
    <?php include 'edit-opportunity-modal.php'; ?>

    <script src="../Dashboards/admin-dashboard.js"></script>
    <script>
        function editOpportunity(btn) {
            // Get data from data attributes
            const id = btn.getAttribute('data-id');
            const hostId = btn.getAttribute('data-host');
            const desc = btn.getAttribute('data-desc');
            const criteria = btn.getAttribute('data-criteria');
            const start = btn.getAttribute('data-start');
            const end = btn.getAttribute('data-end');
            const status = btn.getAttribute('data-status');

            // Populate form fields
            document.getElementById('editOpportunityId').value = id;
            document.getElementById('editHostOrgSelect').value = hostId;
            document.getElementById('editDescription').value = desc;
            document.getElementById('editEligibilityCriteria').value = criteria;
            document.getElementById('editStartDate').value = start;
            document.getElementById('editEndDate').value = end;
            document.getElementById('editStatus').value = status;

            // Show modal
            document.getElementById('editOpportunityFormContainer').style.display = 'block';
        }

        function deleteOpportunity(opportunityId) {
            if (confirm('Are you sure you want to delete this opportunity? This action cannot be undone.')) {
                window.location.href = 'process-delete-opportunity.php?id=' + opportunityId;
            }
        }
    </script>
</body>
</html>
