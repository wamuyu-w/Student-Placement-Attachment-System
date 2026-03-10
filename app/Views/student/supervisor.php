<?php use App\Core\Helpers; ?>

<div class="supervisor-container">
    <h1 class="supervisor-title">My Supervisor</h1>

    <?php if (!empty($supervisors)): ?>
        <?php foreach ($supervisors as $sup): ?>
            <div class="supervisor-card">
                <div class="supervisor-info">
                    <div class="supervisor-avatar">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($sup['Name']) ?>&background=e5e7eb&color=374151&size=128" alt="<?= htmlspecialchars($sup['Name']) ?>" style="width: 100%; height: 100%; border-radius: 50%;">
                    </div>
                    <div class="supervisor-details">
                        <h3><?= htmlspecialchars($sup['Name']) ?></h3>
                        <p><strong>Department:</strong> <?= htmlspecialchars($sup['Department']) ?></p>
                        <p class="supervisor-role">Supervisor</p>
                    </div>
                </div>
                <button class="btn-contact" onclick="alert('Messaging feature coming soon!')">Contact Supervisor</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="section-card" style="text-align: center; padding: 40px; color: #6b7280; margin-bottom: 24px;">
            <i class="fas fa-user-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
            <p>You have not been assigned an academic supervisor yet.</p>
        </div>
    <?php endif; ?>

    <div class="supervisor-grid">
        <!-- Supervision Progress Tracker -->
        <div class="section-card">
            <div class="section-header">
                <h2>Supervision Progress Tracker</h2>
            </div>
            
            <div class="tracker-list">
                <?php 
                $hasAssessments = !empty($assessments);
                if ($hasAssessments): 
                    foreach ($assessments as $index => $ass):
                        $isFirst = ($index === count($assessments) - 1); // Simplistic assumption for "First" vs "Final"
                        $title = $isFirst ? "First Assessment" : "Final Assessment";
                        $statusText = $ass['Marks'] !== null ? "(Completed)" : "(Scheduled)";
                ?>
                <div class="progress-item">
                    <div class="progress-icon">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <div class="progress-content">
                        <div class="progress-top">
                            <div>
                                <span class="assessment-title"><?= $title ?> <?= $statusText ?></span>
                                <span class="assessment-date"><?= date('F jS, Y', strtotime($ass['AssessmentDate'])) ?></span>
                            </div>
                        </div>
                        <?php if ($ass['Marks'] !== null): ?>
                        <div class="assessment-meta">
                            <span class="score-badge">Score: <?= number_format($ass['Marks'], 0) ?>/100</span>
                            <span class="assessor-name">Assessed by: <?= htmlspecialchars($ass['LecturerName']) ?></span>
                            <a href="<?= Helpers::baseUrl('/assessment/view?id=' . $ass['AssessmentID']) ?>" target="_blank" class="btn-view-form" style="text-decoration: none;">View Form</a>
                        </div>
                        <?php else: ?>
                        <div class="assessment-meta">
                            <span class="status-scheduled">Scheduled</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    endforeach;
                else: 
                ?>
                    <!-- Fallback mockup data if no real assessments exist yet -->
                    <div class="progress-item">
                        <div class="progress-icon">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div class="progress-content">
                            <div class="progress-top">
                                <div>
                                    <span class="assessment-title">First Assessment (Pending)</span>
                                    <span class="assessment-date">To be scheduled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Feedback History -->
        <div class="section-card">
            <div class="section-header">
                <h2>Feedback History</h2>
                <a href="#" class="view-all-link">View All</a>
            </div>
            
            <div class="feedback-list">
                <?php if ($hasAssessments): ?>
                    <?php foreach ($assessments as $ass): if (!empty($ass['Remarks'])): ?>
                    <div class="feedback-item">
                        <h3><?= htmlspecialchars($ass['AssessmentType'] ?? 'Assessment') ?> Feedback</h3>
                        <p class="feedback-text"><?= nl2br(htmlspecialchars($ass['Remarks'])) ?></p>
                    </div>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="feedback-item">
                        <p class="feedback-text" style="font-style: italic; color: #9ca3af;">No feedback history available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
