<?php use App\Core\Helpers; ?>

    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Supervised Students Report</h2>
        <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Logbooks</th>
                        <th>Avg. Assessment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && $students->num_rows > 0): ?>
                        <?php while($row = $students->fetch_assoc()): ?>
                            <tr>
                            <td style="font-weight: 500;"><?= htmlspecialchars(($row['FirstName'] ?? '') . ' ' . ($row['LastName'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['Course'] ?? '') ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($row['AttachmentStatus'] ?? 'pending') ?>">
                                    <?= htmlspecialchars($row['AttachmentStatus'] ?? 'Pending') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['log_count']) ?></td>
                                <td>
                                    <?= $row['avg_score'] ? number_format($row['avg_score'], 1) . '%' : 'N/A' ?>
                                </td>
                                <td>
                                    <a href="<?= Helpers::baseUrl('/reports/print/logbook?id=' . $row['StudentID']) ?>" target="_blank" title="Print Logbook" style="color: #4b5563; margin-right: 10px;">
                                        <i class="fas fa-book"></i>
                                    </a>
                                    <a href="<?= Helpers::baseUrl('/reports/print/grades?id=' . $row['StudentID']) ?>" target="_blank" title="Print Assessment" style="color: #4b5563;">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center;">No supervised students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

