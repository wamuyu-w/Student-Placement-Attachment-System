<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="report-header">
    <div class="report-title">
        <h1>System Effectiveness Dashboard</h1>
    </div>
    <a href="<?= Helpers::baseUrl('/reports/print/effectiveness') ?>" target="_blank" class="btn report-tag-dark">
        <i class="fas fa-download"></i> Download Report (PDF)
    </a>
</div>

<div class="summary-cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="summary-card">
        <div class="card-content">
            <h3>Placement Rate</h3>
            <p class="card-number"><?= $stats['totalStudents'] > 0 ? round(($stats['placedStudents'] / $stats['totalStudents']) * 100, 1) : 0 ?>%</p>
            <small class="text-muted"><?= $stats['placedStudents'] ?> of <?= $stats['totalStudents'] ?> Students</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Total Opportunities</h3>
            <p class="card-number"><?= $stats['totalOpportunities'] ?></p>
            <small class="text-muted">Active & Past Listings</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Completed Attachments</h3>
            <p class="card-number"><?= $stats['completedAttachments'] ?></p>
            <small class="text-muted">Verified Completions</small>
        </div>
    </div>
</div>

<div class="content-grid" style="margin-top: 24px;">
    <!-- Lecturer Productivity -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Lecturer Assessment Workload</h2>
        </div>
        <div class="table-container" style="box-shadow: none; border: none;">
            <table>
                <thead>
                    <tr>
                        <th>Lecturer</th>
                        <th style="text-align: center;">Students Assessed</th>
                        <th style="text-align: center;">Avg Score Given</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lecturerStats && $lecturerStats->num_rows > 0): ?>
                        <?php while($row = $lecturerStats->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 600;"><?= htmlspecialchars($row['Name']) ?></td>
                                <td style="text-align: center;">
                                    <span class="report-tag report-tag-dark"><?= $row['students_assessed'] ?> Students</span>
                                </td>
                                <td style="text-align: center; font-weight: bold;">
                                    <?= number_format($row['avg_marks_given'], 1) ?>%
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align: center;">No assessment data found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Placement Impact -->
    <div class="activity-section">
        <div class="section-header">
            <h2>Recent Placement Impact</h2>
        </div>
        <div style="padding: 20px;">
            <?php if ($stats['placementsByMonth'] && $stats['placementsByMonth']->num_rows > 0): ?>
                <?php while($row = $stats['placementsByMonth']->fetch_assoc()): ?>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                            <span class="text-bold"><?= date('F Y', strtotime($row['Month'] . '-01')) ?></span>
                            <span class="text-black text-bold"><?= $row['count'] ?> Placements</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill" style="width: <?= min(($row['count'] / 20) * 100, 100) ?>%;"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center;" class="text-muted">No placement history available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
