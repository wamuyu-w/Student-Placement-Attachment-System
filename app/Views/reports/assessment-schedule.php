<?php use App\Core\Helpers; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Student Details</th>
                <th>Host Organization</th>
                <th>Assigned Lecturer</th>
                <th>Assessment</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($schedule && $schedule->num_rows > 0): ?>
                <?php while($row = $schedule->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></span>
                                <span style="font-size: 11px; color: #6b7280;"><?= htmlspecialchars($row['AdmNumber']) ?></span>
                            </div>
                        </td>
                        <td style="font-size: 13px;"><?= htmlspecialchars($row['OrganizationName'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($row['LecturerName']): ?>
                                <span class="status-badge status-approved" style="font-size: 11px;"><?= htmlspecialchars($row['LecturerName']) ?></span>
                            <?php else: ?>
                                <span class="status-badge status-pending" style="font-size: 11px;">Not Assigned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['AssessmentDate']): ?>
                                <div style="display: flex; flex-direction: column; font-size: 12px;">
                                    <span style="font-weight: 600;"><?= date('M d, Y', strtotime($row['AssessmentDate'])) ?></span>
                                    <span style="color: #6b7280;"><?= htmlspecialchars($row['AssessmentType']) ?></span>
                                </div>
                            <?php else: ?>
                                <span style="color: #9ca3af; font-size: 12px;">Not Scheduled</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 11px; color: #6b7280;">
                            <?= date('M d', strtotime($row['StartDate'])) ?> - <?= date('M d, Y', strtotime($row['EndDate'])) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">No ongoing attachments found in the schedule.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
