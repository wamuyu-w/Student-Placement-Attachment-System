<?php
use App\Core\Helpers;
$d = $impact;
$totalStudents = $d['total_students'];
$placementRate = $totalStudents > 0 ? round(($d['placed_students'] / $totalStudents) * 100, 1) : 0;
$totalApps     = $d['total_applications'] ?: 1;
$acceptRate    = round(($d['accepted_applications'] / $totalApps) * 100, 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Placement Impact Analysis — CUEA</title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
</head>
<body>
<div class="report-container">
<button class="no-print" onclick="window.print()">&#128438; Print / Save PDF</button>

<!-- Header -->
<div class="header">
    <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA">
    <div class="header-text">
        <h1>Placement Impact Analysis Report</h1>
        <p>The Catholic University of Eastern Africa &mdash; Industrial Attachment Programme</p>
        <p>Generated: <?= date('d F Y, H:i') ?> EAT</p>
    </div>
</div>

<!-- KPI Row -->
<div class="kpi-row">
    <div class="kpi-box"><div class="num"><?= $d['total_students'] ?></div><div class="lbl">Total Students</div></div>
    <div class="kpi-box"><div class="num"><?= $placementRate ?>%</div><div class="lbl">Placement Rate</div></div>
    <div class="kpi-box"><div class="num"><?= $d['completed_placements'] ?></div><div class="lbl">Completed</div></div>
    <div class="kpi-box"><div class="num"><?= $d['total_host_orgs'] ?></div><div class="lbl">Host Partners</div></div>
    <div class="kpi-box"><div class="num"><?= $d['total_logbook_weeks'] ?></div><div class="lbl">Approved Logbook Wks</div></div>
    <div class="kpi-box"><div class="num"><?= $d['avg_grade'] ?>%</div><div class="lbl">Avg Grade</div></div>
    <div class="kpi-box"><div class="num"><?= $acceptRate ?>%</div><div class="lbl">Job App Acceptance</div></div>
</div>

<!-- Two columns: Faculty Impact + Grade Distribution -->
<div class="two-col">
    <!-- Faculty -->
    <div class="col">
        <div class="section-heading">Placement by Faculty</div>
        <?php
        $facRows = [];
        $facData = $d['faculty_impact'];
        if ($facData && $facData->num_rows > 0) while ($r = $facData->fetch_assoc()) $facRows[] = $r;
        $maxPl = max(array_column($facRows, 'placed_students') ?: [1]);
        foreach ($facRows as $r):
            $pct  = $r['total_students'] > 0 ? round($r['placed_students'] / $r['total_students'] * 100) : 0;
            $barW = round($r['placed_students'] / $maxPl * 100);
        ?>
        <div class="bar-row">
            <div class="bar-label">
                <span class="name"><?= htmlspecialchars($r['Faculty'] ?? 'Unknown') ?></span>
                <span class="value"><?= $r['placed_students'] ?>/<?= $r['total_students'] ?> (<?= $pct ?>%)<?= $r['avg_score'] ? ' · ' . $r['avg_score'] . '%' : '' ?></span>
            </div>
            <div class="bar-track"><div class="bar-fill" style="background:linear-gradient(90deg,#8B1538,#a51c44); width:<?= $barW ?>%;"></div></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Grade Distribution + Logbook Engagement -->
    <div class="col">
        <div class="section-heading">Grade Distribution</div>
        <?php
        $gradeColors = [
            'Distinction (80-100)' => '#059669',
            'Credit (70-79)'       => '#2563eb',
            'Pass (60-69)'         => '#d97706',
            'Below Pass (<60)'     => '#dc2626',
        ];
        $gradeRows = [];
        $totalGraded = 0;
        $gradeData = $d['grade_dist'];
        if ($gradeData && $gradeData->num_rows > 0) while ($r = $gradeData->fetch_assoc()) { $gradeRows[] = $r; $totalGraded += $r['student_count']; }
        foreach ($gradeRows as $r):
            $pct = $totalGraded > 0 ? round($r['student_count'] / $totalGraded * 100) : 0;
            $clr = $gradeColors[$r['GradeBand']] ?? '#6b7280';
        ?>
        <div class="bar-row">
            <div class="bar-label">
                <span class="name"><?= htmlspecialchars($r['GradeBand']) ?></span>
                <span class="value" style="color:<?= $clr ?>;"><?= $r['student_count'] ?> students (<?= $pct ?>%)</span>
            </div>
            <div class="bar-track"><div class="bar-fill" style="background:<?= $clr ?>; width:<?= $pct ?>%;"></div></div>
        </div>
        <?php endforeach; ?>

        <div class="section-heading" style="margin-top:12px;">Logbook Engagement</div>
        <?php
        $engRows = [];
        $engData = $d['logbook_engagement'];
        $engColors = ['12 weeks (Full)' => '#059669', '9-11 weeks' => '#2563eb', '6-8 weeks' => '#d97706', '< 6 weeks' => '#dc2626'];
        if ($engData && $engData->num_rows > 0) while ($r = $engData->fetch_assoc()) $engRows[] = $r;
        $totalEng = array_sum(array_column($engRows, 'student_count')) ?: 1;
        foreach ($engRows as $r):
            $pct = round($r['student_count'] / $totalEng * 100);
            $clr = $engColors[$r['EngagementBand']] ?? '#6b7280';
        ?>
        <div class="bar-row">
            <div class="bar-label">
                <span class="name"><?= htmlspecialchars($r['EngagementBand']) ?></span>
                <span class="value" style="color:<?= $clr ?>;"><?= $r['student_count'] ?> (<?= $pct ?>%)</span>
            </div>
            <div class="bar-track"><div class="bar-fill" style="background:<?= $clr ?>; width:<?= $pct ?>%;"></div></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Host Organization Performance Table -->
<div class="section-heading">Host Organization Performance</div>
<table style="margin-bottom: 16px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Organization</th>
            <th>Location</th>
            <th class="center">Students</th>
            <th class="center">Cleared</th>
            <th class="center">Avg Grade</th>
            <th>Performance Bar</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $hosts = $d['top_hosts'];
        $hi = 0;
        if ($hosts && $hosts->num_rows > 0):
            while ($h = $hosts->fetch_assoc()):
                $hi++;
                $sc = $h['avg_score'] ?? 0;
                $clr = $sc >= 80 ? '#059669' : ($sc >= 70 ? '#2563eb' : '#d97706');
                $bw  = round($sc);
        ?>
        <tr>
            <td style="color:#9ca3af;"><?= $hi ?></td>
            <td style="font-weight:700;"><?= htmlspecialchars($h['OrganizationName']) ?></td>
            <td style="font-size:7.5pt; color:#4b5563; max-width:130px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($h['PhysicalAddress'] ?? '—') ?></td>
            <td class="center"><span class="badge badge-blue"><?= $h['student_count'] ?></span></td>
            <td class="center"><span class="badge badge-green"><?= $h['cleared_count'] ?></span></td>
            <td class="center" style="font-weight:700; color:<?= $clr ?>;"><?= $sc ? $sc . '%' : '—' ?></td>
            <td style="min-width:80px;">
                <div class="bar-track" style="height:7px;">
                    <div class="bar-fill" style="background:<?= $clr ?>; width:<?= $bw ?>%;"></div>
                </div>
            </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="7" class="center" style="color:#6b7280; padding:12px;">No data.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Opportunities Table -->
<div class="section-heading">Job Opportunities &amp; Application Conversion</div>
<table>
    <thead>
        <tr>
            <th>Opportunity Description</th>
            <th>Host Organization</th>
            <th class="center">Status</th>
            <th class="center">Applicants</th>
            <th class="center">Accepted</th>
            <th class="center">Conversion</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $opps = $d['opportunities'];
        if ($opps && $opps->num_rows > 0):
            while ($op = $opps->fetch_assoc()):
                $conv = $op['total_apps'] > 0 ? round($op['accepted'] / $op['total_apps'] * 100) : 0;
                $cc   = $conv >= 50 ? '#059669' : ($conv >= 25 ? '#2563eb' : '#d97706');
                $desc = mb_strlen($op['Description']) > 80 ? mb_substr($op['Description'],0,80).'…' : $op['Description'];
        ?>
        <tr>
            <td style="font-size:7.5pt; max-width:200px;"><?= htmlspecialchars($desc) ?></td>
            <td style="font-weight:700; font-size:7.5pt;"><?= htmlspecialchars($op['OrganizationName']) ?></td>
            <td class="center">
                <span class="badge <?= $op['Status'] === 'Active' ? 'badge-green' : 'badge-gray' ?>"><?= $op['Status'] ?></span>
            </td>
            <td class="center" style="font-weight:700;"><?= $op['total_apps'] ?></td>
            <td class="center"><span class="badge badge-green"><?= $op['accepted'] ?></span></td>
            <td class="center" style="color:<?= $cc ?>; font-weight:700;"><?= $conv ?>%</td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6" class="center" style="color:#6b7280; padding:12px;">No data.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    Placement Impact Analysis Report &mdash; The Catholic University of Eastern Africa &mdash; <?= date('d F Y') ?> &mdash; CONFIDENTIAL
</div>
</div>
</body>
</html>
