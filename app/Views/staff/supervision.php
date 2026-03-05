<?php use App\Core\Helpers; ?>


    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Assigned Students</h2>
        
        <?php if ($students && $students->num_rows > 0): ?>
            <div class="table-container" style="box-shadow: none; padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th style="padding: 12px;">Student</th>
                            <th style="padding: 12px;">Course</th>
                            <th style="padding: 12px;">Host Organization</th>
                            <th style="padding: 12px;">Status</th>
                            <th style="padding: 12px;">Assessments</th>
                            <th style="padding: 12px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 500;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></div>
                                    <div style="font-size: 0.85em; color: #6b7280;"><?= htmlspecialchars($row['RegistrationNumber']) ?></div>
                                </td>
                                <td style="padding: 12px;"><?= htmlspecialchars($row['Course']) ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars($row['OrganizationName']) ?></td>
                                <td style="padding: 12px;">
                                    <span class="status-badge status-<?= strtolower($row['AttachmentStatus']) ?>">
                                        <?= htmlspecialchars($row['AttachmentStatus']) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <?php if ($row['LastAssessment']): ?>
                                        <div style="font-size: 0.9em;">Last: <?= date('M d', strtotime($row['LastAssessment'])) ?></div>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-size: 0.9em;">None</span>
                                    <?php endif; ?>
                                    <div style="font-size: 0.85em; color: #6b7280;">Total: <?= $row['AssessmentCount'] ?></div>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.85rem;" onclick="openScheduleModal(<?= $row['AttachmentID'] ?>, '<?= htmlspecialchars(addslashes($row['FirstName'] . ' ' . $row['LastName'])) ?>')">
                                            <i class="fas fa-calendar-plus"></i> Schedule
                                        </button>
                                        <button class="btn btn-primary" style="padding: 6px 10px; font-size: 0.85rem;" onclick="openCodeModal(<?= $row['AttachmentID'] ?>, '<?= htmlspecialchars(addslashes($row['FirstName'] . ' ' . $row['LastName'])) ?>')">
                                            <i class="fas fa-clipboard-check"></i> Assess
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-users" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p>No students assigned for supervision yet.</p>
            </div>
        <?php endif; ?>
    </div>


<!-- Assessment Code Modal -->
<div id="codeModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 400px; text-align: center; margin: 100px auto;">
        <h3 style="margin-top: 0; color: #1f2937;">Enter Assessment Code</h3>
        <p style="color: #6b7280; font-size: 0.9em; margin-bottom: 20px;">Please enter the code provided by the Host Supervisor for <span id="codeStudentName" style="font-weight: bold;"></span>.</p>
        
        <form id="codeForm">
            <input type="hidden" name="attachment_id" id="codeAttachmentId">
            <input type="text" name="assessment_code" required placeholder="e.g. AC-1234" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1.1em; text-align: center; letter-spacing: 2px; margin-bottom: 20px; text-transform: uppercase;">
            
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="document.getElementById('codeModal').style.display='none'" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Verify</button>
            </div>
        </form>
    </div>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px; margin: 100px auto;">
        <h3 style="margin-top: 0; color: #1f2937;">Schedule Assessment</h3>
        <p style="color: #6b7280; font-size: 0.9em; margin-bottom: 20px;">Student: <span id="scheduleStudentName" style="font-weight: bold;"></span></p>
        
        <form id="scheduleForm">
            <input type="hidden" name="attachment_id" id="scheduleAttachmentId">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Assessment Type</label>
                <select name="assessment_type" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                    <option value="First Assessment">First Assessment</option>
                    <option value="Final Assessment">Final Assessment</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Date</label>
                <input type="date" name="assessment_date" class="form-control" required min="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Notes (Optional)</label>
                <textarea name="remarks" class="form-control" rows="3" placeholder="Any specific instructions..." style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;"></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('scheduleModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCodeModal(id, name) {
    document.getElementById('codeAttachmentId').value = id;
    document.getElementById('codeStudentName').textContent = name;
    document.getElementById('codeModal').style.display = 'flex';
}

function openScheduleModal(id, name) {
    document.getElementById('scheduleAttachmentId').value = id;
    document.getElementById('scheduleStudentName').textContent = name;
    document.getElementById('scheduleModal').style.display = 'flex';
}

document.getElementById('codeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('<?= Helpers::baseUrl('/assessment/verify-code') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => data.success ? window.location.href = data.redirect : alert(data.message));
});

document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('<?= Helpers::baseUrl('/staff/assessments/schedule') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if(data.success) location.reload();
        });
});
</script>
