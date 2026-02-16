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
                <button onclick="document.getElementById('addStudentModal').style.display='block'" class="btn-primary" style="background-color: #8B1538; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus"></i> Add Student
                </button>
                <button onclick="document.getElementById('bulkUploadModal').style.display='block'" class="btn-secondary" style="background-color: #059669; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-csv"></i> Bulk Upload
                </button>
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

        <!-- Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem; border: 1px solid #fecaca;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Add Student Modal -->
        <div id="addStudentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 1.25rem;">Add New Student</h2>
                    <span onclick="document.getElementById('addStudentModal').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
                </div>
                <form action="process-add-student.php" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Admission Number (Username)</label>
                        <input type="text" name="admNumber" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="font-size: 0.85rem; color: #6b7280; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> A default password <code>Changeme123!</code> will be generated. The student will be required to update their profile details (Name, Course, etc.) upon first login.
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" onclick="document.getElementById('addStudentModal').style.display='none'" style="padding: 8px 16px; margin-right: 10px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="background-color: #8B1538; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Add Student</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Upload Modal -->
        <div id="bulkUploadModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 1.25rem;">Bulk Upload Students</h2>
                    <span onclick="document.getElementById('bulkUploadModal').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
                </div>
                <form action="process-bulk-students.php" method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select CSV File</label>
                        <input type="file" name="csvFile" required accept=".csv" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <div style="background-color: #f8fafc; padding: 10px; margin-top: 10px; border-radius: 4px; font-size: 0.9em; color: #64748b;">
                            <p style="margin: 0 0 5px 0;"><strong>CSV Format (Headers required):</strong></p>
                            <code style="display: block; background: #e2e8f0; padding: 5px; border-radius: 3px;">AdmissionNumber, FirstName, LastName</code>
                            <p style="margin: 5px 0 0 0;">Passwords will be set to default: <code>Changeme123!</code></p>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" onclick="document.getElementById('bulkUploadModal').style.display='none'" style="padding: 8px 16px; margin-right: 10px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="background-color: #059669; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>


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
