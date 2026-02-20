<?php
require_once '../config.php';
requireLogin('host_org');
$conn = getDBConnection();

$hostOrgId = $_SESSION['host_org_id'] ?? null;
if (!$hostOrgId) {
    header("Location: ../Login Pages/login-host-org.php");
    exit();
}

// Fetch Logbook Entries
// For Host Org, we look at attachments linked to this HostOrgID
$entries = [];
if ($hostOrgId) {
    $sql = "SELECT st.FirstName, st.LastName, le.EntryDate, le.Activities, le.HostSupervisorComments, le.AcademicSupervisorComments, le.EntryID
            FROM attachment a
            JOIN logbook l ON l.AttachmentID = a.AttachmentID
            JOIN logbookentry le ON l.LogbookID = le.LogbookID
            JOIN student st ON a.StudentID = st.StudentID
            WHERE a.HostOrgID = ?
            ORDER BY le.EntryDate DESC LIMIT 50";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hostOrgId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbooks - Host Organization</title>
    <link rel="stylesheet" href="../Dashboards/host-org-dashboard.css">
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
            <a href="../Students/host-org-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="host-org-logbook.php" class="nav-item active">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="../Reports/host-org-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        
            <a href="../Supervisor/host-org-supervision.php" class="nav-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Supervision</span>
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
            <h1 class="page-title">Student Logbook Entries</h1>
             <div class="header-actions">
                <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;"><?php echo strtoupper(substr($_SESSION['organization_name'][0] ?? 'H', 0, 1)); ?></div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['organization_name'] ?? 'Host Org'); ?></div>
                        <div class="profile-role">Host Organization</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Activities</th>
                        <th>Academic Comments</th>
                        <th>Host Comments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entries)): ?>
                        <?php foreach($entries as $entry): ?>
                            <tr>
                                <td style="vertical-align: top; width: 15%;"><?php echo htmlspecialchars(date('M j, Y', strtotime($entry['EntryDate']))); ?></td>
                                <td style="vertical-align: top; width: 15%;"><?php echo htmlspecialchars($entry['FirstName'] . ' ' . $entry['LastName']); ?></td>
                                <td style="vertical-align: top; width: 35%;">
                                    <?php 
                                    $activities = json_decode($entry['Activities'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($activities)) {
                                        echo '<div style="font-size: 0.9em;">';
                                        foreach ($activities as $day => $data) {
                                            if (!empty($data['task']) || !empty($data['comment'])) {
                                                echo '<strong>' . $day . ':</strong><br>';
                                                if (!empty($data['task'])) echo '<span style="color: #4b5563;">Task: ' . htmlspecialchars($data['task']) . '</span><br>';
                                                if (!empty($data['comment'])) echo '<span style="color: #059669;">Outcome: ' . htmlspecialchars($data['comment']) . '</span><br>';
                                                echo '<div style="margin-bottom: 8px;"></div>';
                                            }
                                        }
                                        echo '</div>';
                                    } else {
                                        echo nl2br(htmlspecialchars($entry['Activities']));
                                    }
                                    ?>
                                </td>
                                <td style="vertical-align: top; width: 15%; font-size: 0.9em; color: #666;"><?php echo htmlspecialchars($entry['AcademicSupervisorComments'] ?? 'None'); ?></td>
                                <td style="vertical-align: top; width: 20%;">
                                    <div style="margin-bottom: 8px; font-size: 0.9em;">
                                        <?php echo htmlspecialchars($entry['HostSupervisorComments'] ?? ''); ?>
                                    </div>
                                    <button onclick="openCommentModal(<?php echo $entry['EntryID']; ?>)" style="color: #8B1538; background: none; border: none; cursor: pointer; text-decoration: underline; white-space: nowrap;">
                                        <?php echo !empty($entry['HostSupervisorComments']) ? 'Edit Comment' : 'Add Comment'; ?>
                                    </button>
                                </td>
                                 <td>
                                     <!-- Placeholder for potential future actions -->
                                 </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No logbook entries found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comment Modal -->
    <div id="commentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 24px; border-radius: 8px; width: 500px; max-width: 90%;">
            <h3 style="margin-top: 0; color: #1f2937;">Add Host Supervisor Comment</h3>
            <form action="process-logbook-comment.php" method="POST">
                <input type="hidden" name="entry_id" id="modalEntryId">
                <textarea name="comment" rows="4" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 4px; margin: 16px 0; font-family: inherit;" placeholder="Enter your feedback here..."></textarea>
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="document.getElementById('commentModal').style.display='none'" style="padding: 8px 16px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 8px 16px; background: #8B1538; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Comment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCommentModal(entryId) {
            document.getElementById('modalEntryId').value = entryId;
            document.getElementById('commentModal').style.display = 'flex';
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
