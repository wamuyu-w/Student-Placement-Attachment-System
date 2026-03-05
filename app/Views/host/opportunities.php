<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div class="opportunities-list-section">
        <div class="section-header">
            <h2><i class="fas fa-briefcase"></i> My Posted Opportunities</h2>
            <button onclick="openEditModal()" class="btn-primary" style="background-color: #8B1538; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                <i class="fas fa-plus"></i> Post New
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <?php if ($opportunities && $opportunities->num_rows > 0): ?>
            <div class="opportunities-grid">
                <?php while ($opp = $opportunities->fetch_assoc()): ?>
                    <div class="opportunity-card">
                        <div class="opportunity-header">
                            <h3><?= htmlspecialchars(substr($opp['Description'], 0, 60)) ?>...</h3>
                            <span class="status-badge status-<?= strtolower($opp['Status']) ?>"><?= htmlspecialchars($opp['Status']) ?></span>
                        </div>
                        <div class="opportunity-content">
                            <p><strong>Closes:</strong> <?= date('M d, Y', strtotime($opp['ApplicationEndDate'])) ?></p>
                            <p><?= htmlspecialchars(substr($opp['EligibilityCriteria'], 0, 50)) ?>...</p>
                        </div>
                        <div class="opportunity-footer">
                            <button class="btn btn-view" onclick='openEditModal(<?= json_encode($opp) ?>)'>Edit</button>
                            <form action="<?= Helpers::baseUrl('/opportunities/delete') ?>" method="POST" onsubmit="return confirm('Delete this?');" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $opp['OpportunityID'] ?>">
                                <button type="submit" class="btn btn-apply-card" style="background: #ef4444; border-color: #ef4444;">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><p>You haven't posted any opportunities yet.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Post Opportunity</h2>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form action="<?= Helpers::baseUrl('/opportunities/save') ?>" method="POST" style="padding: 20px;">
            <input type="hidden" name="opportunity_id" id="oppId">
            
            <div class="form-group">
                <label>Description / Job Title</label>
                <textarea name="description" id="desc" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Eligibility Criteria</label>
                <textarea name="eligibility_criteria" id="crit" class="form-control" rows="2" required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="application_start_date" id="start" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="application_end_date" id="end" class="form-control" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(data = null) {
    const modal = document.getElementById('editModal');
    const title = document.getElementById('modalTitle');
    
    if (data) {
        title.textContent = 'Edit Opportunity';
        document.getElementById('oppId').value = data.OpportunityID;
        document.getElementById('desc').value = data.Description;
        document.getElementById('crit').value = data.EligibilityCriteria;
        document.getElementById('start').value = data.ApplicationStartDate;
        document.getElementById('end').value = data.ApplicationEndDate;
    } else {
        title.textContent = 'Post Opportunity';
        document.getElementById('oppId').value = '';
        document.querySelector('form').reset();
    }
    
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
<style> .opportunities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; } </style>
