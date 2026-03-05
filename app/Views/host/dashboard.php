<?php use App\Core\Helpers; // Host Dashboard Content ?>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="card-content">
            <h3>Active Placements</h3>
            <p class="card-number"><?= $stats['active_placements'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Students on Attachment</h3>
            <p class="card-number"><?= $stats['students_attached'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Pending Logbook Entries</h3>
            <p class="card-number"><?= $stats['pending_logbooks'] ?? 0; ?></p>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Activity Section -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Recent Placements</h2>
            <a href="#" class="view-all-link">View All →</a>
        </div>
        <div class="activity-list">
            <?php while ($app = $recentApps->fetch_assoc()): ?>
            <div class="activity-item">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($app['FirstName'] . ' ' . $app['LastName']); ?>&background=8B1538&color=fff&size=128" alt="Avatar" class="activity-avatar">
                <div class="activity-content">
                    <div class="activity-title"><?= htmlspecialchars($app['FirstName'] . ' ' . $app['LastName']); ?></div>
                    <div class="activity-description"><?= htmlspecialchars($app['Course']); ?> - <?= htmlspecialchars($app['position_applied']); ?></div>
                    <div class="activity-time"><?= date('M j, Y', strtotime($app['StartDate'])); ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="quick-actions-section">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <button class="action-btn primary" onclick="handlePostPlacement()">
                <i class="fas fa-plus"></i>
                <span>Post New Placement</span>
            </button>
            <button class="action-btn" onclick="handleViewApplications()">
                <i class="fas fa-file-alt"></i>
                <span>View All Applications</span>
            </button>
            <button class="action-btn" onclick="handleManageStudents()">
                <i class="fas fa-graduation-cap"></i>
                <span>Manage Students</span>
            </button>
        </div>
    </div>
</div>

<script src="<?= Helpers::baseUrl('../assets/js/host-org-dashboard.js') ?>"></script>
