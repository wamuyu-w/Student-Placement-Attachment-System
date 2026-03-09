<?php use App\Core\Helpers; // Student Dashboard Content ?>

<?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'Inactive'): ?>
<div class="alert alert-warning" style="background-color: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #ffeeba;">
    <strong>Read-Only Mode:</strong> Your attachment period has concluded. You are currently in read-only mode to view your historical records.
</div>
<?php endif; ?>
<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="card-content">
            <h3>My Applications</h3>
            <p class="card-number"><?php echo $stats['myApplications']; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Active Placement</h3>
            <p class="card-number"><?php echo $stats['activePlacement']; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Available Opportunities</h3>
            <p class="card-number"><?php echo $stats['availableOpportunities']; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Pending Tasks</h3>
            <p class="card-number"><?php echo $stats['pendingTasks']; ?></p>
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
                <div class="activity-item">
                    <img src="https://ui-avatars.com/api/?name=Welcome&background=8B1538&color=fff&size=128" alt="Welcome" class="activity-avatar">
                    <div class="activity-content">
                        <div class="activity-title">Welcome to your dashboard</div>
                        <div class="activity-description">Start by browsing available opportunities</div>
                        <div class="activity-time">Just now</div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <img src="<?php echo htmlspecialchars($activity['avatar']); ?>" alt="Activity" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-title"><?php echo $activity['title']; ?></div>
                            <div class="activity-description"><?php echo $activity['description']; ?></div>
                            <div class="activity-time"><?php echo Helpers::timeAgo($activity['time']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Inactive'): ?>
    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <button class="action-btn primary" onclick="handleBrowseOpportunities()">
                <i class="fas fa-search"></i>
                <span>Browse Opportunities</span>
            </button>
            <button class="action-btn" onclick="handleViewApplications()">
                <i class="fas fa-file-alt"></i>
                <span>View My Applications</span>
            </button>
            <button class="action-btn" onclick="handleViewLogbook()">
                <i class="fas fa-book"></i>
                <span>View Logbook</span>
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="<?= Helpers::baseUrl('../assets/js/student-dashboard.js') ?>"></script>
<script src="<?= Helpers::baseUrl('../assets/js/dashboard-updates.js') ?>"></script>
