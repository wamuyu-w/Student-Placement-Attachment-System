<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div style="margin-bottom: 20px;">
        <a href="<?= Helpers::baseUrl('/host/students') ?>" style="color: #6b7280; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>

    <!-- Student Header -->
    <div class="card mb-4" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h2 style="margin: 0 0 5px 0; font-size: 1.5rem;"><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></h2>
                <p style="color: #6b7280; margin: 0;"><?= htmlspecialchars($student['AdmissionNumber']) ?> | <?= htmlspecialchars($student['Course']) ?></p>
            </div>
            <span class="status-badge status-<?= strtolower($student['EligibilityStatus'] ?? 'pending') ?>">
                <?= htmlspecialchars($student['EligibilityStatus'] ?? 'Pending') ?>
            </span>
        </div>
    </div>

    <?php if ($progress): ?>
        <!-- Attachment Details -->
        <div class="card mb-4" style="border-left: 4px solid #3b82f6; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #1f2937;">Current Attachment</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
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
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Logbook Summary -->
        <div class="card" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; font-size: 1.1rem; color: #1f2937; margin-bottom: 15px;">Logbook Activity</h3>
            <?php if ($logbookEntries && count($logbookEntries) > 0): ?>
                <div style="max-height: 600px; overflow-y: auto; padding-right: 10px;">
                    <?php foreach($logbookEntries as $entry): ?>
                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fafafa;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div>
                                    <span style="font-weight: 600; color: #1f2937; font-size: 1.05rem;">Week <?= $entry['WeekNumber'] ?></span>
                                    <span style="font-size: 0.85rem; color: #6b7280; margin-left: 10px;"><i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($entry['StartDate'])) ?> - <?= date('M d, Y', strtotime($entry['EndDate'])) ?></span>
                                </div>
                                <span class="status-badge status-<?= strtolower($entry['Status']) ?>" style="font-size: 0.8rem; padding: 4px 10px;"><?= $entry['Status'] ?></span>
                            </div>

                            <!-- Student Entries -->
                            <div style="font-size: 0.95rem; color: #4b5563; background: #fff; padding: 12px; border-radius: 6px; border: 1px solid #f3f4f6; margin-bottom: 15px;">
                                <div style="margin-bottom: 12px; font-weight: 600; color: #374151;">Student Entries:</div>
                                <?php 
                                    $desc = $entry['Description'] ?? '';
                                    if (empty($desc)) {
                                        echo '<p style="margin: 0; font-style: italic; color: #9ca3af;">No logbook entries provided by the student.</p>';
                                    } else {
                                        $decoded = json_decode($desc, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $daysMapping = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri'];
                                            echo "<div style='display: flex; flex-direction: column; gap: 8px;'>";
                                            foreach($daysMapping as $key => $label) {
                                                $task = $decoded[$key]['task'] ?? '';
                                                $studentComment = $decoded[$key]['comment'] ?? '';
                                                if (!empty(trim($task)) || !empty(trim($studentComment))) {
                                                    echo "<div style='background: #f8fafc; padding: 10px; border-radius: 4px; border: 1px solid #e2e8f0;'>";
                                                    echo "<strong style='color: #1e293b; display: block; margin-bottom: 6px;'>" . $label . "</strong>";
                                                    if (!empty(trim($task))) echo "<div style='margin-bottom:6px;'><span style='color:#64748b; font-size: 0.85em; display: inline-block; width: 60px;'>Task:</span> " . nl2br(htmlspecialchars($task)) . "</div>";
                                                    if (!empty(trim($studentComment))) echo "<div><span style='color:#64748b; font-size: 0.85em; display: inline-block; width: 60px;'>Comment:</span> " . nl2br(htmlspecialchars($studentComment)) . "</div>";
                                                    echo "</div>";
                                                }
                                            }
                                            echo "</div>";
                                        } else {
                                            echo '<p style="margin: 0;">' . nl2br(htmlspecialchars($desc)) . '</p>';
                                        }
                                    }
                                ?>
                            </div>

                            <!-- Comment Form for Host -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px; color: #047857;">
                                    <i class="fas fa-edit" style="margin-right: 6px;"></i>
                                    <strong style="font-size: 0.95rem;">Host Supervisor Remarks</strong>
                                </div>
                                <form action="<?= Helpers::baseUrl('/logbook/add-comment') ?>" method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="hidden" name="logbook_id" value="<?= $entry['LogbookID'] ?>">
                                    <input type="hidden" name="student_id" value="<?= $student['StudentID'] ?>">
                                    
                                    <textarea name="comment" rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px; font-family: inherit; font-size: 0.9rem; resize: vertical; margin-bottom: 8px;" placeholder="Add or update your remarks here..."><?= htmlspecialchars($entry['HostSupervisorComments'] ?? '') ?></textarea>
                                    
                                    <div style="text-align: right;">
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 16px; font-size: 0.85rem; background-color: #10b981; border: none; border-radius: 4px; color: white; cursor: pointer;">
                                            <i class="fas fa-save"></i> Save Remarks
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #6b7280; font-style: italic; text-align: center; padding: 20px;">No logbook entries found for this student.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>
