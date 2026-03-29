<?php use App\Core\Helpers; ?>


<div class="opportunities-list-section">
    <div class="section-header">
        <div class="header-text-group">
            <p class="section-subtitle">Manage and track your organization's attachment postings</p>
        </div>
        <button onclick="openEditModal()" class="btn-primary">
            <i class="fas fa-plus"></i> Post New Opportunity
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin: 0 0 20px 0;">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if ($opportunities && $opportunities->num_rows > 0): ?>
        <div class="opportunities-grid">
            <?php while ($opp = $opportunities->fetch_assoc()): ?>
                <div class="opportunity-card manage-card">
                    <div class="opportunity-header">
                        <div class="org-badge">
                            <i class="fas fa-building"></i>
                            Your Organization
                        </div>
                        <span class="status-badge status-<?= strtolower($opp['Status']) ?>"><?= htmlspecialchars($opp['Status']) ?></span>
                    </div>
                    <div class="opportunity-content">
                        <h3><?= htmlspecialchars(substr($opp['Description'], 0, 70)) ?><?= strlen($opp['Description']) > 70 ? '...' : '' ?></h3>
                        
                        <div class="opportunity-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Closing Date: <?= date('M d, Y', strtotime($opp['ApplicationEndDate'])) ?></span>
                            </div>
                            <div class="meta-item" style="margin-top: 5px;">
                                <i class="fas fa-info-circle"></i>
                                <span class="meta-text" style="margin-left: 0;"><?= htmlspecialchars(substr($opp['EligibilityCriteria'], 0, 80)) ?>...</span>
                            </div>
                        </div>
                    </div>
                    <div class="opportunity-footer">
                        <button class="btn btn-edit" onclick='openEditModal(<?= json_encode($opp) ?>)'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="<?= Helpers::baseUrl('/opportunities/delete') ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this posting?');" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <input type="hidden" name="id" value="<?= $opp['OpportunityID'] ?>">
                            <button type="submit" class="btn btn-delete" title="Delete Posting">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-briefcase"></i>
            <p>You haven't posted any opportunities yet.</p>
            <p class="text-muted">Click "Post New Opportunity" to start attracting applicants.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 id="modalTitle">Post Opportunity</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="<?= Helpers::baseUrl('/opportunities/save') ?>" method="POST" style="padding: 24px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="opportunity_id" id="oppId">
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description / Job Title</label>
                <textarea name="description" id="desc" class="form-control" rows="3" placeholder="e.g. IT Support Intern" required></textarea>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-graduation-cap"></i> Eligibility Criteria</label>
                <textarea name="eligibility_criteria" id="crit" class="form-control" rows="2" placeholder="e.g. Diploma in IT or related field..." required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Start Date</label>
                    <input type="date" name="application_start_date" id="start" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> End Date (Deadline)</label>
                    <input type="date" name="application_end_date" id="end" class="form-control" required>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 10px;">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Opportunity</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(data = null) {
    const modal = document.getElementById('editModal');
    const title = document.getElementById('modalTitle');
    const form = modal.querySelector('form');
    
    if (data) {
        title.innerHTML = '<i class="fas fa-edit"></i> Edit Opportunity';
        document.getElementById('oppId').value = data.OpportunityID;
        document.getElementById('desc').value = data.Description;
        document.getElementById('crit').value = data.EligibilityCriteria;
        document.getElementById('start').value = data.ApplicationStartDate;
        document.getElementById('end').value = data.ApplicationEndDate;
    } else {
        title.innerHTML = '<i class="fas fa-plus"></i> Post New Opportunity';
        document.getElementById('oppId').value = '';
        form.reset();
    }
    
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeEditModal();
    }
}
</script>

