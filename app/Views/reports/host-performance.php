<?php use App\Core\Helpers; ?>
<link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports-dashboard.css') ?>">

<div class="card">
    <div class="report-card-header">
        <h2 class="report-card-title">Organization Performance Summary</h2>
        <a href="<?= Helpers::baseUrl('/reports/print/host-performance' . (isset($hostId) ? '?host_id=' . urlencode($hostId) : '')) ?>" target="_blank" class="btn report-tag-dark">
            <i class="fas fa-download"></i> Download Report (PDF)
        </a>
    </div>
    
    <div class="table-container">
        <div class="table-actions" style="margin-bottom: 15px;">
            <input type="text" id="tableSearch" placeholder="Search comments or students..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 300px;">
        </div>
        <table id="summaryTable">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Evaluation Date</th>
                    <th>Host Comment Preview</th>
                    <th>Overall Verdict</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($performance && $performance->num_rows > 0): ?>
                    <?php while($row = $performance->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['StartDate'])) ?></td>
                            <td style="font-style: italic;">
                                "<?= htmlspecialchars(substr((string)$row['HostSupervisorComments'], 0, 80)) ?><?= strlen((string)$row['HostSupervisorComments']) > 80 ? '...' : '' ?>"
                            </td>
                            <td>
                                <span class="report-tag report-tag-neutral">Satisfactory</span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 40px; color: #9ca3af;">No evaluative comments found yet.</td></tr>
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
