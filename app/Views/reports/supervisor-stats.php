<?php use App\Core\Helpers; ?>

<div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Supervisor Workloads</h2>
    <a href="<?= Helpers::baseUrl('/admin/reports/print-supervisor-stats') ?>" target="_blank" class="btn-submit" style="text-decoration: none; font-size: 0.9rem; padding: 10px 16px;">
        <i class="fas fa-print"></i> Export / Print PDF
    </a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Lecturer Name</th>
                <th>Department</th>
                <th>Current Workload</th>
                <th>Workload Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($stats && $stats->num_rows > 0): ?>
                <?php while($row = $stats->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= htmlspecialchars($row['Name']) ?></td>
                        <td style="font-size: 13px;"><?= htmlspecialchars($row['Department'] ?? 'Academic Staff') ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-weight: 700; color: #8B1538;"><?= $row['student_count'] ?></span>
                                <span style="font-size: 11px; color: #6b7280;">Students Assigned</span>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $count = $row['student_count'];
                            if ($count >= 10) echo '<span class="status-badge status-rejected" style="font-size:11px;">High Load</span>';
                            elseif ($count >= 5) echo '<span class="status-badge status-pending" style="font-size:11px;">Average</span>';
                            else echo '<span class="status-badge status-approved" style="font-size:11px;">Under Capacity</span>';
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">No supervisor statistics available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
