<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="card">
    <div class="report-card-header">
        <h2 class="report-card-title">Student Assessment Summary</h2>
        <a href="<?= Helpers::baseUrl('/reports/print/assessment-summary') ?>" target="_blank" class="btn report-tag-dark">
            <i class="fas fa-download"></i> Download Report (PDF)
        </a>
    </div>

    <div class="table-container">
        <div class="table-actions" style="margin-bottom: 15px;">
            <input type="text" id="tableSearch" placeholder="Search by student or admission number..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 300px;">
        </div>
        <table id="summaryTable">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Admission No.</th>
                    <th>1st Assessment</th>
                    <th>2nd Assessment</th>
                    <th>Average Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($summary && $summary->num_rows > 0): ?>
                    <?php while($row = $summary->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstName']) ?></td>
                            <td><?= htmlspecialchars($row['AdmNumber']) ?></td>
                            <td style="text-align: center;"><?= $row['FirstScore'] !== null ? $row['FirstScore'] . '%' : '-' ?></td>
                            <td style="text-align: center;"><?= $row['SecondScore'] !== null ? $row['SecondScore'] . '%' : '-' ?></td>
                            <td style="text-align: center; font-weight: bold;" class="text-black">
                                <?= $row['AverageScore'] !== null ? number_format($row['AverageScore'], 1) . '%' : '-' ?>
                            </td>
                            <td>
                                <span class="report-tag report-tag-neutral">
                                    <?= htmlspecialchars($row['AttachmentStatus']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #9ca3af;">No assessment records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let input = this.value.toLowerCase();
        let rows = document.querySelectorAll('#summaryTable tbody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    });
</script>
