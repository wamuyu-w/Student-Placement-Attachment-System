<?php use App\Core\Helpers; ?>

<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/opportunities.css') ?>">


    <div class="opportunities-section">
    <div class="section-header">
        <div>
            <h2><i class="fas fa-briefcase"></i> Active Opportunities</h2>
            <p class="section-subtitle">Browse and apply for attachment opportunities</p>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search opportunities...">
        </div>
    </div>

    <?php if ($opportunities && $opportunities->num_rows > 0): ?>
        <div class="opportunities-grid">
            <?php while($opp = $opportunities->fetch_assoc()): ?>
                <div class="opportunity-card">
                    <div class="opportunity-header">
                        <div class="org-badge">
                            <i class="fas fa-building"></i>
                            <?php echo htmlspecialchars(substr($opp['OrganizationName'], 0, 3)); ?>
                        </div>
                        <span class="deadline-badge">
                            Closes: <?php echo date('M d', strtotime($opp['ApplicationEndDate'])); ?>
                        </span>
                    </div>
                    <div class="opportunity-content">
                        <h3><?php echo htmlspecialchars(substr($opp['Description'], 0, 80)); ?></h3>
                        <p class="organization"><?php echo htmlspecialchars($opp['OrganizationName']); ?></p>
                        <p class="description"><?php echo htmlspecialchars(substr($opp['Description'], 0, 120)); ?>...</p>
                        
                        <div class="opportunity-meta">
                            <div class="meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Eligibility Criteria</span>
                            </div>
                            <p class="meta-text"><?php echo htmlspecialchars(substr($opp['EligibilityCriteria'], 0, 100)); ?>...</p>
                        </div>
                    </div>
                    <div class="opportunity-footer">
                        <button class="btn btn-view" 
                                data-org="<?php echo htmlspecialchars($opp['OrganizationName']); ?>"
                                data-desc="<?php echo htmlspecialchars($opp['Description']); ?>"
                                data-crit="<?php echo htmlspecialchars($opp['EligibilityCriteria']); ?>"
                                data-deadline="<?php echo date('M d, Y', strtotime($opp['ApplicationEndDate'])); ?>"
                                data-id="<?php echo $opp['OpportunityID']; ?>"
                                onclick="handleViewDetails(this)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn btn-apply-card" 
                                data-id="<?php echo $opp['OpportunityID']; ?>"
                                data-org="<?php echo htmlspecialchars($opp['OrganizationName']); ?>"
                                data-desc="<?php echo htmlspecialchars($opp['Description']); ?>"
                                onclick="handleApplyForm(this)">
                            <i class="fas fa-arrow-right"></i> Apply Now
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No Active Opportunities</h3>
            <p>There are currently no active opportunities available. Please check back later.</p>
        </div>
    <?php endif; ?>
</div>


<!-- Details Modal -->
<div id="detailsModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2><i class="fas fa-info-circle"></i> Opportunity Details</h2>
            <button type="button" class="modal-close" onclick="closeDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-content">
            <div class="detail-header">
                <h3 id="detailsRole" class="detail-title"></h3>
                <div class="detail-org">
                    <i class="fas fa-building"></i>
                    <span id="detailsOrg"></span>
                </div>
            </div>
            
            <div class="detail-section">
                <div class="detail-label"><i class="fas fa-align-left"></i> Description</div>
                <p id="detailsDesc" class="detail-text"></p>
            </div>
            
            <div class="detail-section">
                <div class="detail-label"><i class="fas fa-graduation-cap"></i> Eligibility Criteria</div>
                <p id="detailsCriteria" class="detail-text"></p>
            </div>

            <div class="detail-section">
                <div class="detail-label"><i class="fas fa-clock"></i> Application Deadline</div>
                <p id="detailsDeadline" class="detail-text" style="font-weight: 600; color: #b91c1c;"></p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button class="btn btn-view" onclick="closeDetailsModal()">Close</button>
                <button class="btn btn-apply-card" id="detailsApplyBtn">Apply Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Application Modal -->
<div id="applicationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-file-contract"></i> Apply for Attachment</h2>
            <button type="button" class="modal-close" onclick="closeApplicationForm()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="application-summary-header">
            <div class="app-org-name" id="modalOrg"></div>
            <div class="app-role-title" id="modalRole"></div>
        </div>

        <form id="applicationForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="opportunityId" name="opportunity_id" value="">
            
            <!-- Personal & Academic Details (Read-only Summary) -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Applicant Details</h3>
                </div>
                
                <div class="student-summary-card">
                    <div class="summary-row">
                        <div class="summary-item">
                            <span class="label">Full Name</span>
                            <span class="value"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Admission Number</span>
                            <span class="value"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-item">
                            <span class="label">Email</span>
                            <span class="value"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Phone</span>
                            <span class="value"><?php echo htmlspecialchars($_SESSION['phone']); ?></span>
                        </div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-item">
                            <span class="label">Program</span>
                            <span class="value"><?php echo htmlspecialchars($_SESSION['course']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Year of Study</span>
                            <span class="value"><?php echo htmlspecialchars($_SESSION['year_of_study']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivation Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-pen-fancy"></i>
                    <h3>Application Statement</h3>
                </div>
                <div class="form-group full-width">
                    <label for="motivation" class="form-label">Why are you interested in this opportunity? <span style="color: #6b7280; font-weight: 400; font-size: 0.85em;">(Max 500 words)</span></label>
                    <textarea id="motivation" name="motivation" class="form-control" placeholder="Explain your interest and how this attachment aligns with your career goals..." rows="6" maxlength="2500" required></textarea>
                    <span class="char-count"><span id="charCount">0</span> characters</span>
                </div>
            </div>

            <!-- Supporting Documents Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-paperclip"></i>
                    <h3>Supporting Documents</h3>
                </div>
                <div class="form-group full-width">
                    <label for="resume" class="form-label">Upload Resume/CV</label>
                    <div class="file-upload">
                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx">
                        <div class="upload-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>
                                <p style="margin-bottom: 4px; font-weight: 500;">Click to upload or drag & drop</p>
                                <span class="upload-hint">PDF or DOCX (Max 5MB)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group full-width" style="margin-top: 16px;">
                    <label for="resume_link" class="form-label">Or provide a Link (Optional)</label>
                    <div class="input-with-icon">
                        <i class="fas fa-link"></i>
                        <input type="url" id="resume_link" name="resume_link" class="form-control" placeholder="https://drive.google.com/..." style="padding-left: 36px;">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeApplicationForm()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit Application</button>
            </div>

            <!-- Alert Messages -->
            <div id="formAlert" class="alert" style="display: none;">
                <i class="fas fa-info-circle"></i>
                <span id="alertMessage"></span>
            </div>
        </form>
    </div>
</div>

<script src="<?= Helpers::baseUrl('../assets/js/opportunities.js') ?>"></script>
