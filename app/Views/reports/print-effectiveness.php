<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Effectiveness Report</title>
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
                <h2>System Effectiveness & Impact Report</h2>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 10pt;">Date Generated: <?= date('d M Y') ?></p>
            </div>
        </div>

        <h3 style="border-bottom: 1px solid #8B1538; padding-bottom: 5px;">Placements Overview</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                    <th>Sub-Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Placement Rate</strong></td>
                    <td><?= $stats['totalStudents'] > 0 ? round(($stats['placedStudents'] / $stats['totalStudents']) * 100, 1) : 0 ?>%</td>
                    <td><?= $stats['placedStudents'] ?> Placed / <?= $stats['totalStudents'] ?> Total Students</td>
                </tr>
                <tr>
                    <td><strong>Attachment Opportunities</strong></td>
                    <td><?= $stats['totalOpportunities'] ?></td>
                    <td>Total Listings in Database</td>
                </tr>
                <tr>
                    <td><strong>Program Completions</strong></td>
                    <td><?= $stats['completedAttachments'] ?></td>
                    <td>Verified Student Completions</td>
                </tr>
            </tbody>
        </table>

        <h3 style="border-bottom: 1px solid #8B1538; padding-bottom: 5px; margin-top: 30px;">Lecturer Assessment Performance</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lecturer Name</th>
                    <th style="text-align: center;">Students Assessed</th>
                    <th style="text-align: center;">Average Grade Given</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($lecturerStats && $lecturerStats->num_rows > 0): ?>
                    <?php while($row = $lecturerStats->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Name']) ?></td>
                            <td style="text-align: center;"><?= $row['students_assessed'] ?></td>
                            <td style="text-align: center;"><?= number_format($row['avg_marks_given'], 1) ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center;">No assessment data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-signatures" style="margin-top: 80px;">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Quality Assurance Officer</strong></div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Official Stamp</strong></div>
            </div>
        </div>
    </div>
</body>
</html>
