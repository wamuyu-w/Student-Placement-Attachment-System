<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment Summary Report</title>
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
            <div class="header-title">Student Assessment Summary Report</div>
        </div>
        <hr>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Admission No.</th>
                    <th style="text-align: center;">1st Assessment</th>
                    <th style="text-align: left;">1st Assessor</th>
                    <th style="text-align: center;">2nd Assessment</th>
                    <th style="text-align: left;">2nd Assessor</th>
                    <th style="text-align: center;">Average Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($summary && $summary->num_rows > 0): ?>
                    <?php while($row = $summary->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstName']) ?></td>
                            <td><?= htmlspecialchars($row['AdmNumber']) ?></td>
                            <td style="text-align: center;"><?= $row['FirstScore'] !== null ? $row['FirstScore'] . '%' : '-' ?></td>
                            <td style="font-size: 0.9em;"><?= htmlspecialchars($row['FirstAssessor'] ?? '-') ?></td>
                            <td style="text-align: center;"><?= $row['SecondScore'] !== null ? $row['SecondScore'] . '%' : '-' ?></td>
                            <td style="font-size: 0.9em;"><?= htmlspecialchars($row['SecondAssessor'] ?? '-') ?></td>
                            <td style="text-align: center; font-weight: bold;">
                                <?= $row['AverageScore'] !== null ? number_format($row['AverageScore'], 1) . '%' : '-' ?>
                            </td>
                            <td><?= htmlspecialchars($row['AttachmentStatus']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align: center;">No assessment records found.</td></tr>
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
