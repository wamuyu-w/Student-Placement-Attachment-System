<?php use App\Core\Helpers; ?>

<?php if (!$hasAttachment): ?>
    <div class="alert alert-error" style="margin: 24px;">
        You do not have an active ongoing attachment. Please register your placement first.
    </div>
<?php else: ?>
    <?php if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Inactive'): ?>
    
    <!-- New Weekly Entry Form -->
    <div class="logbook-entry-form">
                <div class="logbook-header">
                    <div class="logbook-title-area">
                        <h2>Weekly Logbook Entry</h2>
                        <div class="logbook-subtitle">
                            <i class="far fa-calendar-alt"></i> Fill in your tasks and learnings for the week.
                        </div>
                    </div>
                    <div class="logbook-controls">
                        <form id="submissionForm" action="<?= Helpers::baseUrl('/student/logbook/create') ?>" method="POST" style="display: flex; flex-direction: column; width: 100%;">
                        <div class="week-selector">
                            <label for="week_number" style="font-weight: 600; font-size: 0.9rem; color: #4b5563;">Week No.</label>
                            <input type="number" name="week_number" id="week_number" min="1" max="52" required style="width: 70px;">
                            
                            <label for="end_date" style="font-weight: 600; font-size: 0.9rem; color: #4b5563; margin-left: 8px;">Week Ending:</label>
                            <input type="date" name="end_date" id="end_date" required onchange="updateStartDates()">
                            <input type="hidden" name="start_date" id="start_date">
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" style="margin: 0 0 24px 0; border-radius: 6px; padding: 12px; background: #dcfce7; color: #166534;"><i class="fas fa-check-circle" style="margin-right: 8px;"></i><?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error" style="margin: 0 0 24px 0; border-radius: 6px; padding: 12px; background: #fee2e2; color: #991b1b;"><i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <table class="weekly-logbook-table">
                    <thead>
                        <tr>
                            <th class="col-date">Date</th>
                            <th class="col-task">Task Assigned</th>
                            <th class="col-comments">Student Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $days = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri'];
                        foreach($days as $key => $label): 
                        ?>
                        <tr>
                            <td>
                                <div class="day-label">
                                    <span class="day-name"><?= $label ?></span>
                                    <span class="day-date" id="date_<?= $key ?>">--</span>
                                </div>
                            </td>
                            <td>
                                <textarea name="tasks[<?= $key ?>]" class="logbook-textarea" placeholder="Describe the specific tasks assigned today..."></textarea>
                            </td>
                            <td>
                                <textarea name="comments[<?= $key ?>]" class="logbook-textarea" placeholder="Reflections on learning outcomes or challenges..."></textarea>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="feedback-grid">
                    <div class="feedback-panel">
                        <div class="feedback-header"><i class="fas fa-user-tie"></i> Host Supervisor Feedback</div>
                        <div class="feedback-placeholder">Feedback will appear here after review.</div>
                    </div>
                    <div class="feedback-panel">
                        <div class="feedback-header"><i class="fas fa-chalkboard-teacher"></i> Lecturer Remarks</div>
                        <div class="feedback-placeholder">Assessment feedback will appear here.</div>
                    </div>
                </div>
                
                <div class="submission-policy" style="display: flex; gap: 12px; background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                    <i class="fas fa-info-circle policy-icon" style="color: #3b82f6; font-size: 1.25rem;"></i>
                    <div class="policy-text">
                        <h4 style="color: #1e3a8a; font-size: 0.9rem; font-weight: 700; margin: 0 0 4px 0;">SUBMISSION POLICY</h4>
                        <p style="color: #1e40af; font-size: 0.85rem; margin: 0; line-height: 1.5;">Logbook entries are a critical component of your assessment. Please ensure all "Tasks Assigned" and "Student Comments" are detailed and professional. Weekly submissions must be finalized by Sunday midnight.</p>
                    </div>
                </div>

                <div class="logbook-footer">
                    <button type="submit" class="btn-save-entry" style="margin-left: auto;">
                        Save Entry <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                </form>
            </div>

    <?php endif; ?>

    <!-- Entries List (Outside the layout wrapper) -->
    <div class="bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Logbook History</h2>
        
        <?php if ($entries && $entries->num_rows > 0): ?>
            <div class="entries-list">
                <?php while($row = $entries->fetch_assoc()): 
                    $description = $row['Description'];
                    $isJson = false;
                    $weeklyData = [];
                    if (!empty($description)) {
                        $decoded = json_decode($description, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $isJson = true;
                            $weeklyData = $decoded;
                        }
                    }
                ?>
                    <div class="history-entry-card" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 24px; overflow: hidden;">
                        <div class="history-card-header" style="background: #f8fafc; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 class="history-card-title" style="font-weight: 700; color: #1e293b; font-size: 1.1rem; margin: 0 0 4px 0;">Week <?= htmlspecialchars($row['WeekNumber']) ?></h3>
                                <div class="history-card-dates" style="color: #64748b; font-size: 0.85rem;">
                                    <?= date('M d', strtotime($row['StartDate'])) ?> - <?= date('M d, Y', strtotime($row['EndDate'])) ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?= strtolower($row['Status']) ?>" style="padding: 6px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; background-color: <?= $row['Status'] == 'Approved' ? '#dcfce7' : '#fef9c3' ?>; color: <?= $row['Status'] == 'Approved' ? '#166534' : '#854d0e' ?>; border: 1px solid <?= $row['Status'] == 'Approved' ? '#bbf7d0' : '#fef08a' ?>;">
                                <?= htmlspecialchars($row['Status']) ?>
                            </span>
                        </div>
                        
                        <div class="history-card-body" style="padding: 24px;">
                            <?php if ($isJson): ?>
                                <table class="read-only-table" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                                    <thead>
                                        <tr>
                                            <th class="day-col" style="border: 1px solid #e2e8f0; padding: 12px 16px; text-align: left; background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Day</th>
                                            <th style="border: 1px solid #e2e8f0; padding: 12px 16px; text-align: left; background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Task Assigned</th>
                                            <th style="border: 1px solid #e2e8f0; padding: 12px 16px; text-align: left; background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Student Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // storing the weeks event in the db and trimming them so that they can be stored as a .json file
                                        $daysMapping = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday'];
                                        foreach($daysMapping as $key => $label): 
                                            $task = $weeklyData[$key]['task'] ?? '';
                                            $comment = $weeklyData[$key]['comment'] ?? '';
                                            if (!empty(trim($task)) || !empty(trim($comment))):
                                        ?>
                                        <tr>
                                            <td class="day-col" style="border: 1px solid #e2e8f0; padding: 12px 16px; width: 15%; font-weight: 600; color: #1e293b; background: #f8fafc; font-size: 0.95rem;"><?= $label ?></td>
                                            <td style="border: 1px solid #e2e8f0; padding: 12px 16px; font-size: 0.95rem; color: #334155; line-height: 1.5;"><?= nl2br(htmlspecialchars($task)) ?></td>
                                            <td style="border: 1px solid #e2e8f0; padding: 12px 16px; font-size: 0.95rem; color: #334155; line-height: 1.5;"><?= nl2br(htmlspecialchars($comment)) ?></td>
                                        </tr>
                                        <?php 
                                            endif; 
                                        endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="legacy-description" style="color: #334155; line-height: 1.6; white-space: pre-line; padding: 16px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <?= nl2br(htmlspecialchars($row['Description'])) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Feedback blocks for history -->
                            <div class="feedback-grid">
                                <div class="feedback-panel <?= !empty($row['HostSupervisorComments']) ? 'has-content' : '' ?>">
                                    <div class="feedback-header"><i class="fas fa-user-tie"></i> Host Supervisor Feedback</div>
                                    <?php if (!empty($row['HostSupervisorComments'])): ?>
                                        <div class="feedback-text"><?= nl2br(htmlspecialchars($row['HostSupervisorComments'])) ?></div>
                                    <?php else: ?>
                                        <div class="feedback-placeholder">No feedback provided yet.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="feedback-panel <?= !empty($row['AcademicSupervisorComments']) ? 'has-content' : '' ?>">
                                    <div class="feedback-header"><i class="fas fa-chalkboard-teacher"></i> Lecturer Remarks</div>
                                    <?php if (!empty($row['AcademicSupervisorComments'])): ?>
                                        <div class="feedback-text"><?= nl2br(htmlspecialchars($row['AcademicSupervisorComments'])) ?></div>
                                    <?php else: ?>
                                        <div class="feedback-placeholder">No remarks provided yet.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="color: #6b7280; text-align: center; padding: 20px;">No logbook entries found.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
function updateStartDates() {
    const endDateInput = document.getElementById('end_date').value;
    if (!endDateInput) return;

    const endDate = new Date(endDateInput);
    let fridayOffset = 5 - endDate.getDay(); 
    if (endDate.getDay() === 0) fridayOffset = -2; 
    if (endDate.getDay() === 6) fridayOffset = -1; 
    
    const friday = new Date(endDate);
    friday.setDate(friday.getDate() + fridayOffset);

    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    const shortDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    
    const tableOptions = { month: 'short', day: 'numeric' };

    for (let i = 0; i < 5; i++) {
        const d = new Date(friday);
        d.setDate(d.getDate() - (4 - i));
        
        const dateString = d.toLocaleDateString('en-US', tableOptions);
        const span = document.getElementById('date_' + days[i]);
        if (span) {
            span.textContent = `${dateString}`;
        }

        if (i === 0) {
            const startInput = document.getElementById('start_date');
            if (startInput) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                startInput.value = `${y}-${m}-${dd}`;
            }
        }
    }
}
</script>
