<?php use App\Core\Helpers; ?>

<div class="activity-section">
    <div class="section-header">
        <h2>Assigned Student Progress</h2>
        <a href="<?= Helpers::baseUrl('/staff/reports/lecturer-grades') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-file-invoice"></i> Grade Summary
        </a>
    </div>
    
    <div class="table-container" style="box-shadow: none; border: none; padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Logbook</th>
                    <th>Avg. Score</th>
                    <th>Actions</th>
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
                            <td>
                                <span class="status-badge <?= strtolower($row['AttachmentStatus']) === 'completed' ? 'status-approved' : 'status-active' ?>">
                                    <?= htmlspecialchars($row['AttachmentStatus']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 13px; font-weight: bold;"><?= $row['log_count'] ?> wks</span>
                                    <div style="width: 50px; background: #f3f4f6; height: 6px; border-radius: 3px; overflow: hidden;">
                                        <div style="background: #8B1538; height: 100%; width: <?= min(($row['log_count']/12)*100, 100) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: <?= ($row['avg_score'] >= 50) ? '#059669' : '#dc2626' ?>">
                                    <?= $row['avg_score'] ? number_format($row['avg_score'], 1) . '%' : 'N/A' ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 12px;">
                                    <a href="<?= Helpers::baseUrl('/reports/print/logbook?id=' . $row['StudentID'] . '&session=' . ($row['AttachmentID'] ?? '')) ?>" target="_blank" title="Print Logbook" style="color: #6b7280;">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="<?= Helpers::baseUrl('/assessment/view?id=' . $row['StudentID']) ?>" title="View Assessment" style="color: #6b7280;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">No supervised students assigned to you yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
