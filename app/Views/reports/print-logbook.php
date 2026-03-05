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
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0 0; font-size: 14pt; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11pt; }
        .info-table td { padding: 5px; }
        .entry-block { border: 1px solid #000; margin-bottom: 15px; page-break-inside: avoid; }
        .entry-header { background-color: #f0f0f0; border-bottom: 1px solid #000; padding: 5px 10px; font-weight: bold; display: flex; justify-content: space-between; }
        .entry-content { padding: 10px; min-height: 50px; }
        .entry-feedback { border-top: 1px dotted #000; padding: 5px 10px; font-size: 10pt; font-style: italic; background: #fafafa; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #8B1538; color: #fff; border: none; padding: 10px 20px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>

    <div class="header">
        <h1>The Catholic University of Eastern Africa</h1>
        <h2>Student Attachment Logbook</h2>
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

    <?php if ($entries && $entries->num_rows > 0): ?>
        <?php while($row = $entries->fetch_assoc()): ?>
            <div class="entry-block">
                <div class="entry-header">
                    <span>Week <?= htmlspecialchars($row['WeekNumber']) ?></span>
                    <span><?= date('d M Y', strtotime($row['StartDate'])) ?> - <?= date('d M Y', strtotime($row['EndDate'])) ?></span>
                </div>
                <div class="entry-content">
                    <?= nl2br(htmlspecialchars($row['Description'])) ?>
                </div>
                <?php if (!empty($row['AcademicSupervisorComments']) || !empty($row['HostSupervisorComments'])): ?>
                    <div class="entry-feedback">
                        <?php if ($row['AcademicSupervisorComments']): ?>
                            <div><strong>Lecturer:</strong> <?= htmlspecialchars($row['AcademicSupervisorComments']) ?></div>
                        <?php endif; ?>
                        <?php if ($row['HostSupervisorComments']): ?>
                            <div><strong>Host:</strong> <?= htmlspecialchars($row['HostSupervisorComments']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; font-style: italic;">No logbook entries found.</p>
    <?php endif; ?>
</body>
</html>
