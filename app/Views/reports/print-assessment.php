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
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/reports.css') ?>">
</head>
<body>
    

    <button class="no-print" onclick="window.print()">Print / Save PDF</button>
<div class="report-container">
        <div class="header">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo">
            <h1>The Catholic University of Eastern Africa</h1>
            <div class="header-motto">"Consecrate them in the Truth"</div>
            <div class="header-title">University Supervision Attachment Assessment Form</div>
        </div>
        <hr>

        <table class="info-table">
            <tr><td class="info-label">Student Name:</td><td class="info-value"><?= htmlspecialchars($assessment['FirstName'] . ' ' . $assessment['LastName']) ?></td></tr>
            <tr><td class="info-label">Admission Number:</td><td class="info-value"><?= htmlspecialchars($assessment['AdmissionNumber']) ?></td></tr>
            <tr><td class="info-label">Faculty:</td><td class="info-value"><?= htmlspecialchars($assessment['Faculty']) ?></td></tr>
            <tr><td class="info-label">Course:</td><td class="info-value"><?= htmlspecialchars($assessment['Course']) ?></td></tr>
            <tr><td class="info-label">Host Organization:</td><td class="info-value"><?= htmlspecialchars($assessment['OrganizationName']) ?></td></tr>
            <tr><td class="info-label">Assessment Type:</td><td class="info-value"><?= htmlspecialchars($assessment['AssessmentType']) ?></td></tr>
            <tr><td class="info-label">Date of Assessment:</td><td class="info-value"><?= date('F j, Y', strtotime($assessment['AssessmentDate'])) ?></td></tr>
        </table>

        <h3>Assessment Breakdown</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Assessment Criteria</th>
                    <th style="width: 15%; text-align: center;">Score (1/10)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($criteriaList as $index => $criteria): ?>
                    <tr>
                        <td><?= $criteria ?></td>
                        <td style="text-align: center;"><?= isset($criteriaScores[$index]) ? htmlspecialchars($criteriaScores[$index]) : 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="font-weight: bold;">
                    <td style="text-align: right;">Total Marks</td>
                    <td style="text-align: center;"><?= htmlspecialchars($assessment['Marks']) ?>%</td>
                </tr>
            </tbody>
        </table>

        <div class="remarks-section">
            <span class="remarks-label">General Remarks by the Assessor:</span>
            <div class="remarks-box"><?= nl2br(htmlspecialchars($assessment['Remarks'] ?? 'None')) ?></div>
        </div>

        <div class="footer-signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Assessor's Signature</div>
                <div style="margin-top: 5px;">Name: <?= htmlspecialchars($assessment['AssessorName'] ?? '_________________') ?></div>
                <div>Date: <?= date('F j, Y', strtotime($assessment['AssessmentDate'])) ?></div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Student's Signature</div>
                <div style="margin-top: 5px;">Name: <?= htmlspecialchars($assessment['FirstName'] . ' ' . $assessment['LastName']) ?></div>
                <div>Date: _________________</div>
            </div>
        </div>
    </div>



</body>
</html>
