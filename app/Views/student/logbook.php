<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <?php if (!$hasAttachment): ?>
        <div class="alert alert-error">
            You do not have an active ongoing attachment. Please register your placement first.
        </div>
    <?php else: ?>
        <!-- Add Entry Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-8" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 24px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">New Weekly Entry</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="<?= Helpers::baseUrl('/student/logbook/create') ?>" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="form-label">Week Number</label>
                        <input type="number" name="week_number" min="1" max="12" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" required class="form-control">
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label class="form-label">Activities & Achievements</label>
                    <textarea name="description" rows="5" required class="form-control" placeholder="Describe the tasks you performed and skills you learned this week..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Entry
                </button>
            </form>
        </div>

        <!-- Entries List -->
        <div class="bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Logbook History</h2>
            
            <?php if ($entries && $entries->num_rows > 0): ?>
                <div class="entries-list">
                    <?php while($row = $entries->fetch_assoc()): ?>
                        <div class="entry-card" style="border-left: 4px solid var(--primary-color); background: #f9fafb; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <h3 style="font-weight: 600; color: #374151;">Week <?= htmlspecialchars($row['WeekNumber']) ?></h3>
                                <span class="status-badge status-<?= strtolower($row['Status']) ?>" style="padding: 4px 8px; border-radius: 12px; font-size: 0.85em; font-weight: 600; background-color: <?= $row['Status'] == 'Approved' ? '#d1fae5' : '#fff3cd' ?>; color: <?= $row['Status'] == 'Approved' ? '#065f46' : '#856404' ?>;">
                                    <?= htmlspecialchars($row['Status']) ?>
                                </span>
                            </div>
                            <div style="font-size: 0.9rem; color: #6b7280; margin-bottom: 12px;">
                                <?= date('M d', strtotime($row['StartDate'])) ?> - <?= date('M d, Y', strtotime($row['EndDate'])) ?>
                            </div>
                            <p style="color: #4b5563; white-space: pre-line;"><?= htmlspecialchars($row['Description']) ?></p>
                            <?php if (!empty($row['AcademicSupervisorComments']) || !empty($row['HostSupervisorComments'])): ?>
                                <div class="feedback-display" style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #e5e7eb;">
                                    <?php if (!empty($row['AcademicSupervisorComments'])): ?>
                                        <p style="font-size: 0.85rem; color: #374151;"><strong><i class="fas fa-chalkboard-teacher"></i> Lecturer:</strong> <?= htmlspecialchars($row['AcademicSupervisorComments']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($row['HostSupervisorComments'])): ?>
                                        <p style="font-size: 0.85rem; color: #374151; margin-top: 4px;"><strong><i class="fas fa-user-tie"></i> Host:</strong> <?= htmlspecialchars($row['HostSupervisorComments']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color: #6b7280; text-align: center; padding: 20px;">No logbook entries found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>