<?php use App\Core\Helpers; ?>

<div class="supervisor-container">
    <h1 class="supervisor-title">My Supervisor</h1>

    <?php if (!empty($supervisors)): ?>
        <?php foreach ($supervisors as $index => $sup): ?>
            <div class="supervisor-card">
                <div class="supervisor-info">
                    <div class="supervisor-avatar">
                        <?= \App\Core\Helpers::getAvatar($sup['Name'], '#e5e7eb', '#374151', '', 'width: 100%; height: 100%;'); ?>
                    </div>
                    <div class="supervisor-details">
                        <h3><?= htmlspecialchars($sup['Name']) ?></h3>
                        <p><strong>Department:</strong> <?= htmlspecialchars($sup['Department']) ?></p>
                        <p class="supervisor-role">Supervisor</p>
                    </div>
                </div>
                <button class="btn-contact" onclick="openContactModal(<?= $index ?>)">Contact Supervisor</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="section-card" style="text-align: center; padding: 40px; color: #6b7280; margin-bottom: 24px;">
            <i class="fas fa-user-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
            <p>You have not been assigned an academic supervisor yet.</p>
        </div>
    <?php endif; ?>

    <div class="supervisor-grid">
        <!-- Supervision Progress Tracker -->
        <div class="section-card">
            <div class="section-header">
                <h2>Supervision Progress Tracker</h2>
            </div>
            
            <div class="tracker-list">
                <?php 
                $hasAssessments = !empty($assessments);
                if ($hasAssessments): 
                    $totalAssessments = count($assessments);
                    foreach ($assessments as $index => $ass):
                        // Assessments ordered DESC — index 0 is newest (Final), last is oldest (First)
                        $isFirst = ($index === $totalAssessments - 1);
                        $title = $isFirst ? "First Assessment" : "Final Assessment";
                        $isCompleted = ($ass['Status'] === 'Completed');
                        $statusText = $isCompleted ? "(Completed)" : "(Scheduled)";
                ?>
                <div class="progress-item">
                    <div class="progress-icon">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <div class="progress-content">
                        <div class="progress-top">
                            <div>
                                <span class="assessment-title"><?= $title ?> <?= $statusText ?></span>
                                <span class="assessment-date"><?= date('F jS, Y', strtotime($ass['AssessmentDate'])) ?></span>
                            </div>
                        </div>
                        <?php if ($isCompleted): ?>
                        <div class="assessment-meta">
                            <span class="score-badge">Score: <?= number_format($ass['Marks'], 0) ?>/100</span>
                            <span class="assessor-name">Assessed by: <?= htmlspecialchars($ass['LecturerName']) ?></span>
                            <a href="<?= Helpers::baseUrl('/assessment/view?id=' . $ass['AssessmentID']) ?>" target="_blank" class="btn-view-form" style="text-decoration: none;">View Form</a>
                        </div>
                        <?php else: ?>
                        <div class="assessment-meta">
                            <span class="status-scheduled">Scheduled</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    endforeach;
                else: 
                ?>
                    <!-- Fallback mockup data if no real assessments exist yet -->
                    <div class="progress-item">
                        <div class="progress-icon">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div class="progress-content">
                            <div class="progress-top">
                                <div>
                                    <span class="assessment-title">First Assessment (Pending)</span>
                                    <span class="assessment-date">To be scheduled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Feedback History -->
        <div class="section-card">
            <div class="section-header">
                <h2>Feedback History</h2>
            </div>
            
            <div class="feedback-list">
                <?php if ($hasAssessments): ?>
                    <?php foreach ($assessments as $ass): if (!empty($ass['Remarks'])): ?>
                    <div class="feedback-item">
                        <h3><?= htmlspecialchars($ass['AssessmentType'] ?? 'Assessment') ?> Feedback</h3>
                        <p class="feedback-text"><?= nl2br(htmlspecialchars($ass['Remarks'])) ?></p>
                    </div>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="feedback-item">
                        <p class="feedback-text" style="font-style: italic; color: #9ca3af;">No feedback history available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Contact Supervisor Modal -->
<div id="contactModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="max-width: 440px;">
        <div class="modal-header">
            <h3><i class="fas fa-address-card"></i> Supervisor Contact Info</h3>
            <button class="modal-close" onclick="closeContactModal()">&times;</button>
        </div>
        <div class="modal-body" id="contactModalBody">
            <!-- Populated by JS -->
        </div>
    </div>
</div>

<style>
    /* Contact Modal Styles */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .modal-content {
        background: #fff; border-radius: 12px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        overflow: hidden; animation: slideUp 0.25s ease;
    }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 20px; background: #8B1538; color: #fff;
    }
    .modal-header h3 { margin: 0; font-size: 1rem; }
    .modal-header h3 i { margin-right: 8px; }
    .modal-close {
        background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer;
        line-height: 1; padding: 0 4px;
    }
    .modal-close:hover { opacity: 0.7; }
    .modal-body { padding: 24px 20px; }
    .contact-detail { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
    .contact-detail i { color: #8B1538; font-size: 1rem; margin-top: 3px; width: 20px; text-align: center; }
    .contact-detail .label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    .contact-detail .value { font-size: 0.95rem; color: #1f2937; font-weight: 500; }
    .contact-actions { display: flex; gap: 10px; margin-top: 20px; padding-top: 16px; border-top: 1px solid #e5e7eb; }
    .contact-actions a {
        flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500;
        text-decoration: none; transition: all 0.2s;
    }
    .btn-email-sup { background: #8B1538; color: #fff; }
    .btn-email-sup:hover { background: #6d1130; }
</style>

<script>
    // Supervisor data from PHP
    const supervisorData = <?= json_encode(array_map(function($s) {
        return [
            'name' => htmlspecialchars($s['Name']),
            'department' => htmlspecialchars($s['Department']),
            'faculty' => htmlspecialchars($s['Faculty'] ?? ''),
            'staffNumber' => htmlspecialchars($s['StaffNumber'] ?? ''),
            'assignedDate' => $s['AssignedDate'] ?? ''
        ];
    }, $supervisors ?? [])) ?>;

    function openContactModal(index) {
        const sup = supervisorData[index];
        if (!sup) return;

        const assignedFormatted = sup.assignedDate
            ? new Date(sup.assignedDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
            : 'N/A';

        let html = `
            <div class="contact-detail">
                <i class="fas fa-user"></i>
                <div><div class="label">Name</div><div class="value">${sup.name}</div></div>
            </div>
            <div class="contact-detail">
                <i class="fas fa-building"></i>
                <div><div class="label">Department</div><div class="value">${sup.department || 'N/A'}</div></div>
            </div>`;

        if (sup.faculty) {
            html += `
            <div class="contact-detail">
                <i class="fas fa-university"></i>
                <div><div class="label">Faculty</div><div class="value">${sup.faculty}</div></div>
            </div>`;
        }

        if (sup.staffNumber) {
            html += `
            <div class="contact-detail">
                <i class="fas fa-id-badge"></i>
                <div><div class="label">Staff Number</div><div class="value">${sup.staffNumber}</div></div>
            </div>`;
        }

        html += `
            <div class="contact-detail">
                <i class="fas fa-calendar-alt"></i>
                <div><div class="label">Assigned Since</div><div class="value">${assignedFormatted}</div></div>
            </div>
            <div class="contact-actions">
                <a href="mailto:?subject=Attachment%20Inquiry%20-%20${encodeURIComponent(sup.name)}&body=Dear%20${encodeURIComponent(sup.name)},%0A%0A" class="btn-email-sup">
                    <i class="fas fa-envelope"></i> Send Email
                </a>
            </div>`;

        document.getElementById('contactModalBody').innerHTML = html;
        document.getElementById('contactModal').style.display = 'flex';
    }

    function closeContactModal() {
        document.getElementById('contactModal').style.display = 'none';
    }

    // Close modal when clicking overlay
    document.getElementById('contactModal').addEventListener('click', function(e) {
        if (e.target === this) closeContactModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeContactModal();
    });
</script>

