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
    <title>Logbook - <?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
    <style>
        .page-break { page-break-after: always; }
        @media print {
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>

    <?php if (!isset($entries) || empty($entries)): ?>
        <div style="text-align:center; padding:50px;">No logbook entries found for this student.</div>
    <?php else: ?>
        <?php 
        $count = count($entries);
        foreach ($entries as $index => $logbook): 
            $isLast = ($index === $count - 1);
            
            $weeklyData = [];
            if (!empty($logbook['Description'])) {
                $decoded = json_decode($logbook['Description'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $weeklyData = $decoded;
                }
            }
        ?>
        <div class="report-container <?= !$isLast ? 'page-break' : '' ?>">
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
                    <td class="info-label" style="padding-left: 20px;">Dates:</td><td class="info-value"><?= $logbook['StartDate'] ? date('d M', strtotime($logbook['StartDate'])) : 'N/A' ?> - <?= $logbook['EndDate'] ? date('d M, Y', strtotime($logbook['EndDate'])) : 'N/A' ?></td>
                </tr>
            </table>

            <table class="data-table" style="margin-top: 15px; border: 1px solid #000;">
                <thead>
                    <tr>
                        <th style="width: 12%; background-color: #f0f0f0; border: 1px solid #000; text-align: center; font-size: 11pt; padding: 12px 8px;">DAY</th>
                        <th style="width: 44%; background-color: #f0f0f0; border: 1px solid #000; font-size: 11pt; padding: 12px 8px;">DESCRIPTION OF WORK PERFORMANCE</th>
                        <th style="width: 44%; background-color: #f0f0f0; border: 1px solid #000; font-size: 11pt; padding: 12px 8px;">STUDENT'S REMARKS/CHALLENGES</th>
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
                        <td style="font-weight: bold; border: 1px solid #000; text-align: center; vertical-align: top; padding: 10px 8px; font-size: 10pt;"><?= strtoupper($label) ?></td>
                        <td style="border: 1px solid #000; vertical-align: top; padding: 10px 8px; font-size: 10pt; line-height: 1.4;"><?= !empty($task) ? nl2br(htmlspecialchars($task)) : '<span style="color: #999; font-style: italic;">No entry</span>' ?></td>
                        <td style="border: 1px solid #000; vertical-align: top; padding: 10px 8px; font-size: 10pt; line-height: 1.4;"><?= !empty($comment) ? nl2br(htmlspecialchars($comment)) : '<span style="color: #999; font-style: italic;">No entry</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 25px;">
                <div>
                    <strong style="font-size: 11pt; text-transform: uppercase;">Host Supervisor Feedback:</strong>
                    <div style="border: 1px solid #000; padding: 15px; min-height: 110px; margin-top: 8px; font-size: 10pt; line-height: 1.5;">
                        <?= !empty($logbook['HostSupervisorComments']) ? nl2br(htmlspecialchars($logbook['HostSupervisorComments'])) : '<span style="color: #666; font-style: italic;">No feedback provided yet.</span>' ?>
                    </div>
                </div>
                <div>
                    <strong style="font-size: 11pt; text-transform: uppercase;">Academic Supervisor Remarks:</strong>
                    <div style="border: 1px solid #000; padding: 15px; min-height: 110px; margin-top: 8px; font-size: 10pt; line-height: 1.5;">
                        <?= !empty($logbook['AcademicSupervisorComments']) ? nl2br(htmlspecialchars($logbook['AcademicSupervisorComments'])) : '<span style="color: #666; font-style: italic;">No remarks provided yet.</span>' ?>
                    </div>
                </div>
            </div>

            <div class="footer-signatures" style="margin-top: 50px; display: flex; justify-content: space-between; padding: 0 20px;">
                <div class="sig-block" style="width: 40%; text-align: center;">
                    <div class="sig-line" style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                    <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">Host Supervisor Signature & Stamp</div>
                </div>
                <div class="sig-block" style="width: 40%; text-align: center;">
                    <div class="sig-line" style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                    <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">University Supervisor Signature</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
