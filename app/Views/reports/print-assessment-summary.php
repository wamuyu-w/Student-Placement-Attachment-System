<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment Summary Report</title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report / Save PDF</button>

    <div class="report-container">
        <div class="report-header">
            <div class="logo-container">
                <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
            </div>
            <div class="header-text">
                <h1>The Catholic University of Eastern Africa</h1>
                <h2>Student Assessment Summary Report</h2>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 10pt;">Date Generated: <?= date('d M Y') ?></p>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Admission No.</th>
                    <th style="text-align: center;">1st Assessment</th>
                    <th style="text-align: center;">2nd Assessment</th>
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
                            <td style="text-align: center;"><?= $row['SecondScore'] !== null ? $row['SecondScore'] . '%' : '-' ?></td>
                            <td style="text-align: center; font-weight: bold;">
                                <?= $row['AverageScore'] !== null ? number_format($row['AverageScore'], 1) . '%' : '-' ?>
                            </td>
                            <td><?= htmlspecialchars($row['AttachmentStatus']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">No assessment records found.</td></tr>
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
