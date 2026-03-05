<?php 
use App\Core\Helpers; 
if (!isset($student) || empty($student) || !isset($progress) || empty($progress)) {
    echo "<div style='text-align:center; padding:20px; font-family:sans-serif;'>Certificate data unavailable. Ensure the student has a completed attachment.</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completion Certificate - <?= htmlspecialchars($student['FirstName']) ?></title>
    <style>
        @page { size: A4 landscape; margin: 20mm; }
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; padding: 40px; text-align: center; border: 5px double #8B1538; height: 90vh; box-sizing: border-box; }
        .logo { margin-bottom: 20px; }
        h1 { font-size: 28pt; text-transform: uppercase; color: #8B1538; margin-bottom: 10px; }
        h2 { font-size: 18pt; margin-bottom: 40px; font-weight: normal; }
        .content { font-size: 16pt; line-height: 1.6; margin-bottom: 50px; }
        .student-name { font-weight: bold; font-size: 22pt; border-bottom: 1px solid #000; display: inline-block; min-width: 300px; }
        .signatures { display: flex; justify-content: space-around; margin-top: 80px; }
        .sig-line { border-top: 1px solid #000; width: 250px; padding-top: 10px; font-size: 12pt; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #8B1538; color: #fff; border: none; padding: 10px 20px; cursor: pointer; }
        @media print { .print-btn { display: none; body { border: 5px double #000; } } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Certificate</button>

    <div class="logo">
        <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" height="80">
    </div>

    <h1>The Catholic University of Eastern Africa</h1>
    <h2>Certificate of Completion</h2>

    <div class="content">
        <p>This is to certify that</p>
        <div class="student-name"><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></div>
        <p>Registration Number: <strong><?= htmlspecialchars($student['AdmissionNumber']) ?></strong></p>
        <p>
            Has successfully completed the Industrial Attachment Program<br>
            for the period <strong><?= $progress['StartDate'] ? date('F Y', strtotime($progress['StartDate'])) : 'N/A' ?></strong> to <strong><?= $progress['EndDate'] ? date('F Y', strtotime($progress['EndDate'])) : 'N/A' ?></strong>.
        </p>
    </div>

    <div class="signatures">
        <div class="sig-line">
            Head of Department
        </div>
        <div class="sig-line">
            Academic Registrar
        </div>
    </div>
</body>
</html>
