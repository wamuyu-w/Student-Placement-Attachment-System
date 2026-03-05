<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div style="margin-bottom: 20px;">
        <a href="<?= Helpers::baseUrl('/admin/students') ?>" style="color: #6b7280; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>

    <!-- Student Header -->
    <div class="card mb-4">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h2 style="margin: 0 0 5px 0; font-size: 1.5rem;"><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></h2>
                <p style="color: #6b7280; margin: 0;"><?= htmlspecialchars($student['AdmissionNumber']) ?> | <?= htmlspecialchars($student['Course']) ?></p>
            </div>
            <span class="status-badge status-<?= strtolower($student['EligibilityStatus']) ?>">
                <?= htmlspecialchars($student['EligibilityStatus']) ?>
            </span>
        </div>
    </div>

    <?php if ($progress): ?>
        <!-- Attachment Details -->
        <div class="card mb-4" style="border-left: 4px solid #3b82f6;">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #1f2937;">Current Attachment</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Status</span>
                    <span style="font-weight: 600;"><?= htmlspecialchars($progress['AttachmentStatus']) ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Start Date</span>
                    <span style="font-weight: 600;"><?= date('M d, Y', strtotime($progress['StartDate'])) ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">End Date</span>
                    <span style="font-weight: 600;"><?= date('M d, Y', strtotime($progress['EndDate'])) ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Final Report</span>
                    <?php if ($progress['ReportPath']): ?>
                        <a href="<?= Helpers::baseUrl('/uploads/reports/' . $progress['ReportPath']) ?>" target="_blank" style="color: #3b82f6; text-decoration: none;">
                            <i class="fas fa-file-pdf"></i> View Report
                        </a>
                    <?php else: ?>
                        <span style="color: #9ca3af;">Not submitted</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Assessments -->
        <div class="card mb-4">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #1f2937; margin-bottom: 15px;">Assessments</h3>
            <?php if ($assessments && $assessments->num_rows > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 10px;">Date</th>
                            <th style="text-align: left; padding: 10px;">Type</th>
                            <th style="text-align: left; padding: 10px;">Assessor</th>
                            <th style="text-align: left; padding: 10px;">Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($ass = $assessments->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 10px;"><?= date('M d, Y', strtotime($ass['AssessmentDate'])) ?></td>
                                <td style="padding: 10px;"><?= htmlspecialchars($ass['AssessmentType']) ?></td>
                                <td style="padding: 10px;"><?= htmlspecialchars($ass['AssessorName'] ?? 'N/A') ?></td>
                                <td style="padding: 10px; font-weight: bold;"><?= number_format($ass['Marks'], 1) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #6b7280; font-style: italic;">No assessments recorded yet.</p>
            <?php endif; ?>
        </div>

        <!-- Logbook Summary -->
        <div class="card">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #1f2937; margin-bottom: 15px;">Logbook Activity</h3>
            <?php if ($logbookEntries && $logbookEntries->num_rows > 0): ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php while($entry = $logbookEntries->fetch_assoc()): ?>
                        <div style="border-bottom: 1px solid #f3f4f6; padding: 10px 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="font-weight: 600;">Week <?= $entry['WeekNumber'] ?></span>
                                <span style="font-size: 0.85rem; color: #6b7280;"><?= date('M d', strtotime($entry['StartDate'])) ?> - <?= date('M d', strtotime($entry['EndDate'])) ?></span>
                            </div>
                            <div style="font-size: 0.9rem; color: #4b5563;">
                                Status: <span class="status-badge status-<?= strtolower($entry['Status']) ?>" style="font-size: 0.75rem; padding: 2px 6px;"><?= $entry['Status'] ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color: #6b7280; font-style: italic;">No logbook entries found.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>
