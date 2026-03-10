<?php use App\Core\Helpers; ?>

<div class="activity-section">
    <div class="section-header">
        <h2>Active Student Placements</h2>
        <a href="<?= Helpers::baseUrl('/host/reports/host-performance') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-chart-line"></i> Performance Summary
        </a>
    </div>

    <div class="table-container" style="box-shadow: none; border: none; padding: 0;">
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
                            <td>
                                <span style="font-weight: 600;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></span>
                            </td>
                            <td style="font-size: 0.85rem;"><?= htmlspecialchars($row['Course']) ?></td>
                            <td style="font-size: 0.75rem; color: #6b7280;">
                                <?= date('M d, Y', strtotime($row['StartDate'])) ?> - <?= date('M d, Y', strtotime($row['EndDate'])) ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 13px; font-weight: bold; color: #8B1538;"><?= $row['log_count'] ?> wks</span>
                                    <div style="width: 40px; background: #f3f4f6; height: 4px; border-radius: 2px;">
                                        <div style="background: #8B1538; height: 100%; width: <?= min(($row['log_count']/12)*100, 100) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="<?= Helpers::baseUrl('/host/reports/host-performance?student_id=' . $row['StudentID']) ?>" class="btn btn-outline" style="padding: 4px 10px; font-size: 11px;">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">No students are currently placed in your organization.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Restriction Notice -->
<div class="alert alert-info" style="margin-top: 24px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 16px; border-radius: 8px;">
    <div style="display: flex; gap: 12px;">
        <i class="fas fa-info-circle" style="margin-top: 3px;"></i>
        <div>
            <p style="font-weight: 700; margin-bottom: 4px;">Privacy Policy Reminder</p>
            <p style="font-size: 12px; line-height: 1.4;">Host Organizations have access to student weekly performance summaries and organization-specific evaluations. Access to comprehensive academic logbooks and grading history is restricted to University staff and the students themselves.</p>
        </div>
    </div>
</div>
