<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Statistics Report</title>
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
                <h2>Supervisor Workload Statistics</h2>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 10pt;">Date Generated: <?= date('d M Y') ?></p>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Lecturer Name</th>
                    <th>Department</th>
                    <th style="text-align: center;">Assigned Students</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stats && $stats->num_rows > 0): ?>
                    <?php while($row = $stats->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['Name']) ?></td>
                            <td><?= htmlspecialchars($row['Department'] ?? 'N/A') ?></td>
                            <td style="text-align: center;">
                                <strong><?= $row['student_count'] ?></strong>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center;">No supervisor data found.</td></tr>
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
