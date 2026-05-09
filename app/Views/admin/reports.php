<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<!-- System Pulse Stats -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="card-content">
            <h3>Final Reports</h3>
            <p class="card-number"><?= $systemStats['final_reports'] ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Cleared Students</h3>
            <p class="card-number"><?= $systemStats['cleared_students'] ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Total Job Apps</h3>
            <p class="card-number"><?= array_sum($systemStats['job_apps']) ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Pending Apps</h3>
            <p class="card-number"><?= $systemStats['job_apps']['Pending'] ?? 0 ?></p>
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Detailed Access Cards -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Diagnostic Reports</h2>
        </div>
        <div class="quick-actions">
            <a href="<?= Helpers::baseUrl('/admin/reports/assessment-schedule') ?>" class="action-btn">
                <i class="fas fa-calendar-check" style="color: #000;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Assessment Schedule</span>
                    <small style="color: #6b7280;">Mapped Supervision Dates</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #000;"></i>
            </a>
            
            <a href="<?= Helpers::baseUrl('/admin/reports/supervisor-stats') ?>" class="action-btn">
                <i class="fas fa-users-cog" style="color: #000;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Supervisor Workloads</span>
                    <small style="color: #6b7280;">Students per Lecturer</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #000;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/reports/assessment-summary') ?>" class="action-btn">
                <i class="fas fa-list-ol" style="color: #000;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Assessment Summary</span>
                    <small style="color: #6b7280;">Student Grades & Averages</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #000;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/reports/effectiveness') ?>" class="action-btn">
                <i class="fas fa-chart-line" style="color: #000;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">System Effectiveness</span>
                    <small style="color: #6b7280;">Placement Impact Analytics</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #000;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/reports/placement-completions') ?>" class="action-btn">
                <i class="fas fa-check-double" style="color: #000;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Placement Completions</span>
                    <small style="color: #6b7280;">Verified Attachment Ends</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #000;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/reports/placement-impact') ?>" class="action-btn">
                <i class="fas fa-globe-africa" style="color: #000;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Placement Impact Analysis</span>
                    <small style="color: #6b7280;">Faculty & Host Performance Metrics</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #000;"></i>
            </a>
        </div>
    </div>

    <!-- Faculty Distribution -->
    <div class="activity-section">
        <div class="report-card-header">
            <h2 class="report-card-title"><i class="fas fa-university"></i> Placement by Faculty</h2>
        </div>
        <div style="padding: 12px 0;">
            <?php while($row = $placementStats->fetch_assoc()): ?>
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                        <span class="text-bold"><?= htmlspecialchars($row['Faculty']) ?></span>
                        <span class="text-black text-bold"><?= $row['count'] ?> Students</span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-fill" style="width: <?= min(($row['count'] / 50) * 100, 100) ?>%;"></div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Active Organizations -->
    <div class="activity-section">
        <div class="report-card-header">
            <h2 class="report-card-title"><i class="fas fa-building"></i> Top Host Organizations</h2>
        </div>
        <div class="table-container" style="box-shadow: none; border: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Placements</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $hostStats->fetch_assoc()): ?>
                        <tr>
                            <td class="text-bold"><?= htmlspecialchars($row['OrganizationName']) ?></td>
                            <td>
                                <span class="report-tag report-tag-dark"><?= $row['student_count'] ?> Active Placements</span>
                            </td>
                            <td>
                                <a href="<?= Helpers::baseUrl('/admin/reports/host-performance?host_id=' . ($row['HostOrgID'] ?? '')) ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
                                    View Analytics
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
