<?php use App\Core\Helpers; ?>


    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Attachment Progress Report</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <?php if ($progress): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div class="stat-card" style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                    <div style="color: #6b7280; font-size: 0.9em;">Logbook Entries</div>
                    <div style="font-size: 1.5em; font-weight: bold; color: #374151;"><?= htmlspecialchars($progress['log_count'] ?? '0') ?></div>
                </div>
                <div class="stat-card" style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                    <div style="color: #6b7280; font-size: 0.9em;">Assessments</div>
                    <div style="font-size: 1.5em; font-weight: bold; color: #374151;"><?= htmlspecialchars($progress['assessment_count'] ?? '0') ?></div>
                </div>
                <div class="stat-card" style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                    <div style="color: #6b7280; font-size: 0.9em;">Status</div>
                    <div style="font-size: 1.5em; font-weight: bold; color: #8B1538;"><?= htmlspecialchars($progress['AttachmentStatus'] ?? 'Unknown') ?></div>
                </div>
            </div>

            <div style="margin-bottom: 30px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 15px;">Printable Documents</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="<?= Helpers::baseUrl('/reports/print/logbook') ?>" target="_blank" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-book"></i> Print Logbook
                    </a>
                    <a href="<?= Helpers::baseUrl('/reports/print/grades') ?>" target="_blank" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chart-line"></i> Assessment Summary
                    </a>
                    <?php if (in_array($progress['AttachmentStatus'], ['Completed', 'Cleared'])): ?>
                        <a href="<?= Helpers::baseUrl('/reports/print/completion') ?>" target="_blank" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-certificate"></i> Completion Certificate
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div style="border-top: 1px solid #eee; padding-top: 20px;">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 15px;">Final Report Submission</h3>
                
                <?php if ($progress['ReportPath']): ?>
                    <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Report Submitted</strong><br>
                            <span style="font-size: 0.9em;">Date: <?= date('M d, Y', strtotime($progress['UploadDate'] ?? 'now')) ?></span>
                        </div>
                        <span style="background: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;"><?= htmlspecialchars($progress['ReportStatus'] ?? 'Pending') ?></span>
                    </div>
                <?php else: ?>
                    <form action="<?= Helpers::baseUrl('/student/reports/upload') ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Upload Final Report (PDF)</label>
                            <input type="file" name="final_report" accept=".pdf" required class="form-control">
                            <small style="color: #6b7280;">Ensure your report follows the university guidelines. Max size: 10MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Report
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-folder-open" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p>No active attachment found to report on.</p>
            </div>
        <?php endif; ?>
    </div>

