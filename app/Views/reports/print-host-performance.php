<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Host Performance Report</title>
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
            <div class="header-title">Host Organization Performance Report</div>
        </div>
        <hr>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th style="text-align: center;">Week</th>
                    <th>Date</th>
                    <th>Host Supervisor Comments / Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($performance && $performance->num_rows > 0): ?>
                    <?php while($row = $performance->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                            <td style="text-align: center;"><?= $row['WeekNumber'] ?></td>
                            <td><?= date('d M Y', strtotime($row['StartDate'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['HostSupervisorComments'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center;">No supervisor comments found for this host.</td></tr>
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
