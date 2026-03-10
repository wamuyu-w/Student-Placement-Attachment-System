<?php 
use App\Core\Helpers; 
if (!isset($student) || empty($student)) {
    echo "<div style='text-align:center; padding:20px; font-family:sans-serif;'>Student data not found or invalid request.</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Logbook - Week <?= htmlspecialchars($logbook['WeekNumber']) ?></title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>

    <div class="report-container">
        <div class="report-header">
            <div class="logo-container">
                <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
            </div>
            <div class="header-text">
                <h1>The Catholic University of Eastern Africa</h1>
                <h2>Weekly Attachment Logbook Entry</h2>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Student Name:</td><td class="info-value"><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></td>
                <td class="info-label" style="padding-left: 20px;">Week Number:</td><td class="info-value"><?= htmlspecialchars($logbook['WeekNumber']) ?></td>
            </tr>
            <tr>
                <td class="info-label">Course:</td><td class="info-value"><?= htmlspecialchars($student['Course']) ?></td>
                <td class="info-label" style="padding-left: 20px;">Dates:</td><td class="info-value"><?= date('d M', strtotime($logbook['StartDate'])) ?> - <?= date('d M, Y', strtotime($logbook['EndDate'])) ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Day</th>
                    <th style="width: 42.5%;">Task Assigned</th>
                    <th style="width: 42.5%;">Student Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $daysMapping = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday'];
                foreach($daysMapping as $key => $label): 
                    $task = $weeklyData[$key]['task'] ?? '';
                    $comment = $weeklyData[$key]['comment'] ?? '';
                ?>
                <tr>
                    <td style="font-weight: bold; background: #f8f9fa;"><?= $label ?></td>
                    <td><?= nl2br(htmlspecialchars($task)) ?></td>
                    <td><?= nl2br(htmlspecialchars($comment)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
            <div>
                <strong>Host Supervisor Feedback:</strong>
                <div style="border: 1px solid #ddd; padding: 15px; min-height: 100px; margin-top: 10px; background: #fff; border-radius: 4px; font-size: 10pt;">
                    <?= !empty($logbook['HostSupervisorComments']) ? nl2br(htmlspecialchars($logbook['HostSupervisorComments'])) : 'No feedback provided.' ?>
                </div>
            </div>
            <div>
                <strong>Academic Supervisor Remarks:</strong>
                <div style="border: 1px solid #ddd; padding: 15px; min-height: 100px; margin-top: 10px; background: #fff; border-radius: 4px; font-size: 10pt;">
                    <?= !empty($logbook['AcademicSupervisorComments']) ? nl2br(htmlspecialchars($logbook['AcademicSupervisorComments'])) : 'No remarks provided.' ?>
                </div>
            </div>
        </div>

        <div class="footer-signatures" style="margin-top: 60px;">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Host Supervisor Signature</strong></div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>University Supervisor Signature</strong></div>
            </div>
        </div>
    </div>
</body>
</html>
