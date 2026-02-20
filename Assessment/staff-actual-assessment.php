<?php
require_once '../config.php';
requireLogin('staff');

$conn = getDBConnection();
$attachmentId = $_GET['attachment_id'] ?? null;

// Ensure they came through the code verification step
if (!$attachmentId || !isset($_SESSION['authorized_assessment_' . $attachmentId])) {
    header("Location: ../Supervisor/staff-supervision.php");
    exit();
}

// Fetch Student and Attachment details
$stmt = $conn->prepare("
    SELECT s.FirstName, s.LastName, u.Username as AdmissionNumber, s.Course, s.Faculty, ho.OrganizationName, l.Name as LecName
    FROM attachment a
    JOIN student s ON a.StudentID = s.StudentID
    JOIN users u ON s.UserID = u.UserID
    JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
    JOIN supervision sv ON a.AttachmentID = sv.AttachmentID
    JOIN lecturer l ON sv.LecturerID = l.LecturerID
    WHERE a.AttachmentID = ? AND sv.LecturerID = ?
");

$lecturerId = $_SESSION['LecturerID'] ?? null;
if (!$lecturerId) {
     $stmtLec = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
     $stmtLec->bind_param("s", $_SESSION['staff_number']);
     $stmtLec->execute();
     if ($res = $stmtLec->get_result()->fetch_assoc()) $lecturerId = $res['LecturerID'];
     $stmtLec->close();
}

$stmt->bind_param("ii", $attachmentId, $lecturerId);
$stmt->execute();
$details = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$details) {
    echo "Could not fetch attachment details or you are not authorized.";
    exit();
}

$conn->close();

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
    <title>University Supervision Attachment Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Dashboards/staff-dashboard.css">
    <style>
        body { background-color: #333; /* Dark background behind the form paper */ }
        
        .paper-form {
            background: white;
            max-width: 900px;
            margin: 40px auto;
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-header {
            background-color: #b22222;
            color: white;
            padding: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .form-header img {
            height: 60px;
            background: white;
            border-radius: 50%;
            padding: 5px;
        }

        .form-header h1 {
            margin: 0;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-header p { margin: 5px 0 0 0; font-size: 1rem; }

        .form-body { padding: 40px; }

        .student-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 40px;
            margin-bottom: 40px;
        }

        .info-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-field label {
            font-weight: bold;
            font-size: 0.9rem;
            color: #333;
        }

        .info-field .read-only-box {
            background-color: #f1f5f9;
            padding: 10px 15px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.95rem;
        }

        .section-title {
            color: #b22222;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 2px solid #b22222;
            padding-bottom: 5px;
            display: inline-block;
        }

        .rating-scale-legend {
            font-size: 0.85rem;
            font-weight: bold;
            color: #b22222;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .criteria-table th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e2e8f0;
            color: #b22222;
            font-size: 0.9rem;
        }

        .criteria-table th.c-score { text-align: center; width: 120px; }

        .criteria-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            color: #1e293b;
            font-weight: 500;
        }

        .score-input {
            width: 80px;
            padding: 8px;
            text-align: center;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-weight: bold;
            background-color: #f8fafc;
        }

        .score-input:focus {
            outline: none;
            border-color: #b22222;
        }

        .total-row td {
            font-size: 1.1rem;
            font-weight: bold;
            color: #b22222;
            padding-top: 20px;
            border-bottom: none;
        }
        
        .total-marks-display {
            background-color: #fefce8;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            border: 1px solid #fef08a;
            color: #854d0e;
        }

        .comments-section {
            margin-bottom: 30px;
        }

        .comments-section label {
            display: block;
            font-size: 0.95rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .comments-section textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background-color: #f8fafc;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .declaration {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            background-color: #fef2f2;
            padding: 15px;
            border-radius: 4px;
            border: 1px dashed #fca5a5;
            margin-bottom: 30px;
        }

        .declaration input[type="checkbox"] {
            margin-top: 4px;
            transform: scale(1.2);
            accent-color: #b22222;
        }

        .declaration p { margin: 0; font-size: 0.85rem; color: #b22222; font-weight: 500; }

        .btn-submit-form {
            background-color: #b22222;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.2s;
        }
        .btn-submit-form:disabled { background-color: #fca5a5; cursor: not-allowed; }
        .btn-submit-form:hover:not(:disabled) { background-color: #8a1919; }
    </style>
</head>
<body>

    <div class="paper-form">
        <div class="form-header">
            <img src="../assets/cuea-logo.png" alt="CUEA Logo">
            <div>
                <p>The Catholic University of Eastern Africa</p>
                <h1>University Supervision Attachment Form</h1>
            </div>
        </div>

        <form action="process-assessment.php" method="POST" class="form-body">
            <input type="hidden" name="attachment_id" value="<?php echo htmlspecialchars($attachmentId); ?>">
            <input type="hidden" name="lecturer_id" value="<?php echo htmlspecialchars($lecturerId); ?>">

            <div class="student-info-grid">
                <div class="info-field">
                    <label>Name</label>
                    <div class="read-only-box"><?php echo htmlspecialchars($details['FirstName'] . ' ' . $details['LastName']); ?></div>
                </div>
                <div class="info-field">
                    <label>Admission Number</label>
                    <div class="read-only-box"><?php echo htmlspecialchars($details['AdmissionNumber']); ?></div>
                </div>
                <div class="info-field">
                    <label>Faculty</label>
                    <div class="read-only-box"><?php echo htmlspecialchars($details['Faculty']); ?></div>
                </div>
                <div class="info-field">
                    <label>Department / Course</label>
                    <div class="read-only-box"><?php echo htmlspecialchars($details['Course']); ?></div>
                </div>
                <div class="info-field">
                    <label>Host Organization</label>
                    <div class="read-only-box"><?php echo htmlspecialchars($details['OrganizationName']); ?></div>
                </div>
                <div class="info-field">
                    <label>Name of Assessor</label>
                    <div class="read-only-box"><?php echo htmlspecialchars($details['LecName']); ?></div>
                </div>
            </div>

            <div class="section-title">Assessment Areas</div>
            <p style="font-size: 0.9rem; color: #4b5563; margin-top: 5px; margin-bottom: 15px;">
                Please indicate your assessment of the performance of the student during the attachment period using the following scale:
            </p>

            <div class="rating-scale-legend">
                <span>0-2 - Poor</span>
                <span>3-4 - Below Average</span>
                <span>5-6 - Average</span>
                <span>7-8 - Good</span>
                <span>9-10 - Excellent</span>
            </div>
            
            <p style="font-size: 0.85rem; font-style: italic; color: #64748b; margin-bottom: 10px;">
                * Rate each individual criterion out of 10. The sum will form the final percentage.
            </p>

            <table class="criteria-table">
                <thead>
                    <tr>
                        <th>Assessment Criteria</th>
                        <th class="c-score">Rating ( /10)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteriaList as $index => $criteria): ?>
                        <tr>
                            <td><?php echo $criteria; ?></td>
                            <td class="c-score">
                                <input type="number" name="criteria[<?php echo $index; ?>]" class="score-input criteria-input" min="0" max="10" required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td>Total Assessment Marks</td>
                        <td class="c-score">
                            <div class="total-marks-display" id="totalMarksDisplay">0%</div>
                            <input type="hidden" name="total_score" id="totalScoreHidden" value="0">
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="comments-section">
                <label>Additional Comments:</label>
                <textarea name="comments" placeholder="Write comprehensive feedback regarding the student's performance..." required></textarea>
            </div>

            <div class="declaration">
                <input type="checkbox" id="declarationCheck" required>
                <p>I hereby confirm that all details assessed above for the student are accurate and comply with the University Regulations.</p>
            </div>

            <button type="submit" class="btn-submit-form" id="submitBtn" disabled>Submit Assessment Grading Form</button>
        </form>
    </div>

    <script>
        const inputs = document.querySelectorAll('.criteria-input');
        const display = document.getElementById('totalMarksDisplay');
        const hiddenTotal = document.getElementById('totalScoreHidden');
        const checkbox = document.getElementById('declarationCheck');
        const submitBtn = document.getElementById('submitBtn');

        function calculateTotal() {
            let total = 0;
            inputs.forEach(input => {
                if (input.value) {
                    total += parseInt(input.value);
                }
            });
            display.textContent = total + '%';
            hiddenTotal.value = total;
            checkValidity();
        }

        function checkValidity() {
            let allFilled = Array.from(inputs).every(input => input.value !== '');
            submitBtn.disabled = !(allFilled && checkbox.checked);
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        checkbox.addEventListener('change', checkValidity);
    </script>
</body>
</html>
