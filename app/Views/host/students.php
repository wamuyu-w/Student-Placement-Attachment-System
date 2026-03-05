<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div class="card">
        <div class="table-container">
            <?php if ($students && $students->num_rows > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                            <th style="padding: 12px;">Name</th>
                            <th style="padding: 12px;">Course</th>
                            <th style="padding: 12px;">Year</th>
                            <th style="padding: 12px;">Start Date</th>
                            <th style="padding: 12px;">End Date</th>
                            <th style="padding: 12px;">Status</th>
                            <th style="padding: 12px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['YearOfStudy']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['StartDate']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['EndDate']); ?></td>
                                <td style="padding: 12px;">
                                    <span class="status-badge status-<?php echo strtolower($row['AttachmentStatus']); ?>">
                                        <?php echo htmlspecialchars($row['AttachmentStatus']); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <!-- Note: Progress view not yet migrated for Host, using placeholder -->
                                    <button class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem; opacity: 0.5; cursor: not-allowed;" title="Coming Soon">
                                        Progress
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No students currently attached.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
