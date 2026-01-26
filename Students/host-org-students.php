<?php
require_once '../config.php';
requireLogin('host_org');

$conn = getDBConnection();
$hostOrgId = $_SESSION['host_org_id'];

// Get students attached
$sql = "SELECT s.FirstName, s.LastName, s.Course, s.YearOfStudy, a.StartDate, a.EndDate, a.AttachmentStatus
        FROM attachment a
        JOIN student s ON a.StudentID = s.StudentID
        WHERE a.HostOrgID = ?
        ORDER BY a.StartDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hostOrgId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Host Organization</title>
    <link rel="stylesheet" href="../Dashboards/host-org-dashboard.css">
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
            <a href="../Dashboards/host-org-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Opportunities/host-management-opportunities.php" class="nav-item">
                <i class="fas fa-briefcase"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Applications/host-org-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="host-org-students.php" class="nav-item active">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Reports/host-org-reports.php" class="nav-item">
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
    <div class="main-content">
        <header class="main-header">
            <h1 class="page-title">Attached Students</h1>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="profile-img" style="background: #8B1538; color: white; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;">
                         <?php echo strtoupper(substr($_SESSION['organization_name'] ?? 'H', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>
        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <?php if ($result->num_rows > 0): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                                <th style="padding: 12px;">Name</th>
                                <th style="padding: 12px;">Course</th>
                                <th style="padding: 12px;">Year</th>
                                <th style="padding: 12px;">Start Date</th>
                                <th style="padding: 12px;">End Date</th>
                                <th style="padding: 12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['YearOfStudy']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['StartDate']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['EndDate']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['AttachmentStatus']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">No students currently attached.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
