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
<div class="report-container">
<button class="no-print" onclick="window.print()"><i>&#128438;</i> Print / Save PDF</button>

<!-- Header -->
<div class="header">
    <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA">
    <div class="header-text">
        <h1>Placement Completions Report</h1>
        <p>The Catholic University of Eastern Africa &mdash; Industrial Attachment Programme</p>
        <p>Generated: <?= date('d F Y, H:i') ?> EAT</p>
    </div>
</div>

<!-- KPI Row -->
<div class="kpi-row">
    <div class="kpi-box">
        <div class="num"><?= $r['total_completed'] ?></div>
        <div class="lbl">Completed</div>
    </div>
    <div class="kpi-box">
        <div class="num"><?= $r['total_cleared'] ?></div>
        <div class="lbl">Cleared</div>
    </div>
    <div class="kpi-box">
        <div class="num"><?= $r['total_ongoing'] ?></div>
        <div class="lbl">Ongoing</div>
    </div>
    <div class="kpi-box">
        <div class="num"><?= $r['reports_approved'] ?></div>
        <div class="lbl">Reports Approved</div>
    </div>
    <div class="kpi-box">
        <div class="num"><?= $r['avg_first'] ?>%</div>
        <div class="lbl">Avg 1st Score</div>
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
            <th class="center">1st</th>
            <th class="center">Final</th>
            <th class="center">Avg</th>
            <th class="center">Report</th>
            <th class="center">Clearance</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $students = $r['students'];
    $i = 0;
    if ($students && $students->num_rows > 0):
        while ($row = $students->fetch_assoc()):
            $i++;
            $avg = $row['AvgScore'] !== null ? number_format($row['AvgScore'],1) : '—';
            $avgClass = '';
            if ($row['AvgScore'] !== null) {
                if ($row['AvgScore'] >= 80) $avgClass = 'score-hi';
                elseif ($row['AvgScore'] >= 70) $avgClass = 'score-mid';
                else $avgClass = 'score-low';
            }
            $cleared = $row['ClearanceStatus'] === 'Cleared';
            $rStatus = $row['ReportStatus'] ?? null;
    ?>
    <tr>
        <td style="color:#9ca3af;"><?= $i ?></td>
        <td style="font-weight:700;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
        <td style="font-family: monospace;"><?= htmlspecialchars($row['AdmNumber']) ?></td>
        <td style="font-size:7.5pt; max-width: 110px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"><?= htmlspecialchars($row['Course'] ?? '—') ?></td>
        <td style="font-size:7.5pt; max-width: 120px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"><?= htmlspecialchars($row['OrganizationName']) ?></td>
        <td style="font-size:7.5pt;"><?= htmlspecialchars($row['SupervisorName'] ?? '—') ?></td>
        <td class="center"><?= date('d/m/Y', strtotime($row['StartDate'])) ?></td>
        <td class="center"><?= date('d/m/Y', strtotime($row['EndDate'])) ?></td>
        <td class="center"><span class="badge badge-approved"><?= $row['ApprovedWeeks'] ?>/12</span></td>
        <td class="center <?= $row['FirstScore'] >= 80 ? 'score-hi' : ($row['FirstScore'] >= 70 ? 'score-mid' : 'score-low') ?>"><?= $row['FirstScore'] !== null ? $row['FirstScore'].'%' : '—' ?></td>
        <td class="center <?= $row['FinalScore'] >= 80 ? 'score-hi' : ($row['FinalScore'] >= 70 ? 'score-mid' : 'score-low') ?>"><?= $row['FinalScore'] !== null ? $row['FinalScore'].'%' : '—' ?></td>
        <td class="center <?= $avgClass ?>"><?= $avg !== '—' ? $avg . '%' : '—' ?></td>
        <td class="center">
            <?php if ($rStatus): ?>
                <span class="badge badge-<?= strtolower(str_replace(' ','',($rStatus === 'Approved' ? 'approved' : 'submitted'))) ?>"><?= $rStatus ?></span>
            <?php else: ?>
                <span class="badge badge-none">None</span>
            <?php endif; ?>
        </td>
        <td class="center">
            <span class="badge <?= $cleared ? 'badge-cleared' : 'badge-notcleared' ?>">
                <?= $cleared ? '&#10003; Cleared' : '&#9679; Not Cleared' ?>
            </span>
        </td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="14" style="text-align:center; color:#6b7280; padding:16px;">No records found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    Placement Completions Report &mdash; The Catholic University of Eastern Africa &mdash; Generated <?= date('d F Y') ?> &mdash; CONFIDENTIAL
</div>
</div>
</body>
</html>
