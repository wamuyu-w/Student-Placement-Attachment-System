<?php
require_once '../config.php';

if (!isLoggedIn() || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'staff')) {
    header("Location: ../Login Pages/staff-login.php");
    exit();
}

$userType = $_SESSION['user_type'];
$conn = getDBConnection();

$studentId = $_GET['student_id'] ?? null;
if (!$studentId) {
    echo "No student specified.";
    exit();
}

// Fetch student details
$stmt = $conn->prepare("SELECT FirstName, LastName, Course, Faculty FROM student WHERE StudentID = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "Student not found.";
    exit();
}

// Fetch current attachment
$stmt = $conn->prepare("
    SELECT a.AttachmentID, h.OrganizationName, a.StartDate, a.AttachmentStatus 
    FROM attachment a 
    JOIN hostorganization h ON a.HostOrgID = h.HostOrgID 
    WHERE a.StudentID = ? 
    ORDER BY a.StartDate DESC LIMIT 1
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$attachment = $stmt->get_result()->fetch_assoc();
$stmt->close();

$assessments = [];
$logbookEntries = [];

if ($attachment) {
    $attachmentId = $attachment['AttachmentID'];
    
    // Fetch assessments
    $stmt = $conn->prepare("SELECT AssessmentDate, AssessmentType, Marks, Remarks FROM assessment WHERE AttachmentID = ? ORDER BY AssessmentDate");
    $stmt->bind_param("i", $attachmentId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $assessments[] = $row;
    }
    $stmt->close();
    
    // Fetch logbook grouped by week
    $stmt = $conn->prepare("
        SELECT le.EntryDate, le.Activities, le.HostSupervisorComments,
               CEIL(DATEDIFF(le.EntryDate, ?) / 7) as WeekNumber
        FROM logbookentry le
        JOIN logbook l ON le.LogbookID = l.LogbookID
        WHERE l.AttachmentID = ?
        ORDER BY le.EntryDate ASC
    ");
    $stmt->bind_param("si", $attachment['StartDate'], $attachmentId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $week = $row['WeekNumber'] > 0 ? $row['WeekNumber'] : 1; 
        if (!isset($logbookEntries[$week])) {
            $logbookEntries[$week] = [];
        }
        $logbookEntries[$week][] = $row;
    }
    $stmt->close();
}
$conn->close();

// Include correct CSS and Sidebar based on user type
$cssPath = $userType === 'admin' ? '../Dashboards/Admin/admin-dashboard.css' : '../Dashboards/staff-dashboard.css';
$userName = htmlspecialchars($_SESSION['name'] ?? ucfirst($userType));
$userRole = $userType === 'admin' ? 'Coordinator' : 'Lecturer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress - <?php echo $student['FirstName']; ?></title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="<?php echo $cssPath; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .progress-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .week-group {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .week-header {
            background-color: #f9fafb;
            padding: 12px 15px;
            font-weight: bold;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }
        .entry-item {
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
        }
        .entry-item:last-child {
            border-bottom: none;
        }
        .entry-date {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 5px;
        }
        table.assessments-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.assessments-table th, table.assessments-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        table.assessments-table th {
            background-color: #f8f9fa;
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
            <?php if ($userType === 'admin'): ?>
                <a href="../Dashboards/Admin/admin-dashboard.php" class="nav-item">
                    <i class="fas fa-th-large"></i><span>Dashboard</span>
                </a>
                <a href="../Applications/admin-applications.php" class="nav-item">
                    <i class="fas fa-file-alt"></i><span>Applications</span>
                </a>
                <a href="../Opportunities/admin-opportunities-management.php" class="nav-item">
                    <i class="fas fa-lightbulb"></i><span>Opportunities</span>
                </a>
                <a href="../Supervisor/admin-supervisors.php" class="nav-item">
                    <i class="fas fa-users"></i><span>Supervisors</span>
                </a>
                <a href="admin-students.php" class="nav-item active">
                    <i class="fas fa-graduation-cap"></i><span>Students</span>
                </a>
                <a href="../Reports/admin-reports.php" class="nav-item">
                    <i class="fas fa-chart-bar"></i><span>Reports</span>
                </a>
            <?php else: ?>
                <a href="../Dashboards/staff-dashboard.php" class="nav-item">
                    <i class="fas fa-th-large"></i><span>Dashboard</span>
                </a>
                <a href="staff-students.php" class="nav-item active">
                    <i class="fas fa-graduation-cap"></i><span>Students</span>
                </a>
                <a href="../Logbook/staff-logbook.php" class="nav-item">
                    <i class="fas fa-file-alt"></i><span>Logbooks</span>
                </a>
                <a href="../Reports/staff-reports.php" class="nav-item">
                    <i class="fas fa-chart-bar"></i><span>Reports</span>
                </a>
                <a href="../Supervisor/staff-supervision.php" class="nav-item">
                    <i class="fas fa-chalkboard-teacher"></i><span>Supervision</span>
                </a>
                <a href="../Settings/staff-settings.php" class="nav-item">
                    <i class="fas fa-cog"></i><span>Settings</span>
                </a>
            <?php endif; ?>
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
            <h1 class="page-title">Student Progress: <?php echo htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']); ?></h1>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo $userName; ?></div>
                        <div class="profile-role"><?php echo $userRole; ?></div>
                    </div>
                </div>
            </div>
        </header>

        <div style="margin-bottom: 10px;">
            <a href="<?php echo $userType === 'admin' ? 'admin-students.php' : 'staff-students.php'; ?>" style="color: #6b7280; text-decoration: none;"><i class="fas fa-arrow-left"></i> Back to Students</a>
        </div>

        <?php if ($attachment): ?>
            <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; margin-top: 10px; border: 1px solid #bae6fd; color: #0369a1;">
                <strong>Current Placement:</strong> <?php echo htmlspecialchars($attachment['OrganizationName']); ?> <br>
                <strong>Start Date:</strong> <?php echo date('M j, Y', strtotime($attachment['StartDate'])); ?> | 
                <strong>Status:</strong> <?php echo htmlspecialchars($attachment['AttachmentStatus']); ?>
            </div>

            <div class="progress-section">
                <h2>Assessment Grades</h2>
                <?php if (empty($assessments)): ?>
                    <p style="color: #6b7280;">No assessments recorded yet.</p>
                <?php else: ?>
                    <table class="assessments-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assessments as $assessment): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($assessment['AssessmentDate'])); ?></td>
                                    <td><?php echo htmlspecialchars($assessment['AssessmentType']); ?></td>
                                    <td><strong><?php echo number_format($assessment['Marks'], 1); ?>%</strong></td>
                                    <td><?php echo htmlspecialchars($assessment['Remarks']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="progress-section">
                <h2>Logbook Entries (By Week)</h2>
                <?php if (empty($logbookEntries)): ?>
                    <p style="color: #6b7280;">No logbook entries submitted yet.</p>
                <?php else: ?>
                    <?php 
                    ksort($logbookEntries);
                    foreach ($logbookEntries as $week => $entries): 
                    ?>
                        <div class="week-group">
                            <div class="week-header">Week <?php echo $week; ?></div>
                            <?php foreach ($entries as $entry): ?>
                                <div class="entry-item">
                                    <div class="entry-date"><i class="far fa-calendar-alt"></i> <?php echo date('M j, Y', strtotime($entry['EntryDate'])); ?></div>
                                    <div style="margin: 5px 0 10px 0;">
                                        <strong>Activities:</strong>
                                        <?php 
                                        $activitiesData = json_decode($entry['Activities'], true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($activitiesData)) {
                                            echo "<div style='margin-top: 10px; display: grid; gap: 10px;'>";
                                            foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day) {
                                                if (!empty($activitiesData[$day]['task']) || !empty($activitiesData[$day]['comment'])) {
                                                    echo "<div style='background: #f8fafc; padding: 10px; border-radius: 6px; border-left: 3px solid #3b82f6;'>";
                                                    echo "<h4 style='margin: 0 0 5px 0; font-size: 0.9rem; color: #1e293b;'>" . $day . "</h4>";
                                                    
                                                    if (!empty($activitiesData[$day]['task'])) {
                                                        $task = html_entity_decode($activitiesData[$day]['task'], ENT_QUOTES | ENT_HTML5);
                                                        $task = trim($task, "\"'");
                                                        $task = html_entity_decode($task, ENT_QUOTES | ENT_HTML5); // In case it's double encoded
                                                        echo "<p style='margin: 0; font-size: 0.85rem;'><strong>Task:</strong> " . nl2br(htmlspecialchars($task)) . "</p>";
                                                    }
                                                    if (!empty($activitiesData[$day]['comment'])) {
                                                        $comment = html_entity_decode($activitiesData[$day]['comment'], ENT_QUOTES | ENT_HTML5);
                                                        $comment = trim($comment, "\"'");
                                                        $comment = html_entity_decode($comment, ENT_QUOTES | ENT_HTML5);
                                                        echo "<p style='margin: 4px 0 0 0; font-size: 0.85rem; color: #475569;'><em>Reflections:</em> " . nl2br(htmlspecialchars($comment)) . "</p>";
                                                    }
                                                    
                                                    echo "</div>";
                                                }
                                            }
                                            echo "</div>";
                                        } else {
                                            // Fallback for legacy plain text entries
                                            echo "<p style='margin-top: 5px; color: #334155;'>" . nl2br(htmlspecialchars($entry['Activities'])) . "</p>";
                                        }
                                        ?>
                                    </div>
                                    <?php if ($entry['HostSupervisorComments']): ?>
                                        <div style="background: #f9fafb; padding: 10px; border-radius: 4px; border-left: 3px solid #10b981; font-size: 0.9rem;">
                                            <strong>Host Supervisor:</strong> <?php echo nl2br(htmlspecialchars($entry['HostSupervisorComments'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="progress-section">
                <p>This student does not have an active placement or attachment record yet.</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
