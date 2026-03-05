<?php use App\Core\Helpers; ?>

<div class="split-layout">
    <!-- Left Column: Application Actions -->
    <div class="layout-col">
        <!-- Apply Section -->
        <div class="application-card mb-4">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Apply for Attachment Session</h2>
                    <p class="section-desc">Request clearance from the university to proceed with your industrial attachment.</p>
                </div>
            </div>
            
            <?php if ($hasPendingOrApproved): ?>
                <button class="btn-submit btn-disabled" disabled title="You already have an active application" style="width: 100%; justify-content: center;">
                    <i class="fas fa-check-circle"></i> Application Submitted
                </button>
            <?php else: ?>
                <form action="<?= Helpers::baseUrl('/student/applications/apply-session') ?>" method="POST">
                    <div class="form-group mb-3">
                        <label class="form-label">Intended Host Organization (Optional)</label>
                        <input type="text" name="intended_host" class="form-control" placeholder="E.g. Safaricom PLC, KRA, etc.">
                        <small style="color: var(--text-secondary); font-size: 0.8rem;">If the organization is new, a default account will be created for them.</small>
                    </div>
                    
                    <!-- Contact Details for New Org -->
                    <div class="form-group mb-3">
                        <label class="form-label">Contact Person Name</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="E.g. John Doe (HR Manager)">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Contact Person Email</label>
                        <input type="email" name="contact_email" class="form-control" placeholder="E.g. john.doe@company.com">
                        <small style="color: var(--text-secondary); font-size: 0.8rem;">This email will be used to send login credentials for first time access</small>
                    </div>
                    <button type="submit" class="btn-submit" style="width: 100%; justify-content: center; margin-top: 16px;">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Registration Section -->
        <?php if ($hasApproved && !$hasActiveAttachment): ?>
        <div class="application-card mt-4">
            <div class="section-header">
                <div>
                    <h3 class="section-title"><i class="fas fa-briefcase"></i> Register Your Placement</h3>
                    <p class="section-desc">Your application is approved! Please register the organization where you have secured your attachment.</p>
                </div>
            </div>
            
            <form action="<?= Helpers::baseUrl('/student/applications/register-placement') ?>" method="POST" class="register-form">
                <div class="form-group full-width">
                    <label class="form-label">Host Organization</label>
                    <select name="host_org_id" required class="form-control">
                        <option value="">-- Select Organization --</option>
                        <?php if($hosts) while($host = $hosts->fetch_assoc()): ?>
                            <option value="<?= $host['HostOrgID']; ?>">
                                <?= htmlspecialchars($host['OrganizationName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">Can't find your org? Contact Admin.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" required class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" required class="form-control">
                </div>
                <div class="form-group full-width" style="margin-top: 8px;">
                    <button type="submit" class="btn-submit" style="background-color: #10b981;">
                        <i class="fas fa-save"></i> Register Placement
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: History -->
    <div class="layout-col">
        <div class="application-card">
            <div class="section-header">
                <h3 class="section-title">Application History</h3>
            </div>
            
            <?php if ($applications && $applications->num_rows > 0): ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($app = $applications->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($app['ApplicationDate'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($app['ApplicationStatus']); ?>">
                                        <?= htmlspecialchars($app['ApplicationStatus']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 32px 0;">
                    <i class="fas fa-file-signature" style="font-size: 32px; color: var(--text-secondary); margin-bottom: 12px;"></i>
                    <p style="color: var(--text-secondary);">No applications found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .split-layout { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; }
    @media (max-width: 1024px) { .split-layout { grid-template-columns: 1fr; } }
    .application-card { background: var(--bg-white); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); }
    .section-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); }
    .section-title { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .section-desc { color: var(--text-secondary); font-size: 0.9rem; }
    .register-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 0.875rem; font-weight: 500; color: var(--text-primary); }
    .form-control { padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; width: 100%; transition: border-color 0.2s; }
    .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1); }
    .full-width { grid-column: 1 / -1; }
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table th { text-align: left; padding: 12px; background: var(--bg-light); font-weight: 600; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    .history-table td { padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .btn-submit { background-color: var(--primary-color); color: white; border: none; padding: 12px 20px; border-radius: var(--radius-md); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: background-color 0.2s; }
    .btn-submit:hover { background-color: var(--primary-dark); }
    .btn-disabled { background-color: #e5e7eb; color: #9ca3af; cursor: not-allowed; }
    .status-badge { padding: 4px 8px; border-radius: var(--radius-sm); font-size: 0.85em; font-weight: 600; white-space: nowrap; }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved, .status-active { background-color: #d1fae5; color: #065f46; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
</style>
