<?php
require_once '../config.php';
requireLogin('staff');

$conn = getDBConnection();
$attachmentId = $_GET['attachment_id'] ?? ($_POST['attachment_id'] ?? null);

if (!$attachmentId) {
    header("Location: ../Supervisor/staff-supervision.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredCode = sanitizeInput($_POST['assessment_code']);
    
    $stmt = $conn->prepare("SELECT AssessmentCode FROM attachment WHERE AttachmentID = ?");
    $stmt->bind_param("i", $attachmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $actualCode = $row['AssessmentCode'];
        if (!$actualCode) {
            $error = "The Host Organization has not yet generated an assessment code for this student.";
        } elseif (trim(strtoupper($enteredCode)) === trim(strtoupper($actualCode))) {
            // Success
            $_SESSION['authorized_assessment_' . $attachmentId] = true;
            header("Location: staff-actual-assessment.php?attachment_id=" . urlencode($attachmentId));
            exit();
        } else {
            $error = "Invalid Assessment Code entered.";
        }
    } else {
        $error = "Attachment records could not be verified.";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Code Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Dashboards/staff-dashboard.css">
    <style>
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
        }
        .form-container h2 {
            margin-bottom: 10px;
            color: #111827;
        }
        .form-container p {
            color: #4b5563;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .code-input {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 3px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            background-color: #f9fafb;
            margin-bottom: 25px;
            transition: border-color 0.2s;
        }
        .code-input:focus {
            outline: none;
            border-color: #8B1538;
            background-color: white;
        }
        .btn-proceed {
            background-color: #8B1538;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }
        .btn-proceed:hover {
            background-color: #6a0f29;
        }
        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body style="background-color: #f3f4f6;">
    <div style="padding: 20px;">
        <a href="../Supervisor/staff-supervision.php" style="color: #6b7280; text-decoration: none;"><i class="fas fa-arrow-left"></i> Back to Supervision</a>
    </div>
    
    <div class="form-container">
        <h2>Supervision Code Verification</h2>
        <p>Kindly enter the code that has been provided to you by the host organization to proceed with the assessment.</p>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="attachment_id" value="<?php echo htmlspecialchars($attachmentId); ?>">
            <input type="text" name="assessment_code" class="code-input" placeholder="ENTER CODE" required autocomplete="off">
            <button type="submit" class="btn-proceed">Proceed to Assessment Form</button>
        </form>
    </div>
</body>
</html>
