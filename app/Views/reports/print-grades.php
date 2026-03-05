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
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0 0; font-size: 14pt; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11pt; }
        .info-table td { padding: 5px; }
        .grades-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .grades-table th, .grades-table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .grades-table th { background-color: #f0f0f0; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #8B1538; color: #fff; border: none; padding: 10px 20px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>

    <div class="header">
        <h1>The Catholic University of Eastern Africa</h1>
        <h2>Attachment Assessment Summary</h2>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Name:</strong> <?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></td>
            <td><strong>Reg. No:</strong> <?= htmlspecialchars($student['AdmissionNumber']) ?></td>
        </tr>
        <tr>
            <td><strong>Course:</strong> <?= htmlspecialchars($student['Course']) ?></td>
            <td><strong>Faculty:</strong> <?= htmlspecialchars($student['Faculty']) ?></td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Assessment Type</th>
                <th>Assessor</th>
                <th>Marks Awarded</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($assessments && $assessments->num_rows > 0): ?>
                <?php while($row = $assessments->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['AssessmentDate'])) ?></td>
                        <td><?= htmlspecialchars($row['AssessmentType']) ?></td>
                        <td><?= htmlspecialchars($row['AssessorName'] ?? 'N/A') ?></td>
                        <td style="font-weight: bold;"><?= number_format($row['Marks'], 1) ?> / 100</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center;">No assessments recorded.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
