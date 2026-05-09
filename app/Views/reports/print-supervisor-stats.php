<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Statistics Report</title>
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
            <div class="header-title">Supervisor Workload Statistics</div>
        </div>
        <hr>

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
