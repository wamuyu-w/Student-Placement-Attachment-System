<?php use App\Core\Helpers; ?>

<div class="activity-section">
    <div class="section-header">
        <h2>Organization Performance Summary</h2>
    </div>
    
    <div class="table-container" style="box-shadow: none; border: none; padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Evaluation Date</th>
                    <th>Host Comment Preview</th>
                    <th>Overall verdict</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($performance && $performance->num_rows > 0): ?>
                    <?php while($row = $performance->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                            <td style="font-size: 12px;"><?= date('M d, Y', strtotime($row['LogDate'])) ?></td>
                            <td style="font-size: 13px; font-style: italic; color: #4b5563;">
                                "<?= htmlspecialchars(substr($row['HostComment'], 0, 80)) ?>..."
                            </td>
                            <td>
                                <span class="status-badge status-approved" style="font-size: 11px;">Satisfactory</span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">No evaluative comments found yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
