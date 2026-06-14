<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="report-header">
    <div class="report-title">
        <h1>Placement Completions Report</h1>
        <p class="report-subtitle">Verified attachment ends, clearance statuses, and final grades for all students.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= Helpers::baseUrl('/admin/reports') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="<?= Helpers::baseUrl('/reports/print/placement-completions') ?>" target="_blank" class="btn report-tag-dark">
            <i class="fas fa-print"></i> Print / Download PDF
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="summary-cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
    <div class="summary-card">
        <div class="card-content">
            <h3>Completed</h3>
            <p class="card-number"><?= $report['total_completed'] ?></p>
            <small class="text-muted">Attachments Finished</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Ongoing</h3>
            <p class="card-number"><?= $report['total_ongoing'] ?></p>
            <small class="text-muted">Active Placements</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Avg Final Score</h3>
            <p class="card-number"><?= number_format(($report['avg_first'] + $report['avg_final']) / 2, 1) ?>%</p>
            <small class="text-muted">Combined Assessments</small>
        </div>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="report-card-header">
        <h2 class="report-card-title">
            <i class="fas fa-table"></i> Student Completion Register
        </h2>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">

            <input type="text" id="tableSearch" placeholder="Search student, course, org…"
                   style="padding: 7px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; width: 240px;">
        </div>
    </div>

    <div class="table-container" style="box-shadow: none; border: none; overflow-x: auto;">
        <table id="completionsTable" style="min-width: 1100px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Adm. No.</th>
                    <th>Course</th>
                    <th>Host Organization</th>
                    <th>Supervisor</th>
                    <th style="text-align:center;">Period</th>
                    <th style="text-align:center;">Logbook Wks</th>
                    <th style="text-align:center;">Final Score</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $students = $report['students'];
                $i = 0;
                if ($students && $students->num_rows > 0):
                    while ($row = $students->fetch_assoc()):
                        $i++;
                ?>
                <tr>
                    <td style="; font-size:12px;"><?= $i ?></td>
                    <td style="font-weight:600; white-space: nowrap;">
                        <?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?>
                    </td>
                    <td style="font-family: monospace; font-size:12px;"><?= htmlspecialchars($row['AdmNumber']) ?></td>
                    <td style="font-size:12px; max-width:160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($row['Course']) ?>">
                        <?= htmlspecialchars($row['Course'] ?? '—') ?>
                    </td>
                    <td style="font-size:12px; max-width:160px;" title="<?= htmlspecialchars($row['OrganizationName']) ?>">
                        <?= htmlspecialchars($row['OrganizationName']) ?>
                    </td>
                    <td style="font-size:12px; ;"><?= htmlspecialchars($row['SupervisorName'] ?? '—') ?></td>
                    <td class="text-xs text-muted" style="text-align:center; white-space: nowrap;">
                        <?= date('d M Y', strtotime($row['StartDate'])) ?> –<br>
                        <?= date('d M Y', strtotime($row['EndDate'])) ?>
                    </td>
                    <td style="text-align:center;">
                        <span class="report-tag report-tag-neutral">
                            <?= $row['ApprovedWeeks'] ?>/12
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:600;">
                        <?= $row['AvgScore'] !== null ? number_format($row['AvgScore'], 2) . '%' : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td style="text-align:center;">
                        <a href="<?= Helpers::baseUrl('/reports/print/completion?id=' . $row['StudentID']) ?>" target="_blank"
                           title="Print Completion Certificate"
                           class="btn btn-outline text-xs">
                            <i class="fas fa-certificate"></i> Certificate
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="10" style="text-align:center; padding:30px;" class="text-muted">
                        <i class="fas fa-inbox" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                        No placement records found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    const searchInput  = document.getElementById('tableSearch');
    const table        = document.getElementById('completionsTable');

    function filterTable() {
        const query   = searchInput.value.toLowerCase();
        const rows    = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text      = row.textContent.toLowerCase();
            const matchQ    = text.includes(query);
            row.style.display = matchQ ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
})();
</script>
