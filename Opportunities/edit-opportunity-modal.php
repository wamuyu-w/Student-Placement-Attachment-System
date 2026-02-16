<!-- Edit Opportunity Form Modal -->
<div id="editOpportunityFormContainer" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; overflow-y: auto;">
    <div style="background: white; width: 600px; max-width: 90%; margin: 50px auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h3 style="margin: 0; color: #8B1538;"><i class="fas fa-edit"></i> Edit Opportunity</h3>
            <button type="button" onclick="closeEditForm()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editOpportunityForm" method="POST" action="process-edit-opportunity.php">
            <input type="hidden" id="editOpportunityId" name="opportunity_id">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Host Organization</label>
                <select id="editHostOrgSelect" name="host_org_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="">Select an Organization</option>
                    <?php 
                    // Reuse the $hostOrganizations variable from the parent scope
                    if (isset($hostOrganizations)) {
                        $hostOrganizations->data_seek(0);
                        while ($org = $hostOrganizations->fetch_assoc()) {
                            echo '<option value="' . $org['HostOrgID'] . '">' . htmlspecialchars($org['OrganizationName']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Description</label>
                <textarea id="editDescription" name="description" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Eligibility Criteria</label>
                <textarea id="editEligibilityCriteria" name="eligibility_criteria" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Start Date</label>
                    <input type="date" id="editStartDate" name="application_start_date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">End Date</label>
                    <input type="date" id="editEndDate" name="application_end_date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Status</label>
                <select id="editStatus" name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Closed">Closed</option>
                </select>
            </div>

            <div style="text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="button" onclick="closeEditForm()" style="padding: 10px 20px; margin-right: 10px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button type="submit" style="background-color: #8B1538; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeEditForm() {
        document.getElementById('editOpportunityFormContainer').style.display = 'none';
    }
</script>
