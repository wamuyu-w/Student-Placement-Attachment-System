<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<?php if (isset($_GET['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<?php if (!empty($sessions)): ?>
    <?php foreach($sessions as $progress): ?>
        <div class="card" style="margin-bottom: 24px;">
            <div class="report-card-header" style="margin-bottom: 16px;">
                <div class="report-title">
                    <h3 style="margin:0;"><?= htmlspecialchars($progress['OrganizationName'] ?? 'Attachment Session') ?></h3>
                    <p class="report-subtitle"><?= date('M d, Y', strtotime($progress['StartDate'])) ?> - <?= $progress['EndDate'] ? date('M d, Y', strtotime($progress['EndDate'])) : 'Ongoing' ?></p>
                </div>
                <span class="report-tag <?= strtolower($progress['AttachmentStatus']) === 'completed' ? 'report-tag-dark' : 'report-tag-neutral' ?>">
                    <?= htmlspecialchars($progress['AttachmentStatus']) ?>
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; padding: 0 20px 20px;">
                <!-- Progress Stats -->
                <div>
                    <div style="margin-bottom: 16px;">
                        <span class="text-bold text-small" style="display: block; margin-bottom: 8px;">Logbook Progress</span>
                        <div class="progress-container">
                            <div class="progress-fill" style="width: <?= min(($progress['log_count'] / 12) * 100, 100) ?>%;"></div>
                        </div>
                        <span class="text-xs text-muted"><?= $progress['log_count'] ?> of 12 weeks filled</span>
                    </div>
                    <div>
                        <span class="text-bold text-small" style="display: block; margin-bottom: 4px;">Assessments</span>
                        <span class="text-bold text-black" style="font-size: 1.5rem; display:block;"><?= $progress['assessment_count'] ?> / 2</span>
                        <span class="text-xs text-muted"><?= ($progress['assessment_count'] >= 2) ? 'Final Assessment Conducted' : 'Pending Final Assessment' ?></span>
                    </div>
                </div>

                <!-- Document Status -->
                <div style="border-left: 1px solid #eee; padding-left: 24px;">
                    <h4 class="text-xs text-muted text-bold" style="text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.05em;">Status Tracking</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.875rem;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas <?= $progress['assessment_count'] >= 1 ? 'fa-check-circle' : 'fa-circle' ?>" class="text-black"></i>
                            <span>First Assessment</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas <?= $progress['assessment_count'] >= 2 ? 'fa-check-circle' : 'fa-circle' ?>" class="text-black"></i>
                            <span>Final Assessment</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas <?= $progress['ReportStatus'] === 'Approved' ? 'fa-check-circle' : ($progress['ReportPath'] ? 'fa-hourglass-half' : 'fa-times-circle') ?>" class="text-black"></i>
                            <span class="text-small">Final Report: <strong class="text-black"><?= $progress['ReportStatus'] ?? 'Missing' ?></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div style="border-left: 1px solid #eee; padding-left: 24px; display: flex; flex-direction: column; gap: 8px;">
                    <?php if ($progress['AttachmentStatus'] === 'Completed' && $progress['ReportStatus'] === 'Approved'): ?>
                        <a href="<?= Helpers::baseUrl('/reports/print/completion?id=' . $_SESSION['student_id'] . '&session=' . $progress['AttachmentID']) ?>" target="_blank" class="btn btn-primary" style="width: 100%; font-size: 0.875rem;">
                            <i class="fas fa-certificate"></i> Print Completion Letter
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= Helpers::baseUrl('/reports/print/logbook?id=' . $_SESSION['student_id'] . '&session=' . $progress['AttachmentID']) ?>" target="_blank" class="btn btn-outline" style="width: 100%; font-size: 0.875rem;">
                        <i class="fas fa-print"></i> Print Logbook
                    </a>

                    <?php if (in_array(strtolower($progress['AttachmentStatus']), ['ongoing', 'active']) && !$progress['ReportPath']): ?>
                        <button onclick="document.getElementById('uploadModal').style.display='flex'" class="btn btn-secondary" style="width: 100%; font-size: 0.875rem;">
                            <i class="fas fa-upload"></i> Upload Final Report
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card text-center" style="padding: 60px;">
        <i class="fas fa-chart-line" style="font-size: 48px; color: #d1d5db; margin-bottom: 20px;"></i>
        <h3 class="text-bold text-black" style="font-size: 1.25rem; margin-bottom: 8px;">No Attachment Data Found</h3>
        <p class="text-muted">You have not been placed in any host organization yet.</p>
    </div>
<?php endif; ?>

<!-- Upload Modal -->
<div id="uploadModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 32px; position: relative;">
        <h2 class="text-bold text-black" style="font-size: 1.5rem; margin-bottom: 8px;">Upload Final Report</h2>
        <p class="text-muted text-small" style="margin-bottom: 24px;">Upload your compiled logbook and final report in PDF format for university verification.</p>
        <form action="<?= Helpers::baseUrl('/student/reports/upload') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="form-group">
                <label class="form-label">Choose PDF File</label>
                <input type="file" name="final_report" accept=".pdf" required class="form-control">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="document.getElementById('uploadModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>
