<?php use App\Core\Helpers; ?>


<div class="opportunities-list-section">
    <div class="section-header">
        <div class="header-text-group">
            <p class="section-subtitle">Review, edit, or add new attachment opportunities for students</p>
        </div>
        <button onclick="openEditModal()" class="btn-primary">
            <i class="fas fa-plus"></i> Add New Opportunity
        </button>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin: 0 0 20px 0;">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin: 0 0 20px 0;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if ($opportunities && $opportunities->num_rows > 0): ?>
        <div class="opportunities-grid">
            <?php while ($opp = $opportunities->fetch_assoc()): ?>
                <div class="opportunity-card manage-card">
                    <div class="opportunity-header">
                        <div class="org-badge">
                            <i class="fas fa-building"></i>
                            <?= htmlspecialchars($opp['OrganizationName']) ?>
                        </div>
                        <span class="status-badge status-<?= strtolower($opp['Status']) ?>">
                            <?= htmlspecialchars($opp['Status']) ?>
                        </span>
                    </div>
                    <div class="opportunity-content">
                        <h3><?= htmlspecialchars(substr($opp['Description'], 0, 70)) ?><?= strlen($opp['Description']) > 70 ? '...' : '' ?></h3>
                        
                        <div class="opportunity-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Deadline: <?= date('M d, Y', strtotime($opp['ApplicationEndDate'])) ?></span>
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
                        <form action="<?= Helpers::baseUrl('/opportunities/delete') ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this opportunity?');" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <input type="hidden" name="id" value="<?= $opp['OpportunityID'] ?>">
                            <button type="submit" class="btn btn-delete" title="Delete Opportunity">
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
            <p>No opportunities found.</p>
            <p class="text-muted">Start by adding a new opportunity for students.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 id="modalTitle">Add Opportunity</h2>
            <button type="button" class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="<?= Helpers::baseUrl('/opportunities/save') ?>" method="POST" style="padding: 24px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="opportunity_id" id="oppId">
            
            <div class="form-group">
                <label><i class="fas fa-building"></i> Host Organization</label>
                <select name="host_org_id" id="hostSelect" class="form-control">
                    <option value="">-- Select Existing Organization --</option>
                    <?php if(isset($hostOrganizations)) while($h = $hostOrganizations->fetch_assoc()): ?>
                        <option value="<?= $h['HostOrgID'] ?>"><?= htmlspecialchars($h['OrganizationName']) ?></option>
                    <?php endwhile; ?>
                </select>
                <div style="margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">OR Create New Organization:</span>
                    <input type="text" name="organization_name" placeholder="Enter New Organization Name" class="form-control" style="margin-top: 8px;">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description / Role Title</label>
                <textarea name="description" id="desc" class="form-control" rows="3" placeholder="e.g. Software Engineering Intern" required></textarea>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-graduation-cap"></i> Eligibility Criteria</label>
                <textarea name="eligibility_criteria" id="crit" class="form-control" rows="2" placeholder="e.g. 3rd year Computer Science student..." required></textarea>
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

            <div class="form-group">
                <label><i class="fas fa-toggle-on"></i> Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Closed">Closed</option>
                </select>
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
        document.getElementById('hostSelect').value = data.HostOrgID;
        document.getElementById('desc').value = data.Description;
        document.getElementById('crit').value = data.EligibilityCriteria;
        document.getElementById('start').value = data.ApplicationStartDate;
        document.getElementById('end').value = data.ApplicationEndDate;
        document.getElementById('status').value = data.Status;
    } else {
        title.innerHTML = '<i class="fas fa-plus"></i> Add Opportunity';
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

