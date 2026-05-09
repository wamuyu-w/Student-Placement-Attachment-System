<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="report-header">
    <div class="report-title">
        <h1>Supervisor Workloads</h1>
    </div>
    <a href="<?= Helpers::baseUrl('/reports/print/supervisor-stats') ?>" target="_blank" class="btn report-tag-dark" style="text-decoration: none;">
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
                                <span class="text-bold text-black"><?= $row['student_count'] ?></span>
                                <span class="text-xs text-muted">Students Assigned</span>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $count = $row['student_count'];
                            if ($count >= 10) echo '<span class="report-tag report-tag-dark">High Load</span>';
                            elseif ($count >= 5) echo '<span class="report-tag">Average</span>';
                            else echo '<span class="report-tag report-tag-neutral">Under Capacity</span>';
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 40px; ;">No supervisor statistics available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
