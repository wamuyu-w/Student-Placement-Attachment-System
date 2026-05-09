<?php
use App\Core\Helpers;
$r = $report;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Placement Completions Report — CUEA</title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
    <style>
        @page { size: A4 landscape; }
    </style>
</head>
<body>
<button class="no-print" onclick="window.print()">Print / Save PDF</button>
<div class="report-container">


<!-- Header -->
<div class="header">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
            <h1>The Catholic University of Eastern Africa</h1>
            <div class="header-motto">"Consecrate them in the Truth"</div>
            <div class="header-title">Placement Completions Report</div>
        </div>
        <hr>

<!-- KPI Row -->
<div class="kpi-row">
    <div class="kpi-box">
        <div class="num"><?= $r['total_completed'] ?></div>
        <div class="lbl">Completed</div>
    </div>
    <div class="kpi-box">
        <div class="num"><?= $r['total_ongoing'] ?></div>
        <div class="lbl">Ongoing</div>
    </div>
    <div class="kpi-box">
        <div class="num"><?= $r['avg_final'] ?>%</div>
        <div class="lbl">Avg Final Score</div>
    </div>
</div>

<!-- Table -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Adm. No.</th>
            <th>Course</th>
            <th>Host Organization</th>
            <th>Supervisor</th>
            <th class="center">Start</th>
            <th class="center">End</th>
            <th class="center">Wks ✓</th>
            <th class="center">Final</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $students = $r['students'];
    $i = 0;
    if ($students && $students->num_rows > 0):
        while ($row = $students->fetch_assoc()):
            $i++;
    ?>
    <tr>
        <td style=";"><?= $i ?></td>
        <td style="font-weight:700;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
        <td style="font-family: monospace;"><?= htmlspecialchars($row['AdmNumber']) ?></td>
        <td style="font-size:7.5pt; max-width: 110px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"><?= htmlspecialchars($row['Course'] ?? '—') ?></td>
        <td style="font-size:7.5pt; max-width: 120px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"><?= htmlspecialchars($row['OrganizationName']) ?></td>
        <td style="font-size:7.5pt;"><?= htmlspecialchars($row['SupervisorName'] ?? '—') ?></td>
        <td class="center"><?= date('d/m/Y', strtotime($row['StartDate'])) ?></td>
        <td class="center"><?= date('d/m/Y', strtotime($row['EndDate'])) ?></td>
        <td class="center"><span class="badge badge-approved"><?= $row['ApprovedWeeks'] ?>/12</span></td>
        <td class="center <?= $row['FinalScore'] >= 80 ? 'score-hi' : ($row['FinalScore'] >= 70 ? 'score-mid' : 'score-low') ?>"><?= $row['FinalScore'] !== null ? $row['FinalScore'].'%' : '—' ?></td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="10" style="text-align:center; ; padding:16px;">No records found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>


</div>
</body>
</html>
