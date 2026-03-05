<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Pending Logbook Reviews</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <?php if ($entries && $entries->num_rows > 0): ?>
            <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th style="padding: 12px;">Student</th>
                        <th style="padding: 12px;">Week</th>
                        <th style="padding: 12px;">Dates</th>
                        <th style="padding: 12px;">Activities</th>
                        <th style="padding: 12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $entries->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                            <td style="padding: 12px;">Week <?= htmlspecialchars($row['WeekNumber']) ?></td>
                            <td style="padding: 12px; font-size: 0.9em; color: #6b7280;">
                                <?= date('M d', strtotime($row['StartDate'])) ?> - <?= date('M d', strtotime($row['EndDate'])) ?>
                            </td>
                            <td style="padding: 12px;">
                                <div style="max-height: 100px; overflow-y: auto; font-size: 0.9em;">
                                    <?= nl2br(htmlspecialchars($row['Description'])) ?>
                                </div>
                            </td>
                            <td style="padding: 12px;">
                                <form action="<?= Helpers::baseUrl('/logbook/review') ?>" method="POST">
                                    <input type="hidden" name="logbook_id" value="<?= $row['LogbookID'] ?>">
                                    <div class="form-group" style="margin-bottom: 8px;">
                                        <textarea name="comment" class="form-control" rows="1" placeholder="Add optional feedback..."></textarea>
                                    </div>
                                    <button type="submit" name="status" value="Approved" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85em; background-color: #10b981; width: 100%;">
                                        <i class="fas fa-check"></i> Approve Entry
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p>All caught up! No pending logbooks to review.</p>
            </div>
        <?php endif; ?>
    </div>
</div>