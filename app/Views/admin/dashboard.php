<?php use App\Core\Helpers; // Admin Dashboard Content ?>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="card-content">
            <h3>Pending Applications</h3>
            <p class="card-number"><?= $stats['pendingApps'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Active Placements</h3>
            <p class="card-number"><?= $stats['activePlacements'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Available Opportunities</h3>
            <p class="card-number"><?= $stats['opportunities'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Unassigned Students</h3>
            <p class="card-number"><?= $stats['unassignedStudents'] ?? 0; ?></p>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Recent Activity -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Recent Activity</h2>
            <a href="#" class="view-all-link">View All</a>
        </div>
        <div class="activity-list" id="activityList">
            <?php if (empty($activities)): ?>
                <div style="padding: 20px; text-align: center; color: #6b7280;">
                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; color: #d1d5db;"></i>
                    <p>No recent activity found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <img src="<?= htmlspecialchars($activity['avatar']); ?>" alt="Activity" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-title"><?= $activity['title']; ?></div>
                            <div class="activity-description"><?= $activity['description']; ?></div>
                            <div class="activity-time"><?= Helpers::timeAgo($activity['time']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <button class="action-btn primary" onclick="handleAddOpportunity()">
                <i class="fas fa-plus"></i>
                <span>Add New Opportunity</span>
            </button>
            <button class="action-btn" onclick="handleGenerateReport()">
                <i class="fas fa-file-alt"></i>
                <span>Generate Weekly Report</span>
            </button>
            <button class="action-btn" onclick="handleAssignSupervisor()">
                <i class="fas fa-users"></i>
                <span>Assign Supervisor</span>
            </button>
        </div>
    </div>
</div>

<script src="<?= Helpers::baseUrl('../assets/js/admin-dashboard.js') ?>"></script>
