<?php use App\Core\Helpers; ?>

<style>
    .active-placement-notice {
        background: white;
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
        grid-column: 1 / -1;
    }
    .notice-icon {
        width: 80px;
        height: 80px;
        background: #ecfdf5;
        color: #10b981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 24px;
    }
    .notice-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
    }
    .notice-text {
        color: #64748b;
        font-size: 1.1rem;
        margin-bottom: 32px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    .notice-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
    }
    .btn-notice {
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-logbook {
        background: #8B1538;
        color: white;
    }
    .btn-logbook:hover {
        background: #71112e;
        transform: translateY(-2px);
    }
    .btn-supervision {
        background: #f8fafc;
        color: #1e293b;
        border: 1px solid #e2e8f0;
    }
    .btn-supervision:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
</style>

<div class="split-layout">
    <?php if (isset($hasActivePlacement) && $hasActivePlacement): ?>
        <div class="active-placement-notice">
            <div class="notice-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="notice-title">Placement Already Active</h2>
            <p class="notice-text">You are currently in an ongoing attachment session. New applications are disabled until this session is completed.</p>
            <div class="notice-actions">
                <a href="<?= Helpers::baseUrl('/student/logbook') ?>" class="btn-notice btn-logbook">
                    <i class="fas fa-book"></i> Access Logbook
                </a>
                <a href="<?= Helpers::baseUrl('/student/supervisor') ?>" class="btn-notice btn-supervision">
                    <i class="fas fa-user-tie"></i> Supervision Portal
                </a>
            </div>
        </div>
    <?php endif; ?>
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
            
            <?php if ($hasActivePlacement): ?>
                <div class="alert alert-info" style="margin-top: 10px; border-radius: 8px;">
                    <i class="fas fa-info-circle"></i> You have an ongoing placement.
                </div>
            <?php elseif ($hasPendingOrApproved): ?>
                <button class="btn-submit btn-disabled" disabled title="You already have an active application" style="width: 100%; justify-content: center;">
                    <i class="fas fa-check-circle"></i> Application Submitted
                </button>
            <?php else: ?>
                <form action="<?= Helpers::baseUrl('/student/applications/apply-session') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <!-- Host Organization -->
                    <div class="form-group mb-3">
                        <label class="form-label">Intended Host Organization <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
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
                        <small style="color: var(--text-secondary); font-size: 0.8rem;">Login credentials will be sent to this email if it's a new organization.</small>
                    </div>

                    <!-- Attachment Date Range -->
                    <div style="border-radius:10px;padding:16px;margin-bottom:12px;">
                        <label class="form-label" style="font-weight:700;color:#1e293b;margin-bottom:10px;">
                           
                            Expected Attachment Period <span style="color:#dc2626;">*</span>
                        </label>
                        <p style="font-size:0.82rem;color:#64748b;margin-bottom:12px;">Minimum: <strong>1.5 months</strong> (45 days) &mdash; Maximum: <strong>3 months</strong> (90 days). End date auto-fills when you pick a start date.</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <label class="form-label" style="font-size:0.85rem;">Start Date</label>
                                <input type="date" name="start_date" id="app_start_date" required class="form-control"
                                       min="<?= date('Y-m-d') ?>"
                                       onchange="autoSetEndDate(this.value)">
                            </div>
                            <div>
                                <label class="form-label" style="font-size:0.85rem;">End Date</label>
                                <input type="date" name="end_date" id="app_end_date" required class="form-control"
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Financial Clearance Declaration -->
                    <div class="form-group mb-3" style="border-radius:10px;padding:16px;">
                        <label class="form-label" style="font-weight:700;color:#1e293b;margin-bottom:10px;">
                            <i class="fas fa-money-check-alt" style="color:#8B1538;margin-right:6px;"></i>
                            Financial Clearance Declaration <span style="color:#dc2626;">*</span>
                        </label>
                        <p style="font-size:0.82rem;color:#64748b;margin-bottom:12px;">Confirm your current fee payment status. The admin will verify this before approving your attachment.</p>

                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;border:1px solid #e2e8f0;cursor:pointer;background:white;">
                                <input type="radio" name="financial_clearance_status" value="Cleared" >
                                <span>
                                    <strong>I have cleared all outstanding fees</strong><br>
                                </span>
                            </label>
                            <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;border:1px solid #e2e8f0;cursor:pointer;background:white;transition:border-color 0.2s;">
                                <input type="radio" name="financial_clearance_status" value="Pending">
                                <span>
                                    <strong>Clearance is in progress</strong><br>
                                </span>
                            </label>
                            <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;border:1px solid #e2e8f0;cursor:pointer;background:white;transition:border-color 0.2s;">
                                <input type="radio" name="financial_clearance_status" value="Not Cleared" >
                                <span>
                                    <strong>I have an outstanding fee balance</strong><br>
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="width: 100%; justify-content: center; margin-top: 8px;">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php /* Register Placement section removed — admin approval now auto-creates the attachment record in a single step */ ?>
    </div>

    <!-- Right Column: History -->
    <div class="layout-col">
        <div class="application-card">
            <div class="section-header">
                <h3 class="section-title">Application History</h3>
            </div>
            
            <?php if ($applications && $applications->num_rows > 0): ?>
                <div class="table-responsive">
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
                                    <?php if ($app['ApplicationStatus'] === 'Rejected' && !empty($app['RejectionReason'])): ?>
                                        <div style="margin-top:6px;padding:8px 10px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;font-size:0.82rem;color:#991b1b;">
                                            <i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>
                                            <strong>Reason:</strong> <?= htmlspecialchars($app['RejectionReason']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($app['FinancialClearanceStatus']) && $app['FinancialClearanceStatus'] !== 'Pending'): ?>
                                        <div style="margin-top:4px;font-size:0.8rem;color:#64748b;">
                                            Finance: <?= htmlspecialchars($app['FinancialClearanceStatus']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
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

<script>
function autoSetEndDate(startVal) {
    if (!startVal) return;
    const toStr = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    const minEnd = new Date(startVal);
    const maxEnd = new Date(startVal);
    minEnd.setDate(minEnd.getDate() + 45); // 1.5 months minimum
    maxEnd.setDate(maxEnd.getDate() + 90); // 3 months maximum
    const endInput = document.getElementById('app_end_date');
    endInput.min   = toStr(minEnd);
    endInput.max   = toStr(maxEnd);
    endInput.value = toStr(minEnd); // default to minimum
}
</script>
