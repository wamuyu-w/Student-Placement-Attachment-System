<?php use App\Core\Helpers; ?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 800; color: #1f2937; margin: 0;">Placement Completions Report</h1>
        <p style="color: #6b7280; font-size: 13px; margin: 4px 0 0;">Verified attachment ends, clearance statuses, and final grades for all students.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= Helpers::baseUrl('/admin/reports') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="<?= Helpers::baseUrl('/reports/print/placement-completions') ?>" target="_blank" class="btn" style="background:#8B1538; color:#fff;">
            <i class="fas fa-print"></i> Print / Download PDF
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="summary-cards" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); margin-bottom: 24px;">
    <div class="summary-card">
        <div class="card-content">
            <h3>Completed</h3>
            <p class="card-number" style="color:#8B1538;"><?= $report['total_completed'] ?></p>
            <small style="color:#6b7280;">Attachments Finished</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Cleared</h3>
            <p class="card-number" style="color:#8B1538;"><?= $report['total_cleared'] ?></p>
            <small style="color:#6b7280;">Fully Cleared Students</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Ongoing</h3>
            <p class="card-number" style="color:#1a1a1a;"><?= $report['total_ongoing'] ?></p>
            <small style="color:#6b7280;">Active Placements</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Reports Approved</h3>
            <p class="card-number" style="color:#1a1a1a;"><?= $report['reports_approved'] ?></p>
            <small style="color:#6b7280;">Final Reports Verified</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Avg 1st Score</h3>
            <p class="card-number" style="color:#8B1538;"><?= $report['avg_first'] ?>%</p>
            <small style="color:#6b7280;">First Assessment</small>
        </div>
    </div>
    <div class="summary-card">
        <div class="card-content">
            <h3>Avg Final Score</h3>
            <p class="card-number" style="color:#8B1538;"><?= $report['avg_final'] ?>%</p>
            <small style="color:#6b7280;">Final Assessment</small>
        </div>
    </div>
</div>

<!-- Search + Table -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <h2 style="font-size: 1rem; font-weight: 700; margin: 0; color: #1f2937;">
            <i class="fas fa-table" style="color: #8B1538; margin-right: 6px;"></i> Student Completion Register
        </h2>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <select id="statusFilter" style="padding: 7px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; color: #374151;">
                <option value="">All Statuses</option>
                <option value="Cleared">Cleared</option>
                <option value="Not Cleared">Not Cleared</option>
            </select>
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
                    <th style="text-align:center;">1st Score</th>
                    <th style="text-align:center;">Final Score</th>
                    <th style="text-align:center;">Avg</th>
                    <th style="text-align:center;">Report</th>
                    <th style="text-align:center;">Clearance</th>
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
                        $cleared  = $row['ClearanceStatus'] === 'Cleared';
                        $avg      = $row['AvgScore'] !== null ? number_format($row['AvgScore'], 1) : '—';
                        $avgColor = $row['AvgScore'] >= 70 ? '#8B1538' : '#1a1a1a';
                ?>
                <tr data-clearance="<?= htmlspecialchars($row['ClearanceStatus']) ?>">
                    <td style="color:#9ca3af; font-size:12px;"><?= $i ?></td>
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
                    <td style="font-size:12px; color:#4b5563;"><?= htmlspecialchars($row['SupervisorName'] ?? '—') ?></td>
                    <td style="text-align:center; font-size:11px; white-space: nowrap; color:#4b5563;">
                        <?= date('d M Y', strtotime($row['StartDate'])) ?> –<br>
                        <?= date('d M Y', strtotime($row['EndDate'])) ?>
                    </td>
                    <td style="text-align:center;">
                        <span style="background:#f0e6ea; color:#8B1538; padding:2px 8px; border-radius:20px; font-size:12px; font-weight:600;">
                            <?= $row['ApprovedWeeks'] ?>/12
                        </span>
                    </td>
                    <td style="text-align:center; font-weight:600;">
                        <?= $row['FirstScore'] !== null ? $row['FirstScore'] . '%' : '<span style="color:#9ca3af;">—</span>' ?>
                    </td>
                    <td style="text-align:center; font-weight:600;">
                        <?= $row['FinalScore'] !== null ? $row['FinalScore'] . '%' : '<span style="color:#9ca3af;">—</span>' ?>
                    </td>
                    <td style="text-align:center; font-weight:700; color:<?= $avgColor ?>;">
                        <?= $avg !== '—' ? $avg . '%' : '<span style="color:#9ca3af;">—</span>' ?>
                    </td>
                    <td style="text-align:center;">
                        <?php
                        $rStatus = $row['ReportStatus'] ?? null;
                        $rColors = ['Approved' => '#8B1538', 'Submitted' => '#1a1a1a', 'Pending' => '#4b5563'];
                        $rBg     = ['Approved' => '#f0e6ea', 'Submitted' => '#f0f0f0', 'Pending' => '#f5f5f5'];
                        ?>
                        <?php if ($rStatus): ?>
                            <span style="background:<?= $rBg[$rStatus] ?? '#f3f4f6' ?>; color:<?= $rColors[$rStatus] ?? '#374151' ?>; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600;">
                                <?= $rStatus ?>
                            </span>
                        <?php else: ?>
                            <span style="color:#9ca3af; font-size:11px;">None</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <span style="background:<?= $cleared ? '#f0e6ea' : '#f5f5f5' ?>; color:<?= $cleared ? '#8B1538' : '#4b5563' ?>; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space: nowrap;">
                            <i class="fas fa-<?= $cleared ? 'check-circle' : 'clock' ?>" style="margin-right:4px;"></i>
                            <?= htmlspecialchars($row['ClearanceStatus']) ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="<?= Helpers::baseUrl('/reports/print/completion?id=' . $row['StudentID']) ?>" target="_blank"
                           title="Print Completion Certificate"
                           style="background:#8B1538; color:#fff; padding:4px 10px; border-radius:5px; font-size:11px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; white-space: nowrap;">
                            <i class="fas fa-certificate"></i> Certificate
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="14" style="text-align:center; padding:30px; color:#6b7280;">
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
    const statusFilter = document.getElementById('statusFilter');
    const table        = document.getElementById('completionsTable');

    function filterTable() {
        const query   = searchInput.value.toLowerCase();
        const status  = statusFilter.value.toLowerCase();
        const rows    = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text      = row.textContent.toLowerCase();
            const clearance = (row.dataset.clearance || '').toLowerCase();
            const matchQ    = text.includes(query);
            const matchS    = !status || clearance.includes(status);
            row.style.display = matchQ && matchS ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
})();
</script>
