<?php
require_once '../config.php';
requireLogin('admin');
$conn = getDBConnection();

// Fetch students
$sql = "SELECT s.StudentID, s.FirstName, s.LastName, s.Course, s.Faculty, s.YearOfStudy, s.Email, s.EligibilityStatus 
        FROM student s";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin</title>
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
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea logo">
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
            <a href="../Opportunities/admin-opportunities-management.php" class="nav-item">
                <i class="fas fa-lightbulb"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Supervisor/admin-supervisors.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Supervisors</span>
            </a>
            <a href="admin-students.php" class="nav-item active">
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
            <h1 class="page-title">Manage Students</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search students..." id="searchInput">
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

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Faculty</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                <td><?php echo htmlspecialchars($row['Course']); ?></td>
                                <td><?php echo htmlspecialchars($row['Faculty']); ?></td>
                                <td>
                                    <?php 
                                    $status = $row['EligibilityStatus'];
                                    $color = match($status) {
                                        'Eligible' => 'green',
                                        'not eligible' => 'red',
                                        'Cleared' => '#10b981',
                                        'Attachment Ongoing' => 'blue',
                                        default => 'gray'
                                    };
                                    echo "<span style='color: $color; font-weight: bold;'>" . htmlspecialchars($status) . "</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if ($status == 'Eligible' || $status == 'Attachment Ongoing'): ?>
                                        <form action="process-clear-student.php" method="POST" onsubmit="return confirm('Are you sure you want to clear this student? This will complete their attachment process.');" style="display:inline;">
                                            <input type="hidden" name="student_id" value="<?php echo $row['StudentID']; ?>">
                                            <button type="submit" style="background-color: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                                <i class="fas fa-check"></i> Clear
                                            </button>
                                        </form>
                                    <?php elseif ($status == 'Cleared'): ?>
                                        <span style="color: #10b981;"><i class="fas fa-check-circle"></i> Done</span>
                                    <?php else: ?>
                                        <span style="color: #9ca3af;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
