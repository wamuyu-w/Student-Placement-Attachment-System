<?php
require_once '../config.php';
requireLogin('admin');
$conn = getDBConnection();

// Fetch applications
// Fetch job applications
$sql = "SELECT ja.OpportunityID, ja.StudentID, ja.ApplicationDate, s.FirstName, s.LastName, ao.Description, h.OrganizationName, ja.Status
        FROM jobapplication ja
        JOIN student s ON ja.StudentID = s.StudentID
        JOIN attachmentopportunity ao ON ja.OpportunityID = ao.OpportunityID
        JOIN hostorganization h ON ja.HostOrgID = h.HostOrgID
        ORDER BY ja.ApplicationDate DESC LIMIT 50";
$result = $conn->query($sql);

// Fetch program applications (attachment approvals)
$progSql = "SELECT aa.ApplicationID, aa.StudentID, aa.ApplicationDate, aa.ApplicationStatus, 
            COALESCE(h.OrganizationName, aa.IntendedHostOrg) AS OrganizationName, 
            s.FirstName, s.LastName 
            FROM attachmentapplication aa
            JOIN student s ON aa.StudentID = s.StudentID
            LEFT JOIN hostorganization h ON aa.HostOrgID = h.HostOrgID
            ORDER BY aa.ApplicationDate DESC";
$progResult = $conn->query($progSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - Admin</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    
    <!-- Admin Dashboard Styles (Reused) -->
    <link rel="stylesheet" href="../Dashboards/Admin/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        
        <nav class="sidebar-nav">
            <a href="../Dashboards/Admin/admin-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="admin-applications.php" class="nav-item active">
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
            <h1 class="page-title">Manage Applications</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search applications..." id="searchInput">
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

        <div class="table-container mb-4">
            <h2 class="section-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); padding: 0 1rem;">Program Applications (Attachment Clearance)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Intended Host</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($progResult && $progResult->num_rows > 0): ?>
                        <?php while($row = $progResult->fetch_assoc()): ?>
                            <?php 
                            $statusClass = '';
                            $status = strtolower($row['ApplicationStatus']);
                            if (strpos($status, 'pending') !== false) $statusClass = 'status-pending';
                            elseif (strpos($status, 'approve') !== false) $statusClass = 'status-approved';
                            elseif (strpos($status, 'reject') !== false) $statusClass = 'status-rejected';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ApplicationDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                <td><?php echo htmlspecialchars($row['OrganizationName'] ?? 'Not Specified'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['ApplicationStatus']); ?></span>
                                </td>
                                <td>
                                    <?php if ($row['ApplicationStatus'] == 'Pending'): ?>
                                    <form action="process-program-application.php" method="POST" class="status-form">
                                        <input type="hidden" name="application_id" value="<?php echo $row['ApplicationID']; ?>">
                                        <div style="display: flex; gap: 8px;">
                                            <button type="submit" name="status" value="Approved" title="Approve" style="background: none; border: none; cursor: pointer; color: var(--success-color);">
                                                <i class="fas fa-check-circle fa-lg"></i>
                                            </button>
                                            <button type="submit" name="status" value="Rejected" title="Reject" style="background: none; border: none; cursor: pointer; color: var(--danger-color);">
                                                <i class="fas fa-times-circle fa-lg"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary); font-size: 0.9rem;">Processed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No program applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h2 class="section-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); padding: 0 1rem; margin-top: 2rem;">Job Applications (Specific Opportunities)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Opportunity</th>
                        <th>Host Org</th>
                        <th>Status / Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <?php 
                            $statusClass = '';
                            $status = strtolower($row['Status']);
                            if (strpos($status, 'pending') !== false) $statusClass = 'status-pending';
                            elseif (strpos($status, 'approve') !== false) $statusClass = 'status-approved';
                            elseif (strpos($status, 'reject') !== false) $statusClass = 'status-rejected';
                            elseif (strpos($status, 'accept') !== false) $statusClass = 'status-approved'; // Handle 'Accepted' similarly
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ApplicationDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                <td><?php echo htmlspecialchars($row['Description']); ?></td>
                                <td><?php echo htmlspecialchars($row['OrganizationName']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['Status']); ?></span>
                                    
                                    <form action="process-update-application-status.php" method="POST" class="status-form">
                                        <input type="hidden" name="opportunity_id" value="<?php echo $row['OpportunityID']; ?>">
                                        <input type="hidden" name="student_id" value="<?php echo $row['StudentID']; ?>">
                                        <select name="status" class="status-select" onchange="this.form.submit()">
                                            <option value="" disabled selected>Update</option>
                                            <option value="Approved">Approve</option>
                                            <option value="Rejected">Reject</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
