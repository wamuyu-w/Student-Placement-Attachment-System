<?php use App\Core\Helpers; ?>

<?php if (isset($_GET['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<?php if (!empty($sessions)): ?>
    <?php foreach($sessions as $progress): ?>
        <div class="card" style="margin-bottom: 24px;">
            <div style="border-bottom: 1px solid #eee; padding-bottom: 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827;"><?= htmlspecialchars($progress['OrganizationName'] ?? 'Attachment Session') ?></h3>
                    <p style="color: #6b7280; font-size: 0.85rem;"><?= date('M d, Y', strtotime($progress['StartDate'])) ?> - <?= $progress['EndDate'] ? date('M d, Y', strtotime($progress['EndDate'])) : 'Ongoing' ?></p>
                </div>
                <span class="status-badge <?= strtolower($progress['AttachmentStatus']) === 'completed' ? 'status-approved' : 'status-active' ?>">
                    <?= htmlspecialchars($progress['AttachmentStatus']) ?>
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                <!-- Progress Stats -->
                <div>
                    <div style="margin-bottom: 16px;">
                        <span style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px;">Logbook Progress</span>
                        <div style="background: #f3f4f6; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 4px;">
                            <div style="width: <?= min(($progress['log_count'] / 12) * 100, 100) ?>%; background: #8B1538; height: 100%;"></div>
                        </div>
                        <span style="font-size: 0.75rem; color: #6b7280;"><?= $progress['log_count'] ?> of 12 weeks filled</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Assessments</span>
                        <span style="font-size: 1.5rem; font-weight: 700; color: #111827;"><?= $progress['assessment_count'] ?> / 2</span>
                        <span style="display: block; font-size: 0.75rem; color: #6b7280;"><?= ($progress['assessment_count'] >= 2) ? 'Final Assessment Conducted' : 'Pending Final Assessment' ?></span>
                    </div>
                </div>

                <!-- Document Status -->
                <div style="border-left: 1px solid #eee; padding-left: 24px;">
                    <h4 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; margin-bottom: 12px; letter-spacing: 0.05em;">Status Tracking</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.875rem;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas <?= $progress['assessment_count'] >= 1 ? 'fa-check-circle' : 'fa-circle' ?>" style="color: <?= $progress['assessment_count'] >= 1 ? '#059669' : '#d1d5db' ?>;"></i>
                            <span>First Assessment</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas <?= $progress['assessment_count'] >= 2 ? 'fa-check-circle' : 'fa-circle' ?>" style="color: <?= $progress['assessment_count'] >= 2 ? '#059669' : '#d1d5db' ?>;"></i>
                            <span>Final Assessment</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas <?= $progress['ReportStatus'] === 'Approved' ? 'fa-check-circle' : ($progress['ReportPath'] ? 'fa-hourglass-half' : 'fa-times-circle') ?>" style="color: <?= $progress['ReportStatus'] === 'Approved' ? '#059669' : ($progress['ReportPath'] ? '#d97706' : '#dc2626') ?>;"></i>
                            <span>Final Report: <strong><?= $progress['ReportStatus'] ?? 'Missing' ?></strong></span>
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
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">No Attachment Data Found</h3>
        <p style="color: #6b7280;">You have not been placed in any host organization yet.</p>
    </div>
<?php endif; ?>

<!-- Upload Modal -->
<div id="uploadModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 32px; position: relative;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">Upload Final Report</h2>
        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 24px;">Upload your compiled logbook and final report in PDF format for university verification.</p>
        <form action="<?= Helpers::baseUrl('/student/reports/upload') ?>" method="POST" enctype="multipart/form-data">
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
