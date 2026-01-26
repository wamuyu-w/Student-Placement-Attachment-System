<?php
require_once '../config.php';
requireLogin('staff');
$conn = getDBConnection();

// Get LecturerID
$lecturerId = $_SESSION['LecturerID'] ?? null;
if (!$lecturerId) {
    // Attempt fallback lookup
    $staffNumber = $_SESSION['staff_number'] ?? null;
    if ($staffNumber) {
        $stmt = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
        $stmt->bind_param("s", $staffNumber);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $lecturerId = $row['LecturerID'];
        }
        $stmt->close();
    }
}

// Fetch Supervised Students
$students = [];
if ($lecturerId) {
    $sql = "SELECT s.FirstName, s.LastName, s.Course, s.PhoneNumber, s.Email, 
                   ho.OrganizationName, a.AttachmentStatus
            FROM supervision sv
            JOIN attachment a ON sv.AttachmentID = a.AttachmentID
            JOIN student s ON a.StudentID = s.StudentID
            JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
            WHERE sv.LecturerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - Staff</title>
    <link rel="stylesheet" href="../Dashboards/staff-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
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
            <a href="../Dashboards/staff-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="staff-students.php" class="nav-item active">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Logbook/staff-logbook.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Logbooks</span>
            </a>
            <a href="../Reports/staff-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
             <a href="../Settings/staff-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
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
            <h1 class="page-title">Supervised Students</h1>
             <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search students..." id="searchInput">
                </div>
                 <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['name'][0] ?? 'S', 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Staff'); ?></div>
                        <div class="profile-role">Lecturer</div>
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
                        <th>Host Organization</th>
                        <th>Status</th>
                        <th>Contacts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']); ?></td>
                                <td><?php echo htmlspecialchars($student['Course']); ?></td>
                                <td><?php echo htmlspecialchars($student['OrganizationName']); ?></td>
                                <td><?php echo htmlspecialchars($student['AttachmentStatus']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($student['Email']); ?><br>
                                    <small><?php echo htmlspecialchars($student['PhoneNumber']); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No supervised students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
