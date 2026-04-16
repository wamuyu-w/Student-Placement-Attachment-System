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
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Host Organization</span>
                    <span style="font-weight: 600; color: #111827;"><?= htmlspecialchars($hostOrgName ?? 'Not Assigned') ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">University Supervisor</span>
                    <span style="font-weight: 600; color: #111827;"><?= htmlspecialchars($supervisorName ?? 'Not Assigned') ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Status</span>
                    <span style="font-weight: 600;"><?= htmlspecialchars($progress['AttachmentStatus']) ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Duration</span>
                    <span style="font-weight: 600;"><?= date('M d, Y', strtotime($progress['StartDate'])) ?> &mdash; <?= date('M d, Y', strtotime($progress['EndDate'])) ?></span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.85rem; color: #6b7280;">Final Report</span>
                    <?php if ($progress['ReportPath']): ?>
                        <a href="<?= Helpers::baseUrl('/uploads/reports/' . $progress['ReportPath']) ?>" target="_blank" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
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
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 12px 10px;"><?= date('M d, Y', strtotime($ass['AssessmentDate'])) ?></td>
                                <td style="padding: 12px 10px;">
                                    <span style="background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;">
                                        <?= htmlspecialchars($ass['AssessmentType']) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px 10px;"><?= htmlspecialchars($ass['AssessorName'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 10px; font-weight: bold; color: #111827;"><?= number_format($ass['Marks'], 1) ?> / 100</td>
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
            <?php if ($logbookEntries && count($logbookEntries) > 0): ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach($logbookEntries as $entry): ?>
                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: #fafafa;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div>
                                    <span style="font-weight: 600; color: #1f2937; font-size: 1.05rem;">Week <?= $entry['WeekNumber'] ?></span>
                                    <span style="font-size: 0.85rem; color: #6b7280; margin-left: 10px;"><i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($entry['StartDate'])) ?> - <?= date('M d, Y', strtotime($entry['EndDate'])) ?></span>
                                </div>
                                <span class="status-badge status-<?= strtolower($entry['Status']) ?>" style="font-size: 0.8rem; padding: 4px 10px;"><?= $entry['Status'] ?></span>
                            </div>
                            <div style="font-size: 0.95rem; color: #4b5563; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #f3f4f6; margin-top: 10px;">
                                <?php if (!empty($entry['Description'])): ?>
                                    <p style="margin: 0 0 10px 0;"><strong>Student Entries:</strong><br><?= nl2br(htmlspecialchars($entry['Description'])) ?></p>
                                <?php else: ?>
                                    <p style="margin: 0 0 10px 0; font-style: italic; color: #9ca3af;">No logbook entries provided by the student.</p>
                                <?php endif; ?>

                                <?php if (!empty($entry['HostSupervisorComments'])): ?>
                                    <p style="margin: 0 0 10px 0; color: #047857;"><strong>Host Supervisor Remarks:</strong><br><?= nl2br(htmlspecialchars($entry['HostSupervisorComments'])) ?></p>
                                <?php endif; ?>

                                <?php if (!empty($entry['AcademicSupervisorComments'])): ?>
                                    <p style="margin: 0; color: #1d4ed8;"><strong>Academic Supervisor Remarks:</strong><br><?= nl2br(htmlspecialchars($entry['AcademicSupervisorComments'])) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #6b7280; font-style: italic;">No logbook entries found.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>
