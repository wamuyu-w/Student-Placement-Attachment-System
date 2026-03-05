<?php use App\Core\Helpers; ?>


    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Student Placement Report</h2>
        <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Period</th>
                        <th>Logbook Entries</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && $students->num_rows > 0): ?>
                        <?php while($row = $students->fetch_assoc()): ?>
                            <tr>
                            <td style="font-weight: 500;"><?= htmlspecialchars(($row['FirstName'] ?? '') . ' ' . ($row['LastName'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['Course'] ?? '') ?></td>
                                <td style="font-size: 0.9em; color: #6b7280;">
                                    <?= date('M Y', strtotime($row['StartDate'])) ?> - <?= date('M Y', strtotime($row['EndDate'])) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['log_count']) ?>
                                </td>
                                <td>
                                    <a href="<?= Helpers::baseUrl('/reports/print/logbook?id=' . $row['StudentID']) ?>" target="_blank" title="Print Logbook" style="color: #4b5563;">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center;">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

