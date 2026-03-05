<?php use App\Core\Helpers; ?>

<style>
    .code-display {
        background-color: #f3f4f6;
        padding: 6px 12px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 1.1em;
        letter-spacing: 2px;
        font-weight: bold;
        color: #111827;
        border: 1px solid #e5e7eb;
    }
    .btn-generate {
        background-color: #8B1538;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 0.9rem;
    }
    .btn-generate:hover {
        background-color: #6b0f2a;
    }
</style>

<div class="content-grid">
    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">Generate Assessment Codes</h2>
        <p style="color: #6b7280; margin-bottom: 1.5rem;">Generate a unique code for the university supervisor to start an assessment for each student placed at your organization.</p>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Placement Dates</th>
                        <th>Assessment Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && $students->num_rows > 0): ?>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                                <td><?= htmlspecialchars($row['Course']) ?></td>
                                <td style="font-size: 0.9em; color: #6b7280;">
                                    <?= date('M j, Y', strtotime($row['StartDate'])) ?> - <?= date('M j, Y', strtotime($row['EndDate'])) ?>
                                </td>
                                <td>
                                    <?php if ($row['AssessmentCode']): ?>
                                        <span class="code-display"><?= htmlspecialchars($row['AssessmentCode']) ?></span>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-style: italic;">Not generated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="<?= Helpers::baseUrl('/host/supervision/generate') ?>" method="POST">
                                        <input type="hidden" name="attachment_id" value="<?= $row['AttachmentID'] ?>">
                                        <button type="submit" class="btn-generate">
                                            <?= $row['AssessmentCode'] ? 'Regenerate Code' : 'Generate Code' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280; padding: 20px;">No students are currently placed at your organization.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
