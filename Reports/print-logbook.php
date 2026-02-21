<?php
require_once '../config.php';
requireLogin('admin'); // Assuming this report is primarily for admin, or can adjust if staff/host-org need it.

$conn = getDBConnection();
$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    die("Student ID is required parameter.");
}

// Fetch student details
$stmt = $conn->prepare("
    SELECT 
        s.FirstName, 
        s.LastName, 
        u.Username as AdmissionNumber, 
        s.Course, 
        a.AttachmentID, 
        a.StartDate,
        h.OrganizationName
    FROM student s
    JOIN users u ON s.UserID = u.UserID
    JOIN attachment a ON s.StudentID = a.StudentID
    LEFT JOIN hostorganization h ON a.HostOrgID = h.HostOrgID
    WHERE s.StudentID = ?
    ORDER BY a.AttachmentID DESC LIMIT 1
");

$stmt->bind_param("i", $studentId);
$stmt->execute();
$studentInfo = $stmt->get_result()->fetch_assoc();

if (!$studentInfo) {
    die("No active logbook data found for this student.");
}

// Fetch logbook entries
$logStmt = $conn->prepare("
    SELECT 
        le.EntryDate, 
        le.Activities, 
        le.HostSupervisorComments, 
        le.AcademicSupervisorComments,
        CEIL(DATEDIFF(le.EntryDate, ?) / 7) as WeekNumber
    FROM logbookentry le
    JOIN logbook l ON le.LogbookID = l.LogbookID
    WHERE l.AttachmentID = ?
    ORDER BY le.EntryDate ASC
");
$logStmt->bind_param("si", $studentInfo['StartDate'], $studentInfo['AttachmentID']);
$logStmt->execute();
$logsResult = $logStmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logbook - <?php echo htmlspecialchars($studentInfo['AdmissionNumber']); ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif; /* Better for formal printing */
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 40px;
            background-color: #f3f4f6;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 60px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #8B1538;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #8B1538;
            margin: 0 0 10px 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 40px;
            background: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        .info-group {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #4b5563;
            display: block;
            font-size: 0.9em;
        }
        .info-value {
            color: #111827;
            font-size: 1.1em;
        }
        
        .logbook-entry {
            page-break-inside: avoid;
            margin-bottom: 30px;
            border: 1px solid #d1d5db;
        }
        .entry-header {
            background-color: #f3f4f6;
            padding: 10px 15px;
            border-bottom: 1px solid #d1d5db;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .entry-body {
            padding: 15px;
        }
        .entry-section {
            margin-bottom: 15px;
        }
        .entry-section:last-child {
            margin-bottom: 0;
        }
        .entry-label {
            font-weight: bold;
            color: #4b5563;
            font-size: 0.9em;
            text-transform: uppercase;
            margin-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
            display: inline-block;
        }
        .entry-content {
            margin-top: 5px;
            white-space: pre-wrap;
        }
        
        .no-data {
            text-align: center;
            font-style: italic;
            color: #6b7280;
            padding: 40px;
        }

        .print-actions {
            text-align: center;
            margin-top: 30px;
        }
        .btn-print {
            background-color: #8B1538;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .print-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            .print-actions {
                display: none;
            }
            .student-info {
                border-color: #000;
            }
            .logbook-entry {
                border-color: #000;
            }
            .entry-header {
                border-bottom-color: #000;
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="print-container">
    <div class="header">
        <h1>Catholic University of Eastern Africa</h1>
        <h2>Industrial Attachment Logbook Export</h2>
    </div>

    <div class="student-info">
        <div class="info-group">
            <span class="info-label">Student Name:</span>
            <span class="info-value"><?php echo htmlspecialchars($studentInfo['FirstName'] . ' ' . $studentInfo['LastName']); ?></span>
        </div>
        <div class="info-group">
            <span class="info-label">Admission Number:</span>
            <span class="info-value"><?php echo htmlspecialchars($studentInfo['AdmissionNumber']); ?></span>
        </div>
        <div class="info-group">
            <span class="info-label">Course:</span>
            <span class="info-value"><?php echo htmlspecialchars($studentInfo['Course']); ?></span>
        </div>
        <div class="info-group">
            <span class="info-label">Host Organization:</span>
            <span class="info-value"><?php echo htmlspecialchars($studentInfo['OrganizationName'] ?: 'Not Assigned'); ?></span>
        </div>
    </div>

    <?php if ($logsResult->num_rows > 0): ?>
        <?php while ($log = $logsResult->fetch_assoc()): ?>
            <div class="logbook-entry">
                <div class="entry-header">
                    <span>Week <?php echo htmlspecialchars($log['WeekNumber'] > 0 ? $log['WeekNumber'] : 1); ?></span>
                    <span>Date: <?php echo htmlspecialchars(date('M d, Y', strtotime($log['EntryDate']))); ?></span>
                </div>
                <div class="entry-body">
                    
                    <?php 
                    $activitiesData = json_decode($log['Activities'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($activitiesData)):
                        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day):
                            if (!empty($activitiesData[$day]['task']) || !empty($activitiesData[$day]['comment'])):
                    ?>
                                <div class="entry-section" style="border-left: 3px solid #6b7280; padding-left: 10px; margin-bottom: 20px;">
                                    <div class="entry-label" style="border: none; margin-bottom: 2px;"><?php echo $day; ?></div>
                                    
                                    <?php if (!empty($activitiesData[$day]['task'])): ?>
                                        <div style="font-size: 0.9em; margin-bottom: 4px;">
                                            <strong>Task:</strong><br>
                                            <?php 
                                            $task = trim(html_entity_decode(html_entity_decode($activitiesData[$day]['task'], ENT_QUOTES | ENT_HTML5)), "\"'");
                                            echo nl2br(htmlspecialchars($task)); 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($activitiesData[$day]['comment'])): ?>
                                        <div style="font-size: 0.9em; color: #4b5563;">
                                            <em>Reflection:</em><br>
                                            <?php 
                                            $comment = trim(html_entity_decode(html_entity_decode($activitiesData[$day]['comment'], ENT_QUOTES | ENT_HTML5)), "\"'");
                                            echo nl2br(htmlspecialchars($comment)); 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                    <?php 
                            endif;
                        endforeach;
                    else: 
                    ?>
                        <div class="entry-section">
                            <div class="entry-label">Activities</div>
                            <div class="entry-content"><?php echo nl2br(htmlspecialchars($log['Activities'])); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($log['HostSupervisorComments'])): ?>
                    <div class="entry-section" style="margin-top: 15px; border-top: 1px dashed #d1d5db; padding-top: 15px;">
                        <div style="font-size: 0.9em; font-weight: bold; color: #047857;">Host Supervisor Comments:</div>
                        <div style="font-size: 0.9em; font-style: italic; margin-top: 5px;"><?php echo nl2br(htmlspecialchars($log['HostSupervisorComments'])); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($log['AcademicSupervisorComments'])): ?>
                    <div class="entry-section" style="margin-top: 15px; border-top: 1px dashed #d1d5db; padding-top: 15px;">
                        <div style="font-size: 0.9em; font-weight: bold; color: #8B1538;">Academic Supervisor Comments:</div>
                        <div style="font-size: 0.9em; font-style: italic; margin-top: 5px;"><?php echo nl2br(htmlspecialchars($log['AcademicSupervisorComments'])); ?></div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-data">
            No logbook entries have been submitted by this student yet.
        </div>
    <?php endif; ?>

    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">
            <svg style="width:16px;height:16px;vertical-align:middle;margin-right:8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Logbook
        </button>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
