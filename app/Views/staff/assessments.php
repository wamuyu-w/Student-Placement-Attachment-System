<?php use App\Core\Helpers; ?>


    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0;">Students Pending Assessment</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <?php if ($students && $students->num_rows > 0): ?>
            <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th style="padding: 12px;">Student Name</th>
                        <th style="padding: 12px;">Reg. Number</th>
                        <th style="padding: 12px;">Host Organization</th>
                        <th style="padding: 12px;">Status</th>
                        <th style="padding: 12px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                            <td style="padding: 12px; color: #6b7280;"><?= htmlspecialchars($row['RegistrationNumber']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($row['OrganizationName']) ?></td>
                            <td style="padding: 12px;">
                                <span class="status-badge status-neutral">
                                    <?= $row['AssessmentCount'] == 0 ? 'First Assessment' : 'Final Assessment' ?>
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <button onclick="openCodeModal(<?= $row['AttachmentID'] ?>, '<?= htmlspecialchars(addslashes($row['FirstName'])) ?>')" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.9em;">
                                    <i class="fas fa-clipboard-check"></i> Assess
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-user-graduate" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p>No students currently assigned for assessment.</p>
            </div>
        <?php endif; ?>
    </div>


<!-- Assessment Code Modal -->
<div id="codeModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 400px; text-align: center;">
        <h3 style="margin-top: 0; color: #1f2937;">Enter Assessment Code</h3>
        <p style="color: #6b7280; font-size: 0.9em; margin-bottom: 20px;">Please enter the code provided by the Host Supervisor for <span id="studentName" style="font-weight: bold;"></span>.</p>
        
        <form id="codeForm">
            <input type="hidden" name="attachment_id" id="attachmentId">
            <input type="text" name="assessment_code" required placeholder="e.g. AC-1234" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1.1em; text-align: center; letter-spacing: 2px; margin-bottom: 20px; text-transform: uppercase;">
            
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="document.getElementById('codeModal').style.display='none'" style="flex: 1; padding: 10px; border: 1px solid #d1d5db; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button type="submit" style="flex: 1; padding: 10px; border: none; background: #8B1538; color: white; border-radius: 4px; cursor: pointer;">Verify</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCodeModal(id, name) {
    document.getElementById('attachmentId').value = id;
    document.getElementById('studentName').textContent = name;
    document.getElementById('codeModal').style.display = 'flex';
}

document.getElementById('codeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= Helpers::baseUrl('/assessment/verify-code') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.message);
        }
    });
});
</script>
