<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$assessmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$studentId = $_SESSION['student_id'] ?? null;

if (!$assessmentId || !$studentId) {
    echo "Invalid request.";
    exit();
}

// Fetch Assessment and related details, ensuring the student owns this assessment
$sql = "
    SELECT 
        a.AssessmentDate, a.AssessmentType, a.Marks, a.Remarks, a.CriteriaScores,
        s.FirstName, s.LastName, u.Username as AdmissionNumber, s.Course, s.Faculty,
        ho.OrganizationName,
        l.Name as AssessorName, l.Department as AssessorDept
    FROM assessment a
    JOIN attachment att ON a.AttachmentID = att.AttachmentID
    JOIN student s ON att.StudentID = s.StudentID
    JOIN users u ON s.UserID = u.UserID
    JOIN hostorganization ho ON att.HostOrgID = ho.HostOrgID
    LEFT JOIN lecturer l ON a.LecturerID = l.LecturerID
    WHERE a.AssessmentID = ? AND s.StudentID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $assessmentId, $studentId);
$stmt->execute();
$result = $stmt->get_result();
$assessment = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$assessment) {
    echo "Assessment not found or you do not have permission to view it.";
    exit();
}

$criteriaScores = json_decode($assessment['CriteriaScores'], true) ?: [];

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Form - <?php echo htmlspecialchars($assessment['FirstName'] . ' ' . $assessment['LastName']); ?></title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif; /* Formal font for official documents */
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            height: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14pt;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            border-bottom: 1px dotted #000;
            width: 70%;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        .criteria-table th, .criteria-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .criteria-table th {
            background-color: #f0f0f0;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        .score-col {
            width: 15%;
            text-align: center !important;
        }

        .total-row {
            font-weight: bold;
        }
        .total-row td {
            background-color: #f0f0f0;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .remarks-section {
            margin-bottom: 30px;
            font-size: 11pt;
        }
        .remarks-box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 80px;
            margin-top: 5px;
        }

        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
        }
        .sig-block {
            width: 45%;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 30px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #8B1538;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>

    <div class="container">
        <div class="header">
            <!-- Ensure path exists or remove image if broken -->
            <img src="../assets/cuea-logo.png" alt="CUEA Logo">
            <h1>The Catholic University of Eastern Africa</h1>
            <h2>University Supervision Attachment Assessment Form</h2>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Student Name:</td>
                <td class="info-value"><?php echo htmlspecialchars($assessment['FirstName'] . ' ' . $assessment['LastName']); ?></td>
            </tr>
            <tr>
                <td class="info-label">Admission Number:</td>
                <td class="info-value"><?php echo htmlspecialchars($assessment['AdmissionNumber']); ?></td>
            </tr>
            <tr>
                <td class="info-label">Faculty:</td>
                <td class="info-value"><?php echo htmlspecialchars($assessment['Faculty']); ?></td>
            </tr>
            <tr>
                <td class="info-label">Course:</td>
                <td class="info-value"><?php echo htmlspecialchars($assessment['Course']); ?></td>
            </tr>
            <tr>
                <td class="info-label">Host Organization:</td>
                <td class="info-value"><?php echo htmlspecialchars($assessment['OrganizationName']); ?></td>
            </tr>
            <tr>
                <td class="info-label">Assessment Type:</td>
                <td class="info-value"><?php echo htmlspecialchars($assessment['AssessmentType']); ?></td>
            </tr>
            <tr>
                <td class="info-label">Date of Assessment:</td>
                <td class="info-value"><?php echo date('F j, Y', strtotime($assessment['AssessmentDate'])); ?></td>
            </tr>
        </table>

        <div class="section-title">Assessment Breakdown</div>

        <table class="criteria-table">
            <thead>
                <tr>
                    <th>Assessment Criteria</th>
                    <th class="score-col">Score (/10)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($criteriaList as $index => $criteria): ?>
                    <tr>
                        <td><?php echo $criteria; ?></td>
                        <td class="score-col"><?php echo isset($criteriaScores[$index]) ? htmlspecialchars($criteriaScores[$index]) : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td style="text-align: right;">Total Marks</td>
                    <td class="score-col"><?php echo htmlspecialchars($assessment['Marks']); ?>%</td>
                </tr>
            </tbody>
        </table>

        <div class="remarks-section">
            <div style="font-weight: bold;">General Remarks by the Assessor:</div>
            <div class="remarks-box">
                <?php echo nl2br(htmlspecialchars($assessment['Remarks'])); ?>
            </div>
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Assessor's Signature</strong></div>
                <div>Name: <?php echo htmlspecialchars($assessment['AssessorName'] ?? '_________________'); ?></div>
                <div>Date: <?php echo date('F j, Y', strtotime($assessment['AssessmentDate'])); ?></div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div><strong>Student's Signature</strong></div>
                <div>Name: <?php echo htmlspecialchars($assessment['FirstName'] . ' ' . $assessment['LastName']); ?></div>
                <div>Date: _________________</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print prompt on load
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
