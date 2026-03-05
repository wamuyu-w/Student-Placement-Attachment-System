<?php use App\Core\Helpers; // Staff Dashboard Content ?>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="card-content">
            <h3>Monitored Attachments</h3>
            <p class="card-number"><?= $stats['monitored_attachments'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Pending Reviews</h3>
            <p class="card-number"><?= $stats['pending_reviews'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Students Monitored</h3>
            <p class="card-number"><?= $stats['students_monitored'] ?? 0; ?></p>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Total Logbooks</h3>
            <p class="card-number"><?= $stats['total_logbooks'] ?? 0; ?></p>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Activity Section -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Recent Logbook Submissions</h2>
            <a href="#" class="view-all-link">View All →</a>
        </div>
        <div class="activity-list">
            <?php while ($log = $recentLogs->fetch_assoc()): ?>
            <div class="activity-item">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($log['FirstName'] . ' ' . $log['LastName']); ?>&background=8B1538&color=fff&size=128" alt="Avatar" class="activity-avatar">
                <div class="activity-content">
                    <div class="activity-title"><?= htmlspecialchars($log['FirstName'] . ' ' . $log['LastName']); ?></div>
                    <div class="activity-description"><?= htmlspecialchars($log['Course']); ?></div>
                    <div class="activity-time"><?= date('M j, Y', strtotime($log['IssueDate'])); ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="quick-actions-section">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <button class="action-btn primary" onclick="handleReviewLogbook()">
                <i class="fas fa-file-check"></i>
                <span>Review Logbooks</span>
            </button>
            <button class="action-btn" onclick="handleViewStudents()">
                <i class="fas fa-graduation-cap"></i>
                <span>View Students</span>
            </button>
            <button class="action-btn" onclick="handleGenerateReport()">
                <i class="fas fa-chart-bar"></i>
                <span>Generate Report</span>
            </button>
        </div>
    </div>
</div>

<script src="<?= Helpers::baseUrl('../assets/js/staff-dashboard.js') ?>"></script>
