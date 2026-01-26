<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

//fixed from active to ongoing - status for a logbook to be opened
$attachmentStmt = $conn->prepare("SELECT AttachmentID, HostOrgID, StartDate, EndDate FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing' LIMIT 1");
$attachmentStmt->bind_param("i", $studentId);
$attachmentStmt->execute();
$attachmentResult = $attachmentStmt->get_result();

$hasActiveAttachment = $attachmentResult->num_rows > 0;
$attachment = $hasActiveAttachment ? $attachmentResult->fetch_assoc() : null;
$attachmentStmt->close();

$logbookEntries = [];
if ($hasActiveAttachment) {
    $logbookIdStmt = $conn->prepare("SELECT LogbookID FROM logbook WHERE AttachmentID = ?");
    $logbookIdStmt->bind_param("i", $attachment['AttachmentID']);
    $logbookIdStmt->execute();
    $logbookIdResult = $logbookIdStmt->get_result();
    
    if ($logbookIdResult->num_rows > 0) {
        $logbookId = $logbookIdResult->fetch_assoc()['LogbookID'];
        
        $entriesStmt = $conn->prepare("SELECT * FROM logbookentry WHERE LogbookID = ? ORDER BY EntryDate DESC");
        $entriesStmt->bind_param("i", $logbookId);
        $entriesStmt->execute();
        $logbookEntries = $entriesStmt->get_result();
    } else {
        // If logbook does not exist, create one
        $createLogbookStmt = $conn->prepare("INSERT INTO logbook (AttachmentID, IssueDate, Status) VALUES (?, NOW(), 'Ongoing')");
        $createLogbookStmt->bind_param("i", $attachment['AttachmentID']);
        $createLogbookStmt->execute();
        $logbookId = $conn->insert_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook - CUEA Attachment</title>
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .logbook-container {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .entry-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }
        .entry-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .entry-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 0.875rem;
        }
        .entry-content {
            color: #334155;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        .supervisor-comment {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.875rem;
            color: #475569;
            border-left: 3px solid #3b82f6;
        }
        .btn-add-entry {
            background-color: #8B1538;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add-entry:hover {
            background-color: #BE1E4A;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
        }
    </style>
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
            <a href="student-logbook.php" class="nav-item active">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="../Reports/student-reports.php" class="nav-item">
                <i class="fas fa-file-pdf"></i>
                <span>Reports</span>
            </a>
            <a href="../Supervisor/student-supervisor.php" class="nav-item">
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
            <h1 class="page-title">Weekly Logbook</h1>
            <div class="header-actions">
                <div class="user-profile">
                     <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-grid">
            <?php if (!$hasActiveAttachment): ?>
                <div class="empty-state text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-lock text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Logbook Locked</h3>
                    <p class="text-gray-500 mb-6">You need an active attachment placement to access the logbook.</p>
                    <br>
                    <a href="../Opportunities/student-opportunities.php" class="btn-primary" style="background-color: #8B1538; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                        Find a Placement
                    </a>
                </div>
            <?php else: ?>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">My Entries</h2>
                    <button class="btn-add-entry" onclick="openLogbookModal()">
                        <i class="fas fa-plus"></i> New Entry
                    </button>
                </div>

                <div class="logbook-container">
                    <?php if (is_object($logbookEntries) && $logbookEntries->num_rows > 0): ?>
                        <?php while ($entry = $logbookEntries->fetch_assoc()): ?>
                            <div class="entry-card">
                                <div class="entry-header">
                                    <span class="font-medium"><i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($entry['EntryDate'])); ?></span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">Week <?php echo ceil(date('j', strtotime($entry['EntryDate'])) / 7); ?></span>
                                </div>
                                <div class="entry-content">
                                    <p><?php echo nl2br(htmlspecialchars($entry['Activities'])); ?></p>
                                </div>
                                <?php if ($entry['HostSupervisorComments']): ?>
                                    <div class="supervisor-comment">
                                        <strong><i class="fas fa-comment"></i> Supervisor:</strong> <?php echo htmlspecialchars($entry['HostSupervisorComments']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <p class="text-gray-500">No entries yet. Start by adding your first weekly activity report.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Entry Modal -->
    <div id="logbookModal" class="modal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Add Logbook Entry</h2>
            <form action="process-logbook-entry.php" method="POST">
                <input type="hidden" name="logbook_id" value="<?php echo $logbookId ?? ''; ?>">
                <div class="form-group">
                    <label class="form-label">Date (Week Ending)</label>
                    <input type="date" name="entry_date" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Activities Description</label>
                    <textarea name="activities" class="form-control" rows="5" placeholder="Describe your main activities, tasks completed, and skills learned this week..." required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeLogbookModal()" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding: 8px 16px; background: #8B1538; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Entry</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openLogbookModal() {
            document.getElementById('logbookModal').style.display = 'flex';
        }
        function closeLogbookModal() {
            document.getElementById('logbookModal').style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target == document.getElementById('logbookModal')) {
                closeLogbookModal();
            }
        }
    </script>
</body>
</html>
<?php 
if(isset($conn)) $conn->close(); 
?>
