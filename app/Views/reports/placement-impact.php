<?php use App\Core\Helpers; ?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 800; color: #1f2937; margin: 0;">Placement Impact Analysis</h1>
        <p style="color: #6b7280; font-size: 13px; margin: 4px 0 0;">
            Comprehensive analytics on placement program effectiveness, faculty reach, host performance and student outcomes.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= Helpers::baseUrl('/admin/reports') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="<?= Helpers::baseUrl('/reports/print/placement-impact') ?>" target="_blank" class="btn" style="background:#8B1538; color:#fff;">
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
            <small style="color:#6b7280;">Registered</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Placement Rate</h3>
            <p class="card-number">
                <?= $d['total_students'] > 0 ? round(($d['placed_students'] / $d['total_students']) * 100, 1) : 0 ?>%
            </p>
            <small style="color:#6b7280;"><?= $d['placed_students'] ?> of <?= $d['total_students'] ?> placed</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Completed</h3>
            <p class="card-number"><?= $d['completed_placements'] ?></p>
            <small style="color:#6b7280;">Verified Completions</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Host Partners</h3>
            <p class="card-number"><?= $d['total_host_orgs'] ?></p>
            <small style="color:#6b7280;">Organizations</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Logbook Weeks</h3>
            <p class="card-number"><?= $d['total_logbook_weeks'] ?></p>
            <small style="color:#6b7280;">Approved Entries</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Avg Grade</h3>
            <p class="card-number"><?= $d['avg_grade'] ?>%</p>
            <small style="color:#6b7280;"><?= $d['total_assessments'] ?> assessments</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Job Applications</h3>
            <p class="card-number"><?= $d['total_applications'] ?></p>
            <small style="color:#6b7280;"><?= $d['accepted_applications'] ?> Accepted</small>
        </div>
    </div>
</div>

<!-- Row 1: Faculty Impact + Grade Distribution -->
<div class="content-grid" style="margin-bottom: 24px;">

    <!-- Faculty Breakdown -->
    <div class="activity-section">
        <div class="section-header">
            <h2><i class="fas fa-university" style="color:#8B1538; margin-right:6px;"></i> Placement by Faculty</h2>
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
                    <span style="font-weight:600; color:#1f2937;"><?= htmlspecialchars($r['Faculty'] ?? 'Unknown') ?></span>
                    <span style="color:#8B1538; font-weight:700;"><?= $r['placed_students'] ?>/<?= $r['total_students'] ?>
                        <small style="color:#6b7280;">(<?= $pct ?>%)</small>
                        <?php if ($r['avg_score']): ?>
                            &nbsp;· <span style="color:#1a1a1a;"><?= $r['avg_score'] ?>% avg</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div style="width:100%; height:9px; background:#f3f4f6; border-radius:5px; overflow:hidden;">
                    <div style="background:linear-gradient(90deg,#8B1538,#c0264d); height:100%; width:<?= $barW ?>%; border-radius:5px; transition: width 0.6s;"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($facRows)): ?>
                <p style="text-align:center; color:#6b7280; padding:20px;">No faculty data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="activity-section">
        <div class="section-header">
            <h2><i class="fas fa-graduation-cap" style="color:#8B1538; margin-right:6px;"></i> Grade Distribution</h2>
        </div>
        <div style="padding: 12px 0;">
            <?php
            $gradeColors = [
                'Distinction (80-100)' => ['bar' => '#8B1538', 'bg' => '#f0e6ea', 'txt' => '#8B1538'],
                'Credit (70-79)'       => ['bar' => '#1a1a1a', 'bg' => '#f0f0f0', 'txt' => '#1a1a1a'],
                'Pass (60-69)'         => ['bar' => '#4b5563', 'bg' => '#f5f5f5', 'txt' => '#4b5563'],
                'Below Pass (<60)'     => ['bar' => '#6b7280', 'bg' => '#f9f9f9', 'txt' => '#6b7280'],
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
                    <span style="background:<?= $color['bg'] ?>; color:<?= $color['txt'] ?>; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700;">
                        <?= htmlspecialchars($r['GradeBand']) ?>
                    </span>
                    <span style="font-weight:700; color:<?= $color['bar'] ?>; font-size:14px;">
                        <?= $r['student_count'] ?> <small style="color:#6b7280; font-weight:400;">(<?= $pct ?>%)</small>
                    </span>
                </div>
                <div style="width:100%; height:10px; background:#f3f4f6; border-radius:5px; overflow:hidden;">
                    <div style="background:<?= $color['bar'] ?>; height:100%; width:<?= $pct ?>%; border-radius:5px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($gradeRows)): ?>
                <p style="text-align:center; color:#6b7280; padding:20px;">No assessment grades available.</p>
            <?php endif; ?>

            <!-- Logbook Engagement -->
            <div style="margin-top: 24px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                <h3 style="font-size:14px; font-weight:700; color:#374151; margin-bottom:12px;">
                    <i class="fas fa-book-open" style="color:#8B1538; margin-right:6px;"></i>Logbook Engagement
                </h3>
                <?php
                $engRows = [];
                $engData = $d['logbook_engagement'];
                if ($engData && $engData->num_rows > 0) while ($r = $engData->fetch_assoc()) $engRows[] = $r;
                $engColors = ['12 weeks (Full)' => '#8B1538', '9-11 weeks' => '#1a1a1a', '6-8 weeks' => '#4b5563', '< 6 weeks' => '#6b7280'];
                $totalEng  = array_sum(array_column($engRows, 'student_count'));
                foreach ($engRows as $r):
                    $pct = $totalEng > 0 ? round(($r['student_count'] / $totalEng) * 100) : 0;
                    $clr = $engColors[$r['EngagementBand']] ?? '#6b7280';
                ?>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; margin-bottom:8px;">
                    <span style="color:#374151;"><?= htmlspecialchars($r['EngagementBand']) ?></span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div style="width:80px; height:7px; background:#f3f4f6; border-radius:4px; overflow:hidden;">
                            <div style="background:<?= $clr ?>; height:100%; width:<?= $pct ?>;"></div>
                        </div>
                        <span style="color:<?= $clr ?>; font-weight:700;"><?= $r['student_count'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Top Host Organizations Table -->
<div class="card" style="padding:0; overflow:hidden; margin-bottom: 24px;">
    <div style="padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
        <h2 style="font-size:1rem; font-weight:700; margin:0; color:#1f2937;">
            <i class="fas fa-building" style="color:#8B1538; margin-right:6px;"></i> Host Organization Performance
        </h2>
        <span style="font-size:12px; color:#6b7280;">Ranked by average student grade</span>
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
                        $perfColor = $score >= 70 ? '#8B1538' : '#1a1a1a';
                        $barW  = round(($score / $maxScore) * 100);
                ?>
                <tr>
                    <td style="color:#9ca3af; font-size:12px; font-weight:700;"><?= $hi ?></td>
                    <td style="font-weight:700; font-size:13px;"><?= htmlspecialchars($host['OrganizationName']) ?></td>
                    <td style="font-size:12px; color:#4b5563;"><?= htmlspecialchars($host['PhysicalAddress'] ?? '—') ?></td>
                    <td style="text-align:center;">
                        <span style="background:#f0e6ea; color:#8B1538; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700;">
                            <?= $host['student_count'] ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span style="background:#f0f0f0; color:#1a1a1a; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700;">
                            <?= $host['cleared_count'] ?>
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:bold; color:<?= $perfColor ?>;">
                        <?= $score ? $score . '%' : '<span style="color:#9ca3af;">—</span>' ?>
                    </td>
                    <td style="min-width:120px; padding-right:16px;">
                        <div style="width:100%; height:8px; background:#f3f4f6; border-radius:4px; overflow:hidden;">
                            <div style="background:<?= $perfColor ?>; height:100%; width:<?= $barW ?>%; border-radius:4px;"></div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center; color:#6b7280; padding:24px;">No host data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Row 3: Job Opportunities Analysis -->
<div class="card" style="padding:0; overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap: wrap; gap: 8px;">
        <h2 style="font-size:1rem; font-weight:700; margin:0; color:#1f2937;">
            <i class="fas fa-briefcase" style="color:#8B1538; margin-right:6px;"></i> Job Opportunities &amp; Application Conversion
        </h2>
        <!-- Job app stats pills -->
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <?php
            $jaStats = $d['job_app_stats'];
            $jaPillColors = [
                'Accepted' => ['#f0e6ea','#8B1538'],
                'Pending'  => ['#f5f5f5','#4b5563'],
                'Rejected' => ['#f0f0f0','#1a1a1a']
            ];
            foreach ($jaStats as $status => $cnt):
                [$bg, $txt] = $jaPillColors[$status] ?? ['#f3f4f6','#374151'];
            ?>
            <span style="background:<?= $bg ?>; color:<?= $txt ?>; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
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
                        $convColor = $conv >= 50 ? '#8B1538' : '#1a1a1a';
                        $desc = mb_strlen($opp['Description']) > 70 ? mb_substr($opp['Description'], 0, 70) . '…' : $opp['Description'];
                ?>
                <tr>
                    <td style="font-size:12px; max-width:260px;" title="<?= htmlspecialchars($opp['Description']) ?>">
                        <?= htmlspecialchars($desc) ?>
                    </td>
                    <td style="font-size:12px; font-weight:600;"><?= htmlspecialchars($opp['OrganizationName']) ?></td>
                    <td style="text-align:center;">
                        <?php $os = $opp['Status']; ?>
                        <span style="background:<?= $os === 'Active' ? '#f0e6ea' : '#f3f4f6' ?>; color:<?= $os === 'Active' ? '#8B1538' : '#374151' ?>; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700;">
                            <?= htmlspecialchars($os) ?>
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:700;"><?= $opp['total_apps'] ?></td>
                    <td style="text-align:center;">
                        <span style="background:#f0e6ea; color:#8B1538; padding:2px 8px; border-radius:20px; font-size:12px; font-weight:700;">
                            <?= $opp['accepted'] ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; align-items:center; gap:6px; justify-content:center;">
                            <div style="width:60px; height:7px; background:#f3f4f6; border-radius:4px; overflow:hidden;">
                                <div style="background:<?= $convColor ?>; height:100%; width:<?= $conv ?>%;"></div>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:<?= $convColor ?>;"><?= $conv ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align:center; color:#6b7280; padding:24px;">No opportunity data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
