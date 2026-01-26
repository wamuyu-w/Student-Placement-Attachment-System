<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

// Get Active Attachment and Supervisor
$sql = "SELECT l.Name, l.Department, l.Faculty, l.Role, u.Username as Email
        FROM supervision s
        JOIN attachment a ON s.AttachmentID = a.AttachmentID
        JOIN lecturer l ON s.LecturerID = l.LecturerID
        LEFT JOIN users u ON l.UserID = u.UserID
        WHERE a.StudentID = ? AND a.AttachmentStatus = 'Ongoing'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$supervisor = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor - CUEA Attachment</title>
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        <nav class="sidebar-nav">
             <a href="../Dashboards/student-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Opportunities/student-opportunities.php" class="nav-item">
                <i class="fas fa-briefcase"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Applications/student-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>My Applications</span>
            </a>
            <a href="../Logbook/student-logbook.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="../Reports/student-reports.php" class="nav-item">
                <i class="fas fa-file-pdf"></i>
                <span>Reports</span>
            </a>
            <a href="student-supervisor.php" class="nav-item active">
                <i class="fas fa-user-tie"></i>
                <span>Supervisor</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../Settings/student-settings.php" class="nav-item">
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
            <h1 class="page-title">My Supervisor</h1>
            <div class="header-actions">
                <div class="user-profile">
                     <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-grid">
            <?php if (!$supervisor): ?>
                 <div class="empty-state text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-user-clock text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Supervisor Assigned</h3>
                    <p class="text-gray-500">You will be assigned an academic supervisor once your attachment is confirmed.</p>
                </div>
            <?php else: ?>
                <style>
                    .supervisor-card {
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 2rem;
                        text-align: center;
                    }
                    .supervisor-icon {
                        width: 80px;
                        height: 80px;
                        background-color: #e0f2fe;
                        color: #0284c7;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 2rem;
                        margin: 0 auto 1rem auto;
                    }
                    .supervisor-name {
                        font-size: 1.5rem;
                        font-weight: 700;
                        color: #1f2937;
                        margin-bottom: 0.5rem;
                    }
                    .supervisor-subtitle {
                        color: #6b7280;
                        margin-bottom: 2rem;
                    }
                    .detail-row {
                        display: flex;
                        align-items: center;
                        padding: 1rem;
                        background-color: #f9fafb;
                        border-radius: 8px;
                        margin-bottom: 1rem;
                        text-align: left;
                    }
                    .detail-icon {
                        width: 40px;
                        height: 40px;
                        background-color: white;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #9ca3af;
                        margin-right: 1rem;
                        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                    }
                    .detail-content {
                        flex: 1;
                    }
                    .detail-label {
                        font-size: 0.875rem;
                        color: #6b7280;
                    }
                    .detail-value {
                        font-weight: 500;
                        color: #111827;
                    }
                    .email-link {
                        color: #2563eb;
                        text-decoration: none;
                    }
                    .email-link:hover {
                        text-decoration: underline;
                    }
                </style>
                <div class="supervisor-card">
                    <div class="supervisor-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h2 class="supervisor-name"><?php echo htmlspecialchars($supervisor['Name']); ?></h2>
                    <p class="supervisor-subtitle"><?php echo htmlspecialchars($supervisor['Role']); ?> - <?php echo htmlspecialchars($supervisor['Department']); ?></p>
                    
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                        <div class="detail-row">
                            <div class="detail-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Faculty</div>
                                <div class="detail-value"><?php echo htmlspecialchars($supervisor['Faculty']); ?></div>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value">
                                    <a href="mailto:<?php echo htmlspecialchars($supervisor['Email']); ?>?subject=Inquiry from Attachment Student" class="email-link">
                                        Contact via Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>
