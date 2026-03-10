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
        @page { size: A4 landscape; margin: 0; }
        body { 
            font-family: 'Times New Roman', serif; 
            color: #000; 
            background: #fff; 
            margin: 0;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            box-sizing: border-box;
        }
        .certificate-border {
            border: 15px double #8B1538;
            padding: 40px;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .certificate-border::before {
            content: "";
            position: absolute;
            top: 5px;
            right: 5px;
            bottom: 5px;
            left: 5px;
            border: 2px solid #8B1538;
        }
        .logo { margin-bottom: 20px; z-index: 10; }
        .logo img { height: 120px; width: auto; }
        h1 { font-size: 32pt; text-transform: uppercase; color: #8B1538; margin: 10px 0; z-index: 10; }
        h2 { font-size: 22pt; margin-bottom: 30px; font-weight: normal; color: #333; z-index: 10; }
        .content { font-size: 18pt; line-height: 1.8; margin-bottom: 40px; z-index: 10; text-align: center; }
        .student-name { font-weight: bold; font-size: 28pt; color: #000; margin: 15px 0; display: block; }
        .signatures { display: flex; justify-content: space-around; width: 100%; margin-top: 40px; z-index: 10; }
        .sig-block { width: 300px; text-align: center; }
        .sig-line { border-top: 2px solid #8B1538; padding-top: 10px; font-size: 14pt; color: #555; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #8B1538; color: #fff; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        @media print { 
            .print-btn { display: none; }
            body { padding: 0; }
            .certificate-border { border-color: #8B1538 !important; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save Certificate</button>

    <div class="certificate-border">
        <div class="logo">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
        </div>

        <h1>The Catholic University of Eastern Africa</h1>
        <h2>Certificate of Completion</h2>

        <div class="content">
            <p>This is to certify that</p>
            <span class="student-name"><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></span>
            <p>Registration Number: <strong><?= htmlspecialchars($student['AdmissionNumber']) ?></strong></p>
            <p>
                Has successfully completed the Industrial Attachment Program<br>
                from <strong><?= $progress['StartDate'] ? date('F Y', strtotime($progress['StartDate'])) : 'N/A' ?></strong> to <strong><?= $progress['EndDate'] ? date('F Y', strtotime($progress['EndDate'])) : 'N/A' ?></strong>.
            </p>
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line">
                    Head of Department
                </div>
            </div>
            <div class="sig-block">
                <div class="sig-line">
                    Academic Registrar
                </div>
            </div>
        </div>
    </div>
</body>
</html>
