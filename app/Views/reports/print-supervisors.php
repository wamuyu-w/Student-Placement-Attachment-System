<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor List</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 11pt; }
        .table th { background-color: #f0f0f0; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #8B1538; color: #fff; border: none; padding: 10px 20px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print List</button>

    <div class="header">
        <h1>The Catholic University of Eastern Africa</h1>
        <h2>Registered Academic Supervisors</h2>
        <p>Date Generated: <?= date('d M Y') ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Faculty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($supervisors) && $supervisors && $supervisors->num_rows > 0): ?>
                <?php while($row = $supervisors->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= htmlspecialchars($row['Department']) ?></td>
                        <td><?= htmlspecialchars($row['Faculty']) ?></td>
                        <td><?= htmlspecialchars($row['Status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center;">No supervisors found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
