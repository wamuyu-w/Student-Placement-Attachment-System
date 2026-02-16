<?php
require_once '../config.php';
requireLogin('admin');
$conn = getDBConnection();

// Fetch supervisors
$sql = "SELECT l.Name, l.Department, l.Faculty, u.Status 
        FROM lecturer l 
        JOIN users u ON l.UserID = u.UserID 
        WHERE l.Role = 'Supervisor'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Supervisors - Admin</title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    
    <!-- Admin Dashboard Styles -->
    <link rel="stylesheet" href="../Dashboards/Admin/admin-dashboard.css">
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
            <a href="admin-supervisors.php" class="nav-item active">
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
             <a href="../Settings/admin-settings.php" class="nav-item">
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
            <h1 class="page-title">Manage Supervisors</h1>
            <div class="header-actions">
                <button onclick="document.getElementById('addSupervisorModal').style.display='block'" class="btn-primary" style="background-color: #8B1538; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus"></i> Add Supervisor
                </button>
                <button onclick="document.getElementById('bulkUploadModal').style.display='block'" class="btn-secondary" style="background-color: #059669; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-csv"></i> Bulk Upload
                </button>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search supervisors..." id="searchInput">
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

        <!-- Add Supervisor Modal -->
        <div id="addSupervisorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 1.25rem;">Add New Supervisor</h2>
                    <span onclick="document.getElementById('addSupervisorModal').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
                </div>
                <form action="process-add-supervisor.php" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Staff Number</label>
                        <input type="text" name="staffNumber" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 10px; background-color: #f3f4f6; padding: 10px; border-radius: 4px; border: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 5px 0;"><i class="fas fa-info-circle"></i> <strong>Automatic Generation:</strong></p>
                            <ul style="margin: 0; padding-left: 20px;">
                                <li>A generic <strong>Username</strong> (e.g., L004) will be generated.</li>
                                <li>A default <strong>Password</strong> (<code>Changeme123!</code>) will be set.</li>
                            </ul>
                            <p style="margin: 5px 0 0 0;">The supervisor will use these to log in and update their profile.</p>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" onclick="document.getElementById('addSupervisorModal').style.display='none'" style="padding: 8px 16px; margin-right: 10px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="background-color: #8B1538; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Add Supervisor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Upload Modal -->
        <div id="bulkUploadModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 1.25rem;">Bulk Upload Supervisors</h2>
                    <span onclick="document.getElementById('bulkUploadModal').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
                </div>
                <form action="process-bulk-supervisors.php" method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select CSV File</label>
                        <input type="file" name="csvFile" required accept=".csv" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <div style="background-color: #f8fafc; padding: 10px; margin-top: 10px; border-radius: 4px; font-size: 0.9em; color: #64748b;">
                            <p style="margin: 0 0 5px 0;"><strong>CSV Format (Headers required):</strong></p>
                            <code style="display: block; background: #e2e8f0; padding: 5px; border-radius: 3px;">StaffNumber, Name, Department</code>
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
        <div class="content-grid">
            
            <!-- Assignment Form Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-8" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Assign Supervisor to Student</h2>
                <form action="process-assign-supervisor.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Select Student (Ongoing Attachment)</label>
                        <select name="attachment_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                            <option value="">-- Choose Student --</option>
                            <?php
                            // Fetch students with active attachments needing supervision
                            $studSql = "SELECT a.AttachmentID, s.FirstName, s.LastName, h.OrganizationName 
                                        FROM attachment a 
                                        JOIN student s ON a.StudentID = s.StudentID 
                                        JOIN hostorganization h ON a.HostOrgID = h.HostOrgID
                                        LEFT JOIN supervision sup ON a.AttachmentID = sup.AttachmentID
                                        WHERE a.AttachmentStatus = 'Ongoing' AND sup.SupervisionID IS NULL";
                            $studRes = $conn->query($studSql);
                            if ($studRes->num_rows > 0) {
                                while($row = $studRes->fetch_assoc()) {
                                    echo "<option value='" . $row['AttachmentID'] . "'>" . htmlspecialchars($row['FirstName'] . " " . $row['LastName'] . " (" . $row['OrganizationName'] . ")") . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No students pending assignment</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Select Supervisor</label>
                        <select name="lecturer_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                            <option value="">-- Choose Lecturer --</option>
                            <?php
                            // Fetch Supervisors
                            $lecSql = "SELECT LecturerID, Name FROM lecturer WHERE Role = 'Supervisor' OR Role = 'Admin'";
                            $lecRes = $conn->query($lecSql);
                            while($row = $lecRes->fetch_assoc()) {
                                echo "<option value='" . $row['LecturerID'] . "'>" . htmlspecialchars($row['Name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" style="background-color: #8B1538; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer;">
                        Assign
                    </button>
                </form>
            </div>

            <div class="table-container">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Supervisor List</h2>
            <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Faculty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset pointer or re-query if needed, but here we just list them
                    // Note: The previous query result pointer is at the end. We need to re-fetch or effectively use data_seek(0)
                    $result->data_seek(0);
                    if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Department']); ?></td>
                                <td><?php echo htmlspecialchars($row['Faculty']); ?></td>
                                <td><?php echo htmlspecialchars($row['Status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No supervisors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
