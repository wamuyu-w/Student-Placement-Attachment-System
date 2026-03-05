<?php use App\Core\Helpers; ?>


    <div class="header-actions" style="margin-bottom: 20px; text-align: right;">
        <a href="<?= Helpers::baseUrl('/reports/print/supervisors') ?>" target="_blank" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-print"></i> Print Supervisor List
        </a>
    </div>

    <div class="card mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Placements by Faculty</h2>
        <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Total Placements</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($placementStats && $placementStats->num_rows > 0): ?>
                        <?php while($row = $placementStats->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['Faculty'] ?? 'Unknown') ?></td>
                                <td style="font-weight: bold;"><?= htmlspecialchars($row['count']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" style="text-align: center;">No data available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Top Host Organizations</h2>
        <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Students Attached</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hostStats && $hostStats->num_rows > 0): ?>
                        <?php while($row = $hostStats->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['OrganizationName'] ?? 'Unknown') ?></td>
                                <td style="font-weight: bold;"><?= htmlspecialchars($row['student_count']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" style="text-align: center;">No data available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

