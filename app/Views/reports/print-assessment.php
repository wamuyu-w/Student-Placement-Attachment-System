<?php
use App\Core\Helpers;

if (!isset($assessment) || empty($assessment)) {
    echo "<div style='text-align:center; padding:20px; font-family:sans-serif;'>Assessment data not found or unauthorized access.</div>";
    exit;
}

$criteriaList = [
    "Availability of required documents",
    "Degree of Organization of Daily Entries in the Logbook",
    "Ability to work in teams",
    "Accomplishment of Assignments",
    "Presence at designated areas",
    "Communication Skills",
    "Mannerisms",
    "Level of adaptability of the attachee in the organization",
    "Student's understanding of assignments/tasks given",
    "Oral Presentation"
];
$criteriaScores = json_decode($assessment['CriteriaScores'], true) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessment Form - <?= htmlspecialchars($assessment['FirstName']) ?></title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0 0; font-size: 14pt; }
        .info-table, .criteria-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11pt; }
        .info-table td { padding: 5px; vertical-align: top; }
        .info-label { font-weight: bold; width: 30%; }
        .info-value { border-bottom: 1px dotted #000; width: 70%; }
        .criteria-table th, .criteria-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .criteria-table th { background-color: #f0f0f0; }
        .score-col { width: 15%; text-align: center; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
        .remarks-box { border: 1px solid #000; padding: 10px; min-height: 80px; margin-top: 5px; }
        .signatures { margin-top: 40px; display: flex; justify-content: space-between; font-size: 11pt; }
        .sig-block { width: 45%; }
        .sig-line { border-bottom: 1px solid #000; margin-bottom: 5px; height: 30px; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #8B1538; color: #fff; border: none; padding: 10px 20px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>

    <div class="header">
        <h1>The Catholic University of Eastern Africa</h1>
        <h2>University Supervision Attachment Assessment Form</h2>
    </div>

    <table class="info-table">
        <tr><td class="info-label">Student Name:</td><td class="info-value"><?= htmlspecialchars($assessment['FirstName'] . ' ' . $assessment['LastName']) ?></td></tr>
        <tr><td class="info-label">Admission Number:</td><td class="info-value"><?= htmlspecialchars($assessment['AdmissionNumber']) ?></td></tr>
        <tr><td class="info-label">Faculty:</td><td class="info-value"><?= htmlspecialchars($assessment['Faculty']) ?></td></tr>
        <tr><td class="info-label">Course:</td><td class="info-value"><?= htmlspecialchars($assessment['Course']) ?></td></tr>
        <tr><td class="info-label">Host Organization:</td><td class="info-value"><?= htmlspecialchars($assessment['OrganizationName']) ?></td></tr>
        <tr><td class="info-label">Assessment Type:</td><td class="info-value"><?= htmlspecialchars($assessment['AssessmentType']) ?></td></tr>
        <tr><td class="info-label">Date:</td><td class="info-value"><?= date('F j, Y', strtotime($assessment['AssessmentDate'])) ?></td></tr>
    </table>

    <h3>Assessment Breakdown</h3>
    <table class="criteria-table">
        <thead>
            <tr><th>Assessment Criteria</th><th class="score-col">Score (/10)</th></tr>
        </thead>
        <tbody>
            <?php foreach ($criteriaList as $index => $criteria): ?>
                <tr>
                    <td><?= $criteria ?></td>
                    <td class="score-col"><?= isset($criteriaScores[$index]) ? htmlspecialchars($criteriaScores[$index]) : 'N/A' ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td style="text-align: right;">Total Marks</td>
                <td class="score-col"><?= htmlspecialchars($assessment['Marks']) ?>%</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-bottom: 30px;">
        <strong>General Remarks:</strong>
        <div class="remarks-box"><?= nl2br(htmlspecialchars($assessment['Remarks'])) ?></div>
    </div>

    <div class="signatures">
        <div class="sig-block">
            <div class="sig-line"></div>
            <div><strong>Assessor's Signature</strong></div>
            <div>Name: <?= htmlspecialchars($assessment['AssessorName'] ?? '_________________') ?></div>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <div><strong>Student's Signature</strong></div>
        </div>
    </div>
</body>
</html>
