<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment Schedule</title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
</head>
<body>
    

    <button class="no-print" onclick="window.print()">Print / Save PDF</button>
<div class="report-container">
        <!-- Header -->
<div class="header">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
            <h1>The Catholic University of Eastern Africa</h1>
            <div class="header-motto">"Consecrate them in the Truth"</div>
            <div class="header-title">Attachment Assessment Schedule</div>
        </div>
        <hr>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Adm Number</th>
                    <th>Host Organization</th>
                    <th>Assigned Lecturer</th>
                    <th>Assessment Date</th>
                    <th>Assessment Type</th>
                    <th>Attachment Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($schedule && $schedule->num_rows > 0): ?>
                    <?php while($row = $schedule->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                            <td><?= htmlspecialchars($row['AdmNumber']) ?></td>
                            <td><?= htmlspecialchars($row['OrganizationName'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['LecturerName'] ?? 'Not Assigned') ?></td>
                            <td><?= $row['AssessmentDate'] ? date('d M Y', strtotime($row['AssessmentDate'])) : 'Not Scheduled' ?></td>
                            <td><?= htmlspecialchars($row['AssessmentType'] ?? 'N/A') ?></td>
                            <td><?= date('d M', strtotime($row['StartDate'])) ?> - <?= date('d M Y', strtotime($row['EndDate'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align: center;">No ongoing attachments found in the schedule.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-signatures" style="margin-top: 80px;">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Industrial Attachment Coordinator</strong></div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Official Stamp</strong></div>
            </div>
        </div>
    
</div>
</body>
</html>
