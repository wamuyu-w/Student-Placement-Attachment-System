<?php use App\Core\Helpers; ?>

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
                <i class="fas fa-calendar-check" style="color: #8B1538;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Assessment Schedule</span>
                    <small style="color: #6b7280;">Mapped Supervision Dates</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #d1d5db;"></i>
            </a>
            
            <a href="<?= Helpers::baseUrl('/admin/reports/supervisor-stats') ?>" class="action-btn">
                <i class="fas fa-users-cog" style="color: #2563eb;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Supervisor Workloads</span>
                    <small style="color: #6b7280;">Students per Lecturer</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #d1d5db;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/reports/assessment-summary') ?>" class="action-btn">
                <i class="fas fa-list-ol" style="color: #d97706;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Assessment Summary</span>
                    <small style="color: #6b7280;">Student Grades & Averages</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #d1d5db;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/reports/effectiveness') ?>" class="action-btn">
                <i class="fas fa-chart-line" style="color: #7c3aed;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">System Effectiveness</span>
                    <small style="color: #6b7280;">Placement Impact Analytics</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #d1d5db;"></i>
            </a>

            <a href="<?= Helpers::baseUrl('/admin/users') ?>" class="action-btn">
                <i class="fas fa-check-double" style="color: #059669;"></i>
                <div style="flex: 1;">
                    <span style="display: block; font-weight: bold;">Placement Completions</span>
                    <small style="color: #6b7280;">Verified Attachment Ends</small>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 12px; color: #d1d5db;"></i>
            </a>
        </div>
    </div>

    <!-- Faculty Distribution -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Faculty Distribution</h2>
        </div>
        <div style="padding: 10px 0;">
            <?php if ($placementStats && $placementStats->num_rows > 0): ?>
                <?php while($row = $placementStats->fetch_assoc()): ?>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                            <span style="font-weight: 600;"><?= htmlspecialchars($row['Faculty']) ?></span>
                            <span style="color: #8B1538; font-weight: bold;"><?= $row['count'] ?> Students</span>
                        </div>
                        <div style="width: 100%; bg-gray-100; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div style="background: #8B1538; height: 100%; width: <?= min(($row['count'] / 50) * 100, 100) ?>%;"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Top Hosts -->
<div class="card" style="margin-top: 24px; padding: 0;">
    <div style="padding: 20px; border-bottom: 1px solid #eee;">
        <h2 style="font-size: 18px; font-weight: 700;">Top Placement Hosts</h2>
    </div>
    <div class="table-container" style="box-shadow: none; border: none;">
        <table>
            <thead>
                <tr>
                    <th>Host Organization</th>
                    <th>Student Intake</th>
                    <th>Performance Analytics</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($hostStats && $hostStats->num_rows > 0): ?>
                    <?php while($row = $hostStats->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['OrganizationName']) ?></td>
                            <td>
                                <span class="status-badge status-active"><?= $row['student_count'] ?> Active Placements</span>
                            </td>
                            <td>
                                <a href="<?= Helpers::baseUrl('/admin/reports/host-performance?host_id=' . ($row['HostOrgID'] ?? '')) ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
                                    <i class="fas fa-chart-pie"></i> View Feedback
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
