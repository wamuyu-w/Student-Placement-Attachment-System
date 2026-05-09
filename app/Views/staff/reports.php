<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="activity-section">
    <div class="report-card-header">
        <h2 class="report-card-title">Assigned Student Progress</h2>
        <a href="<?= Helpers::baseUrl('/staff/reports/lecturer-grades') ?>" class="btn btn-outline text-small">
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
                            <td class="text-bold">
                                <?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?>
                            </td>
                            <td class="text-small text-muted"><?= htmlspecialchars($row['Course']) ?></td>
                            <td>
                                <span class="report-tag report-tag-neutral">
                                    <?= htmlspecialchars($row['AttachmentStatus']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                     <span class="text-bold text-black text-small"><?= $row['log_count'] ?> wks</span>
                                     <div class="progress-container progress-sm" style="width: 50px;">
                                         <div class="progress-fill" style="width: <?= min(($row['log_count']/12)*100, 100) ?>%"></div>
                                     </div>
                                 </div>
                            </td>
                            <td class="text-bold text-black">
                                 <?= $row['avg_score'] ? number_format($row['avg_score'], 1) . '%' : 'N/A' ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 12px;">
                                    <a href="<?= Helpers::baseUrl('/reports/print/logbook?id=' . $row['StudentID'] . '&session=' . ($row['AttachmentID'] ?? '')) ?>" target="_blank" title="Print Logbook" class="text-muted">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="<?= Helpers::baseUrl('/assessment/view?id=' . $row['StudentID']) ?>" title="View Assessment" class="text-muted">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 40px;" class="text-muted">No supervised students assigned to you yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
