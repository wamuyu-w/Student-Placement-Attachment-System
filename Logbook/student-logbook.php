<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

// Get Active/Ongoing Attachment
$attachmentStmt = $conn->prepare("SELECT AttachmentID, HostOrgID FROM attachment WHERE StudentID = ? AND AttachmentStatus = 'Ongoing' LIMIT 1");
$attachmentStmt->bind_param("i", $studentId);
$attachmentStmt->execute();
$attachmentResult = $attachmentStmt->get_result();
$attachment = $attachmentResult->fetch_assoc();
$attachmentStmt->close();

$hasActiveAttachment = (bool)$attachment;
$currentWeekEnding = $_GET['week'] ?? date('Y-m-d', strtotime('next friday'));
$logbookEntry = null;

if ($hasActiveAttachment) {
    // Ensure Logbook Exists
    $logbookIdStmt = $conn->prepare("SELECT LogbookID FROM logbook WHERE AttachmentID = ?");
    $logbookIdStmt->bind_param("i", $attachment['AttachmentID']);
    $logbookIdStmt->execute();
    $logbookIdResult = $logbookIdStmt->get_result();
    
    if ($logbookIdResult->num_rows > 0) {
        $logbookId = $logbookIdResult->fetch_assoc()['LogbookID'];
    } else {
        $createLogbookStmt = $conn->prepare("INSERT INTO logbook (AttachmentID, IssueDate, Status) VALUES (?, NOW(), 'Ongoing')");
        $createLogbookStmt->bind_param("i", $attachment['AttachmentID']);
        $createLogbookStmt->execute();
        $logbookId = $conn->insert_id;
    }

    // Fetch Entry for Selected Week
    // We treat EntryDate as the "Week Ending" date
    $entryStmt = $conn->prepare("SELECT * FROM logbookentry WHERE LogbookID = ? AND EntryDate = ?");
    $entryStmt->bind_param("is", $logbookId, $currentWeekEnding);
    $entryStmt->execute();
    $entryResult = $entryStmt->get_result();
    $logbookEntry = $entryResult->fetch_assoc();

    // Parse Activities (JSON or Legacy Text)
    $activities = [
        'Monday' => ['task' => '', 'comment' => ''],
        'Tuesday' => ['task' => '', 'comment' => ''],
        'Wednesday' => ['task' => '', 'comment' => ''],
        'Thursday' => ['task' => '', 'comment' => ''],
        'Friday' => ['task' => '', 'comment' => '']
    ];

    if ($logbookEntry && !empty($logbookEntry['Activities'])) {
        $decoded = json_decode($logbookEntry['Activities'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $activities = array_merge($activities, $decoded);
        } else {
            // Fallback for legacy text: put it in Monday's comment
            $activities['Monday']['comment'] = $logbookEntry['Activities'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Logbook - CUEA Attachment</title>
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .logbook-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .logbook-header-section {
            padding: 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .week-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .week-selector label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        .week-selector input[type="date"] {
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 4px;
            font-family: inherit;
            color: #334155;
            outline: none;
        }

        .status-badge {
            background-color: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Grid Layout */
        .entries-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .entries-grid th {
            text-align: left;
            padding: 16px 24px;
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        .entries-grid td {
            padding: 24px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .day-column {
            width: 15%;
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .input-column {
            width: 42.5%;
        }

        textarea.log-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.9rem;
            color: #334155;
            resize: vertical;
            min-height: 100px;
            transition: all 0.2s;
        }

        textarea.log-input:focus {
            outline: none;
            border-color: #8B1538;
            box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.05);
        }

        .feedback-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding: 24px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .feedback-box {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            color: #94a3b8;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .feedback-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feedback-content {
            font-size: 0.9rem;
            color: #334155;
            font-style: italic;
            text-align: left;
            width: 100%;
        }

        .action-bar {
            padding: 24px;
            background-color: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .last-saved {
            color: #64748b;
            font-size: 0.875rem;
        }

        .btn-submit {
            background-color: #8B1538;
            color: white;
            padding: 12px 32px;
            border-radius: 6px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: #70102d;
        }

        .submission-policy {
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
            display: flex;
            gap: 12px;
        }

        .policy-icon {
            color: #2563eb;
            font-size: 1.25rem;
        }

        .policy-text h4 {
            color: #1e3a8a;
            font-size: 0.9rem;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .policy-text p {
            color: #1e40af;
            font-size: 0.85rem;
            margin: 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <!-- Sidebar (Same as generic) -->
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
            <h1 class="page-title">Weekly Entry Form</h1>
            <div class="user-profile">
                <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                    <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                </div>
            </div>
        </header>

        <?php if (!$hasActiveAttachment): ?>
            <div class="empty-state text-center py-12 bg-white rounded-lg shadow-sm">
                <i class="fas fa-lock" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #334155; margin-bottom: 8px;">Logbook Locked</h3>
                <p style="color: #64748b; margin-bottom: 24px;">You need an active attachment placement to access the logbook.</p>
                <a href="../Opportunities/student-opportunities.php" style="background-color: #8B1538; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">Find a Placement</a>
            </div>
        <?php else: ?>
            <div class="logbook-wrapper">
                <form id="logbookForm" action="process-logbook-entry.php" method="POST">
                    <input type="hidden" name="logbook_id" value="<?php echo $logbookId; ?>">
                    <input type="hidden" name="week_ending" value="<?php echo $currentWeekEnding; ?>">

                    <div class="logbook-header-section">
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Weekly Logbook Entry</h2>
                            <p style="color: #64748b; font-size: 0.9rem;">
                                <i class="far fa-calendar"></i> &nbsp; Week Ending: <?php echo date('M j, Y', strtotime($currentWeekEnding)); ?>
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div class="week-selector">
                                <label for="weekPicker">Select Week:</label>
                                <input type="date" id="weekPicker" value="<?php echo $currentWeekEnding; ?>" onchange="changeWeek(this.value)">
                            </div>
                            <span class="status-badge">Draft</span>
                        </div>
                    </div>

                    <table class="entries-grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Task Assigned</th>
                                <th>Student Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                            $weekEndTimestamp = strtotime($currentWeekEnding);
                            // Assuming currentWeekEnding is Friday
                            // Monday is -4 days from Friday
                            ?>
                            <?php foreach ($days as $index => $day): 
                                $dayOffset = $index - 4; // Mon=-4, Fri=0
                                $date = date('M j', strtotime("$dayOffset days", $weekEndTimestamp));
                                $fullDate = date('D, M j', strtotime("$dayOffset days", $weekEndTimestamp));
                            ?>
                            <tr>
                                <td class="day-column">
                                    <?php echo $fullDate; ?>
                                </td>
                                <td class="input-column">
                                    <textarea name="tasks[<?php echo $day; ?>]" class="log-input" placeholder="Describe the specific tasks assigned today..."><?php echo htmlspecialchars($activities[$day]['task'] ?? ''); ?></textarea>
                                </td>
                                <td class="input-column">
                                    <textarea name="comments[<?php echo $day; ?>]" class="log-input" placeholder="Reflections on learning outcomes or challenges..."><?php echo htmlspecialchars($activities[$day]['comment'] ?? ''); ?></textarea>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- Feedback Section (Read Only) -->
                    <div class="feedback-section">
                        <div class="feedback-box">
                            <div class="feedback-title"><i class="fas fa-user-tie"></i> Host Supervisor Feedback</div>
                            <?php if (!empty($logbookEntry['HostSupervisorComments'])): ?>
                                <p class="feedback-content"><?php echo nl2br(htmlspecialchars($logbookEntry['HostSupervisorComments'])); ?></p>
                            <?php else: ?>
                                <p style="font-size: 0.85rem;">Feedback will appear here after review.</p>
                            <?php endif; ?>
                        </div>
                        <div class="feedback-box">
                            <div class="feedback-title"><i class="fas fa-chalkboard-teacher"></i> Lecturer Remarks</div>
                            <!-- Lecturer remarks logic would go here if column existed, placeholder for now -->
                             <p style="font-size: 0.85rem;">Assessment feedback will appear here.</p>
                        </div>
                    </div>

                    <div class="action-bar">
                        <span class="last-saved">
                            <?php if ($logbookEntry): ?>
                                Last saved: <?php echo date('h:i A', strtotime($logbookEntry['EntryDate'])); // Using EntryDate as visual proxy for now ?>
                            <?php else: ?>
                                Not saved yet
                            <?php endif; ?>
                        </span>
                        <div style="display: flex; gap: 12px;">
                            <!-- <button type="button" class="btn-secondary" style="background: white; border: 1px solid #cbd5e1; padding: 12px 24px; border-radius: 6px; font-weight: 600; cursor: pointer;">Save Draft</button> -->
                            <button type="submit" class="btn-submit">
                                Save Entry <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="submission-policy">
                <div class="policy-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="policy-text">
                    <h4>Submission Policy</h4>
                    <p>Logbook entries are a critical component of your assessment. Please ensure all 'Tasks Assigned' and 'Student Comments' are detailed and professional. Weekly submissions must be finalized by Sunday midnight.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function changeWeek(date) {
            window.location.href = '?week=' + date;
        }

        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    </script>
</body>
</html>
<?php if(isset($conn)) $conn->close(); ?>

