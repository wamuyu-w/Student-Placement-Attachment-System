<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="card">
    <div class="report-card-header">
        <h2 class="report-card-title">Active Student Placements</h2>
        <a href="<?= Helpers::baseUrl('/host/reports/host-performance') ?>" class="btn report-tag-dark">
            <i class="fas fa-chart-line"></i> Performance Summary
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Period</th>
                    <th>Logbook Status</th>
                    <th>Evaluation</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students && $students->num_rows > 0): ?>
                    <?php while($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td class="text-bold">
                                <?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?>
                            </td>
                            <td class="text-small text-muted"><?= htmlspecialchars($row['Course']) ?></td>
                            <td class="text-xs text-muted">
                                <?= date('M d, Y', strtotime($row['StartDate'])) ?> - <?= date('M d, Y', strtotime($row['EndDate'])) ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="text-bold text-black text-small"><?= $row['log_count'] ?> wks</span>
                                    <div class="progress-container progress-sm" style="width: 40px;">
                                        <div class="progress-fill" style="width: <?= min(($row['log_count']/12)*100, 100) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="<?= Helpers::baseUrl('/host/reports/host-performance?student_id=' . $row['StudentID']) ?>" class="btn report-tag-neutral text-xs" style="padding: 4px 10px; text-decoration: none;">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px;" class="text-muted">No students are currently placed in your organization.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Restriction Notice -->
<div class="card" style="margin-top: 24px;">
    <div style="display: flex; gap: 12px; align-items: flex-start;">
        <i class="fas fa-info-circle text-black" style="margin-top: 3px;"></i>
        <div>
            <p class="text-bold text-black" style="margin: 0 0 4px 0;">Privacy Policy Reminder</p>
            <p class="text-xs text-muted" style="margin: 0; line-height: 1.4;">Host Organizations have access to student weekly performance summaries and organization-specific evaluations. Access to comprehensive academic logbooks and grading history is restricted to University staff and the students themselves.</p>
        </div>
    </div>
</div>
