<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="report-header">
    <div class="report-title">
        <h1>Placement Impact Analysis</h1>
        <p class="report-subtitle">
            Comprehensive analytics on placement program effectiveness, faculty reach, host performance and student outcomes.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= Helpers::baseUrl('/admin/reports') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="<?= Helpers::baseUrl('/reports/print/placement-impact') ?>" target="_blank" class="btn report-tag-dark">
            <i class="fas fa-print"></i> Print / Download PDF
        </a>
    </div>
</div>

<!-- Top KPI Row -->
<?php $d = $impact; ?>
<div class="summary-cards" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); margin-bottom: 28px;">
    <div class="summary-card">
        <div class="card-content">
            <h3>Total Students</h3>
            <p class="card-number"><?= $d['total_students'] ?></p>
            <small style=";">Registered</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Placement Rate</h3>
            <p class="card-number">
                <?= $d['total_students'] > 0 ? round(($d['placed_students'] / $d['total_students']) * 100, 1) : 0 ?>%
            </p>
            <small style=";"><?= $d['placed_students'] ?> of <?= $d['total_students'] ?> placed</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Completed</h3>
            <p class="card-number"><?= $d['completed_placements'] ?></p>
            <small style=";">Verified Completions</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Host Partners</h3>
            <p class="card-number"><?= $d['total_host_orgs'] ?></p>
            <small style=";">Organizations</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Logbook Weeks</h3>
            <p class="card-number"><?= $d['total_logbook_weeks'] ?></p>
            <small style=";">Approved Entries</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Avg Grade</h3>
            <p class="card-number"><?= $d['avg_grade'] ?>%</p>
            <small style=";"><?= $d['total_assessments'] ?> assessments</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Job Applications</h3>
            <p class="card-number"><?= $d['total_applications'] ?></p>
            <small style=";"><?= $d['accepted_applications'] ?> Accepted</small>
        </div>
    </div>
</div>

<!-- Row 1: Faculty Impact + Grade Distribution -->
<div class="content-grid" style="margin-bottom: 24px;">

    <!-- Faculty Breakdown -->
    <div class="activity-section">
        <div class="report-card-header">
            <h2 class="report-card-title"><i class="fas fa-university"></i> Placement by Faculty</h2>
        </div>
        <div style="padding: 12px 0;">
            <?php
            $faculties  = $d['faculty_impact'];
            $maxPlaced  = 1;
            $facRows    = [];
            if ($faculties && $faculties->num_rows > 0) {
                while ($r = $faculties->fetch_assoc()) { $facRows[] = $r; if ($r['placed_students'] > $maxPlaced) $maxPlaced = $r['placed_students']; }
            }
            foreach ($facRows as $r):
                $pct = round(($r['placed_students'] / max($r['total_students'],1)) * 100);
                $barW = round(($r['placed_students'] / $maxPlaced) * 100);
            ?>
            <div style="margin-bottom: 18px;">
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                    <span class="text-bold text-black"><?= htmlspecialchars($r['Faculty'] ?? 'Unknown') ?></span>
                    <span class="text-black text-bold"><?= $r['placed_students'] ?>/<?= $r['total_students'] ?>
                        <small class="text-muted">(<?= $pct ?>%)</small>
                        <?php if ($r['avg_score']): ?>
                            &nbsp;· <span class="text-black"><?= $r['avg_score'] ?>% avg</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="progress-container">
                    <div class="progress-fill" style="width:<?= $barW ?>%;"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($facRows)): ?>
                <p style="text-align:center; ; padding:20px;">No faculty data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="activity-section">
        <div class="report-card-header">
            <h2 class="report-card-title"><i class="fas fa-graduation-cap"></i> Grade Distribution</h2>
        </div>
        <div style="padding: 12px 0;">
            <?php
            $gradeColors = [
                'Distinction (80-100)' => ['bar' => '#000', 'bg' => '#f3f4f6', 'txt' => '#000'],
                'Credit (70-79)'       => ['bar' => '#1a1a1a', 'bg' => '#f9fafb', 'txt' => '#1a1a1a'],
                'Pass (60-69)'         => ['bar' => '#4b5563', 'bg' => '#ffffff', 'txt' => '#4b5563'],
                'Below Pass (<60)'     => ['bar' => '#6b7280', 'bg' => '#ffffff', 'txt' => '#6b7280'],
            ];
            $gradeDist = $d['grade_dist'];
            $gradeRows = [];
            $totalGraded = 0;
            if ($gradeDist && $gradeDist->num_rows > 0) {
                while ($r = $gradeDist->fetch_assoc()) { $gradeRows[] = $r; $totalGraded += $r['student_count']; }
            }
            foreach ($gradeRows as $r):
                $pct   = $totalGraded > 0 ? round(($r['student_count'] / $totalGraded) * 100) : 0;
                $color = $gradeColors[$r['GradeBand']] ?? ['bar'=>'#6b7280','bg'=>'#f3f4f6','txt'=>'#374151'];
            ?>
            <div style="margin-bottom: 20px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <span class="report-tag" style="background:<?= $color['bg'] ?>; color:<?= $color['txt'] ?>;">
                        <?= htmlspecialchars($r['GradeBand']) ?>
                    </span>
                    <span class="text-bold text-black" style="font-size:14px;">
                        <?= $r['student_count'] ?> <small class="text-muted" style="font-weight:400;">(<?= $pct ?>%)</small>
                    </span>
                </div>
                <div class="progress-container" style="height:10px;">
                    <div class="progress-fill" style="background:<?= $color['bar'] ?>; width:<?= $pct ?>%;"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($gradeRows)): ?>
                <p style="text-align:center; ; padding:20px;">No assessment grades available.</p>
            <?php endif; ?>

            <div style="margin-top: 24px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                <h3 class="text-bold text-black" style="font-size:14px; margin-bottom:12px;">
                    <i class="fas fa-book-open" style="margin-right:6px;"></i>Logbook Engagement
                </h3>
                <?php
                $engColors = [
                    '12 weeks (Full)' => '#000',
                    '9-11 weeks'      => '#1a1a1a',
                    '6-8 weeks'       => '#4b5563',
                    '< 6 weeks'       => '#6b7280'
                ];
                $logbookEngagement = $d['logbook_engagement'];
                $engRows = [];
                $totalEng = 0;
                if ($logbookEngagement && $logbookEngagement->num_rows > 0) {
                    while ($r = $logbookEngagement->fetch_assoc()) {
                        $engRows[] = $r;
                        $totalEng += $r['student_count'];
                    }
                }
                foreach ($engRows as $r):
                    $pct = $totalEng > 0 ? round(($r['student_count'] / $totalEng) * 100) : 0;
                    $clr = $engColors[$r['EngagementBand']] ?? '#6b7280';
                ?>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; margin-bottom:8px;">
                    <span class="text-muted"><?= htmlspecialchars($r['EngagementBand']) ?></span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div class="progress-container progress-sm" style="width:80px;">
                            <div class="progress-fill" style="background:<?= $clr ?>; width:<?= $pct ?>%;"></div>
                        </div>
                        <span style="color:<?= $clr ?>; font-weight:700;"><?= $r['student_count'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="card" style="padding:0; overflow:hidden; margin-bottom: 24px;">
    <div class="report-card-header">
        <h2 class="report-card-title">
            <i class="fas fa-building"></i> Host Organization Performance
        </h2>
        <span class="text-small text-muted">Ranked by average student grade</span>
    </div>
    <div class="table-container" style="box-shadow:none; border:none; overflow-x:auto;">
        <table style="min-width: 700px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Organization</th>
                    <th>Location</th>
                    <th style="text-align:center;">Students Hosted</th>
                    <th style="text-align:center;">Cleared</th>
                    <th style="text-align:center;">Avg Student Grade</th>
                    <th style="text-align:center;">Performance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hosts = $d['top_hosts'];
                $hi    = 0;
                $maxScore = 100;
                if ($hosts && $hosts->num_rows > 0):
                    while ($host = $hosts->fetch_assoc()):
                        $hi++;
                        $score = $host['avg_score'] ?? 0;
                        $perfColor = $score >= 70 ? '#000' : '#1a1a1a';
                        $barW  = round(($score / $maxScore) * 100);
                ?>
                <tr>
                    <td class="text-xs text-bold"><?= $hi ?></td>
                    <td class="text-bold" style="font-size:13px;"><?= htmlspecialchars($host['OrganizationName']) ?></td>
                    <td class="text-small text-muted"><?= htmlspecialchars($host['PhysicalAddress'] ?? '—') ?></td>
                    <td style="text-align:center;">
                        <span class="report-tag report-tag-neutral">
                            <?= $host['student_count'] ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="report-tag report-tag-neutral">
                            <?= $host['cleared_count'] ?>
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:bold; color:<?= $perfColor ?>;">
                        <?= $score ? $score . '%' : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td style="min-width:120px; padding-right:16px;">
                        <div class="progress-container">
                            <div class="progress-fill" style="background:<?= $perfColor ?>; width:<?= $barW ?>%;"></div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center; ; padding:24px;">No host data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Row 3: Job Opportunities Analysis -->
<div class="card" style="padding:0; overflow:hidden;">
    <div class="report-card-header">
        <h2 class="report-card-title">
            <i class="fas fa-briefcase"></i> Job Opportunities &amp; Conversion
        </h2>
        <!-- Job app stats pills -->
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <?php
            $jaStats = $d['job_app_stats'];
            $jaPillColors = [
                'Accepted' => ['#f3f4f6','#000'],
                'Pending'  => ['#f9fafb','#4b5563'],
                'Rejected' => ['#ffffff','#1a1a1a']
            ];
            foreach ($jaStats as $status => $cnt):
                [$bg, $txt] = $jaPillColors[$status] ?? ['#f3f4f6','#374151'];
            ?>
            <span class="report-tag" style="background:<?= $bg ?>; color:<?= $txt ?>;">
                <?= $status ?>: <?= $cnt ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="table-container" style="box-shadow:none; border:none; overflow-x:auto;">
        <table style="min-width: 650px;">
            <thead>
                <tr>
                    <th>Opportunity</th>
                    <th>Host Organization</th>
                    <th style="text-align:center;">Listing Status</th>
                    <th style="text-align:center;">Total Applicants</th>
                    <th style="text-align:center;">Accepted</th>
                    <th style="text-align:center;">Conversion Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $opps = $d['opportunities'];
                if ($opps && $opps->num_rows > 0):
                    while ($opp = $opps->fetch_assoc()):
                        $conv = $opp['total_apps'] > 0 ? round(($opp['accepted'] / $opp['total_apps']) * 100) : 0;
                        $convColor = $conv >= 50 ? '#000' : '#1a1a1a';
                        $desc = mb_strlen($opp['Description']) > 70 ? mb_substr($opp['Description'], 0, 70) . '…' : $opp['Description'];
                ?>
                <tr>
                    <td class="text-small" style="max-width:260px;" title="<?= htmlspecialchars($opp['Description']) ?>">
                        <?= htmlspecialchars($desc) ?>
                    </td>
                    <td class="text-small text-bold"><?= htmlspecialchars($opp['OrganizationName']) ?></td>
                    <td style="text-align:center;">
                        <?php $os = $opp['Status']; ?>
                        <span class="report-tag <?= $os === 'Active' ? 'report-tag-dark' : '' ?>">
                            <?= htmlspecialchars($os) ?>
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:700;"><?= $opp['total_apps'] ?></td>
                    <td style="text-align:center;">
                        <span class="report-tag report-tag-neutral">
                            <?= $opp['accepted'] ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; align-items:center; gap:6px; justify-content:center;">
                            <div class="progress-container progress-sm" style="width:60px;">
                                <div class="progress-fill" style="background:<?= $convColor ?>; width:<?= $conv ?>%;"></div>
                            </div>
                            <span class="text-small text-bold" style="color:<?= $convColor ?>;"><?= $conv ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align:center; ; padding:24px;">No opportunity data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
