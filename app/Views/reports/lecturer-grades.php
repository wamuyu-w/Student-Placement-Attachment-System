<?php use App\Core\Helpers; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Assessment</th>
                <th>Marks</th>
                <th>Organization</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($grades && $grades->num_rows > 0): ?>
                <?php while($row = $grades->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                        <td><span class="status-badge status-neutral" style="font-size: 11px;"><?= htmlspecialchars($row['AssessmentType']) ?></span></td>
                        <td style="font-weight: 700; color: #8B1538;"><?= $row['Marks'] ?>%</td>
                        <td style="font-size: 13px;"><?= htmlspecialchars($row['OrganizationName']) ?></td>
                        <td style="font-size: 11px; color: #6b7280;"><?= date('M d, Y', strtotime($row['AssessmentDate'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">No assessment records found for your students.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
