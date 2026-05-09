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
</head><body>
    <button class="no-print" onclick="window.print()">Print / Save PDF</button>
<div class="report-container">
        <div class="header">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
            <h1>The Catholic University of Eastern Africa</h1>
            <div class="header-motto">"Consecrate them in the Truth"</div>
            <div class="header-title">Placement Impact Analysis Report</div>
        </div>
        <hr>

        <div class="kpi-row">
            <div class="kpi-box"><span class="num"><?= $d['total_students'] ?></span><span class="lbl">Total Students</span></div>
            <div class="kpi-box"><span class="num"><?= $placementRate ?>%</span><span class="lbl">Placement Rate</span></div>
            <div class="kpi-box"><span class="num"><?= $d['completed_placements'] ?></span><span class="lbl">Completed</span></div>
            <div class="kpi-box"><span class="num"><?= $d['total_host_orgs'] ?></span><span class="lbl">Host Partners</span></div>
            <div class="kpi-box"><span class="num"><?= $d['avg_grade'] ?>%</span><span class="lbl">Avg Grade</span></div>
        </div>

        <h3>Placement by Faculty</h3>
        <table>
            <thead>
                <tr>
                    <th>Faculty</th>
                    <th style="text-align: center;">Placed / Total</th>
                    <th style="text-align: center;">Rate (%)</th>
                    <th style="text-align: center;">Avg Score (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $facData = $d['faculty_impact'];
                if ($facData && $facData->num_rows > 0):
                    while ($r = $facData->fetch_assoc()):
                        $pct = $r['total_students'] > 0 ? round($r['placed_students'] / $r['total_students'] * 100) : 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['Faculty'] ?? 'Unknown') ?></td>
                    <td style="text-align: center;"><?= $r['placed_students'] ?> / <?= $r['total_students'] ?></td>
                    <td style="text-align: center;"><?= $pct ?>%</td>
                    <td style="text-align: center;"><?= $r['avg_score'] ? $r['avg_score'] . '%' : '-' ?></td>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>

        <h3>Grade Distribution</h3>
        <table>
            <thead>
                <tr>
                    <th>Grade Band</th>
                    <th style="text-align: center;">Student Count</th>
                    <th style="text-align: center;">Percentage (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalGraded = 0;
                $gradeRows = [];
                $gradeData = $d['grade_dist'];
                if ($gradeData && $gradeData->num_rows > 0) while ($r = $gradeData->fetch_assoc()) { $gradeRows[] = $r; $totalGraded += $r['student_count']; }
                foreach ($gradeRows as $r):
                    $pct = $totalGraded > 0 ? round($r['student_count'] / $totalGraded * 100) : 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['GradeBand']) ?></td>
                    <td style="text-align: center;"><?= $r['student_count'] ?></td>
                    <td style="text-align: center;"><?= $pct ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Host Organization Performance</h3>
        <table>
            <thead>
                <tr>
                    <th>Organization</th>
                    <th style="text-align: center;">Students</th>
                    <th style="text-align: center;">Cleared</th>
                    <th style="text-align: center;">Avg Grade (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hosts = $d['top_hosts'];
                if ($hosts && $hosts->num_rows > 0):
                    while ($h = $hosts->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($h['OrganizationName']) ?></td>
                    <td style="text-align: center;"><?= $h['student_count'] ?></td>
                    <td style="text-align: center;"><?= $h['cleared_count'] ?></td>
                    <td style="text-align: center;"><?= $h['avg_score'] ? $h['avg_score'] . '%' : '-' ?></td>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>>
</html>
