<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

// Get Active Attachment and Supervisor
$sql = "SELECT l.Name, l.Department, l.Faculty, l.Role, u.Username as Email, a.AttachmentID
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
            <h1 class="page-title">My Supervisor</h1>
            <div class="header-actions">
                <div class="user-profile">
                     <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Custom CSS for the new design -->
        <style>
            :root {
                --primary-color: #8B1538;
                --bg-color: #f3f4f6;
                --card-bg: #ffffff;
                --text-main: #1f2937;
                --text-muted: #6b7280;
                --accent-blue: #2563eb;
                --success-green: #10b981;
                --status-pending: #9ca3af;
            }
            
            body { 
                background-color: var(--bg-color); 
                font-family: 'Inter', apple-system, sans-serif;
            }

            .main-content {
                padding: 2rem;
            }

            /* Layout Grid */
            .dashboard-grid {
                display: grid;
                grid-template-columns: 1fr 1.3fr;
                gap: 1.5rem;
                margin-top: 1.5rem;
            }
            
            @media (max-width: 1024px) {
                .dashboard-grid { grid-template-columns: 1fr; }
            }

            /* Cards */
            .custom-card {
                background: var(--card-bg);
                border-radius: 16px;
                padding: 1.5rem;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.02);
            }

            /* Supervisor Profile Card (Top) */
            .supervisor-profile-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 2rem;
            }
            .profile-left {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }
            .supervisor-avatar {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #e5e7eb;
            }
            .supervisor-info h2 {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--text-main);
                margin: 0 0 0.25rem 0;
            }
            .supervisor-info p {
                color: var(--accent-blue);
                font-weight: 500;
                margin: 0 0 0.25rem 0;
                font-size: 0.95rem;
            }
            .supervisor-info span {
                color: var(--text-muted);
                font-size: 0.875rem;
                display: block;
            }
            .btn-email {
                background-color: var(--accent-blue);
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: background 0.2s;
                border: none;
                cursor: pointer;
            }
            .btn-email:hover {
                background-color: #1d4ed8;
            }

            /* Section Titles */
            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
            }
            .section-title {
                font-size: 1.1rem;
                font-weight: 700;
                color: var(--text-main);
                margin: 0;
            }
            .view-all-link {
                color: var(--accent-blue);
                font-size: 0.875rem;
                font-weight: 600;
                text-decoration: none;
            }

            /* Timeline (Left Column) */
            .timeline {
                position: relative;
                padding-left: 1rem;
            }
            /* Vertical Line */
            .timeline::before {
                content: '';
                position: absolute;
                left: 24px;
                top: 10px;
                bottom: 0;
                width: 2px;
                z-index: 0;
            }
            .timeline-item {
                display: flex;
                gap: 1.5rem;
                margin-bottom: 2rem;
                position: relative;
                z-index: 1;
            }
            .timeline-item:last-child { margin-bottom: 0; }
            
            .timeline-icon {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: white;
                border: 2px solid;
            }
            .status-completed {
                border-color: var(--success-green);
                color: var(--success-green);
                background: #ecfdf5;
            }
            .status-scheduled {
                border-color: var(--accent-blue);
                color: var(--accent-blue);
                background: #eff6ff;
            }
            .status-pending {
                border-color: #d1d5db;
                color: #9ca3af;
                background: #f9fafb;
            }
            
            .timeline-content h4 {
                font-weight: 700;
                color: var(--text-main);
                margin: 0 0 0.25rem 0;
                font-size: 1rem;
            }
            .timeline-date {
                font-size: 0.85rem;
                color: var(--text-muted);
                display: block;
                margin-bottom: 0.5rem;
            }
            .status-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 0.75rem;
                font-weight: 600;
            }
            .badge-green { background: #dcfce7; color: #166534; }
            .badge-blue { background: #dbeafe; color: #1e40af; }
            .badge-gray { background: #f3f4f6; color: #4b5563; }

            /* Feedback Cards (Right Column) */
            .feedback-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 1.25rem;
                margin-bottom: 1rem;
                transition: transform 0.2s;
            }
            .feedback-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            }
            .feedback-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.75rem;
            }
            .feedback-date {
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .feedback-status {
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
            }
            .status-satisfactory { background: #dcfce7; color: #15803d; }
            .status-reviewed { background: #dbeafe; color: #1d4ed8; }
            .status-attention { background: #fee2e2; color: #b91c1c; }

            .feedback-title {
                font-weight: 700;
                color: var(--text-main);
                margin-bottom: 0.5rem;
                font-size: 1rem;
            }
            .feedback-text {
                color: #4b5563;
                font-size: 0.9rem;
                line-height: 1.5;
                margin-bottom: 1rem;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .read-more {
                color: var(--accent-blue);
                font-size: 0.85rem;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            .read-more:hover { text-decoration: underline; }

        </style>

        <div class="content">
            <?php if (!$supervisor): ?>
                 <div class="empty-state text-center py-12 bg-white rounded-lg shadow-sm">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-user-clock text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Supervisor Assigned</h3>
                    <p class="text-gray-500">You will be assigned an academic supervisor once your attachment is confirmed.</p>
                </div>
            <?php else: ?>
                
                <!-- 1. Supervisor Profile Card (Full Width) -->
                <div class="custom-card supervisor-profile-card">
                    <div class="profile-left">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($supervisor['Name']); ?>&background=random&size=128" alt="Dr. Profile" class="supervisor-avatar">
                        <div class="supervisor-info">
                            <h2><?php echo htmlspecialchars($supervisor['Name']); ?></h2>
                            <p><?php echo htmlspecialchars($supervisor['Department']); ?></p>
                            <span><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($supervisor['Role']); ?> & Academic Supervisor</span>
                        </div>
                    </div>
                    <a href="mailto:<?php echo htmlspecialchars($supervisor['Username']); ?>" class="btn-email">
                        <i class="fas fa-envelope"></i> Email Supervisor
                    </a>
                </div>

                <!-- 2. Main Grid: Tracker & Feedback -->
                <div class="dashboard-grid">
                    
                    <!-- Left: Supervision Progress Tracker -->
                    <div class="custom-card">
                        <div class="section-header">
                            <h3 class="section-title"><i class="fas fa-chart-line" style="color: var(--accent-blue); margin-right: 8px;"></i> Supervision Progress Tracker</h3>
                        </div>

                        <div class="timeline">
                            <?php
                            // Fetch assessments to determine progress
                            $assessSql = "SELECT * FROM assessment WHERE AttachmentID = ?";
                            $stmt = $conn->prepare($assessSql);
                            $stmt->bind_param("i", $supervisor['AttachmentID']);
                            $stmt->execute();
                            $assessRes = $stmt->get_result();
                            $assessments = [];
                            while ($a = $assessRes->fetch_assoc()) {
                                $assessments[$a['AssessmentType']] = $a;
                            }

                            // Define milestones
                            $milestones = [
                                ['type' => 'First Assessment', 'label' => 'First Assessment', 'icon' => 'fa-calendar-check'],
                                ['type' => 'Final Assessment', 'label' => 'Final Assessment', 'icon' => 'fa-flag-checkered']
                            ];

                            foreach ($milestones as $m): 
                                $isCompleted = isset($assessments[$m['type']]);
                                $data = $assessments[$m['type']] ?? null;
                                // Logic: If it exists, it's completed/scheduled. If it has marks, it's definitely completed.
                                $statusClass = $isCompleted ? ($data['Marks'] ? 'status-completed' : 'status-scheduled') : 'status-pending';
                                // Hack: Initial Visit is usually implied or manual, let's assume it's completed if any assessment exists
                                if ($m['type'] == 'First Assessment' && !empty($assessments)) {
                                    $isCompleted = true;
                                    $statusClass = 'status-completed';
                                    $data = ['AssessmentDate' => $assessments[array_key_first($assessments)]['AssessmentDate']]; // Use first date
                                }
                            ?>
                                <div class="timeline-item">
                                    <div class="timeline-icon <?php echo $statusClass; ?>">
                                        <i class="fas <?php echo $m['icon']; ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4><?php echo $m['label']; ?> <?php echo $isCompleted ? ($data['Marks'] ? '(Completed)' : '(Scheduled)') : '(Pending)'; ?></h4>
                                        <?php if ($isCompleted && isset($data['AssessmentDate'])): ?>
                                            <span class="timeline-date"><i class="far fa-calendar"></i> <?php echo date('F j, Y', strtotime($data['AssessmentDate'])); ?></span>
                                        <?php else: ?>
                                            <span class="timeline-date">Date to be determined</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($isCompleted && isset($data['Marks'])): ?>
                                            <span class="status-badge badge-green">Score: <?php echo $data['Marks']; ?>/100</span>
                                        <?php elseif ($isCompleted): ?>
                                            <span class="status-badge badge-blue">Scheduled</span>
                                        <?php else: ?>
                                            <span class="status-badge badge-gray">Pending</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Right: Feedback History -->
                    <div class="custom-card">
                        <div class="section-header">
                            <h3 class="section-title">Feedback History</h3>
                            <a href="../Logbook/student-logbook.php" class="view-all-link">View All</a>
                        </div>

                        <?php
                        // Combine Logbook Feedback and Assessment Remarks
                        $feedbackItems = [];

                        // 1. Get Logbook Feedback
                        $logbookSql = "SELECT le.EntryDate, le.Activities, le.HostSupervisorComments, 'Logbook' as Type 
                                       FROM logbookentry le
                                       JOIN logbook l ON le.LogbookID = l.LogbookID
                                       WHERE l.AttachmentID = ? AND le.HostSupervisorComments IS NOT NULL AND le.HostSupervisorComments != ''
                                       limit 5";
                        $stmt = $conn->prepare($logbookSql);
                        $stmt->bind_param("i", $supervisor['AttachmentID']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($row = $res->fetch_assoc()) {
                            $feedbackItems[] = [
                                'date' => $row['EntryDate'],
                                'title' => 'Weekly Logbook Review',
                                'content' => $row['HostSupervisorComments'],
                                'status' => 'Satisfactory', // Assumed
                                'type' => 'logbook'
                            ];
                        }

                        // 2. Get Assessment Remarks
                        foreach ($assessments as $a) {
                            if (!empty($a['Remarks'])) {
                                $feedbackItems[] = [
                                    'date' => $a['AssessmentDate'],
                                    'title' => $a['AssessmentType'] . ' Feedback',
                                    'content' => $a['Remarks'],
                                    'status' => 'Reviewed',
                                    'type' => 'assessment'
                                ];
                            }
                        }

                        // Sort by date DESC
                        usort($feedbackItems, function($a, $b) {
                            return strtotime($b['date']) - strtotime($a['date']);
                        });

                        if (empty($feedbackItems)):
                        ?>
                            <div class="text-center py-8 text-gray-500">
                                <p>No feedback recorded yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($feedbackItems as $item): ?>
                                <div class="feedback-card">
                                    <div class="feedback-header">
                                        <span class="feedback-date"><?php echo date('M d, Y', strtotime($item['date'])); ?></span>
                                        <span class="feedback-status <?php echo ($item['status'] == 'Satisfactory' ? 'status-satisfactory' : 'status-reviewed'); ?>">
                                            <?php echo $item['status']; ?>
                                        </span>
                                    </div>
                                    <h4 class="feedback-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p class="feedback-text"><?php echo htmlspecialchars($item['content']); ?></p>
                                    <a href="<?php echo $item['type'] == 'logbook' ? '../Logbook/student-logbook.php' : '#'; ?>" class="read-more">
                                        <i class="far fa-eye"></i> Read more
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

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
