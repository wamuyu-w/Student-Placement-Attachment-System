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
    <title>Assessment Summary - <?= htmlspecialchars($student['FirstName']) ?></title>
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
                <h2>Attachment Assessment Summary</h2>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Student Name:</td><td class="info-value"><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></td>
                <td class="info-label" style="padding-left: 20px;">Admission No:</td><td class="info-value"><?= htmlspecialchars($student['AdmissionNumber']) ?></td>
            </tr>
            <tr>
                <td class="info-label">Course:</td><td class="info-value"><?= htmlspecialchars($student['Course']) ?></td>
                <td class="info-label" style="padding-left: 20px;">Faculty:</td><td class="info-value"><?= htmlspecialchars($student['Faculty']) ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Assessment Type</th>
                    <th>Assessor</th>
                    <th style="text-align: center;">Marks Awarded</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($assessments && $assessments->num_rows > 0): ?>
                    <?php while($row = $assessments->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['AssessmentDate'])) ?></td>
                            <td><?= htmlspecialchars($row['AssessmentType']) ?></td>
                            <td><?= htmlspecialchars($row['AssessorName'] ?? 'N/A') ?></td>
                            <td style="font-weight: bold; text-align: center;"><?= number_format($row['Marks'], 1) ?> / 100</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center;">No assessments recorded.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="footer-signatures" style="margin-top: 100px;">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Registrar / Faculty Dean</strong></div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Official Stamp</strong></div>
            </div>
        </div>
    </div>
</body>
</html>
