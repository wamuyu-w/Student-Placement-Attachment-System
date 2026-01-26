<?php
require_once '../config.php';
requireLogin('staff');
$conn = getDBConnection();

// Get LecturerID
$lecturerId = $_SESSION['LecturerID'] ?? null;
if (!$lecturerId) {
    // Try to resolve from staff number if session only has that
    $staffNumber = $_SESSION['staff_number'] ?? null;
    if ($staffNumber) {
        $stmt = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
        $stmt->bind_param("s", $staffNumber);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) $lecturerId = $row['LecturerID'];
        $stmt->close();
    }
}

// Fetch supervised students
$students = [];
if ($lecturerId) {
    $sql = "SELECT s.StudentID, s.FirstName, s.LastName, h.OrganizationName, a.AttachmentID
            FROM supervision sv
            JOIN attachment a ON sv.AttachmentID = a.AttachmentID
            JOIN student s ON a.StudentID = s.StudentID
            JOIN hostorganization h ON a.HostOrgID = h.HostOrgID
            WHERE sv.LecturerID = ? AND a.AttachmentStatus = 'Ongoing'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Assessment - Staff</title>
    <link rel="stylesheet" href="../Dashboards/staff-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        .form-select, .form-input, .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
        }
        .btn-submit {
            background-color: #8B1538;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #70102d;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea logo">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        <nav class="sidebar-nav">
             <a href="../Dashboards/staff-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Students/staff-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Logbook/staff-logbook.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Logbooks</span>
            </a>
             <a href="staff-assessment.php" class="nav-item active">
                <i class="fas fa-clipboard-check"></i>
                <span>Assessment</span>
            </a>
            <a href="../Reports/staff-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
        <div class="sidebar-footer">
             <a href="../Login Pages/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <div class="main-content">
        <header class="main-header">
            <h1 class="page-title">Student Assessment</h1>
        </header>

        <div class="content-grid">
            <div class="form-container">
                <h2 style="margin-bottom: 20px; color: #8B1538;">Grade Student Performance</h2>
                
                <?php if (isset($_GET['success'])): ?>
                    <div style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                        Assessment submitted successfully!
                    </div>
                <?php endif; ?>
                
                <form action="process-assessment.php" method="POST">
                    <input type="hidden" name="lecturer_id" value="<?php echo htmlspecialchars($lecturerId); ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Select Student</label>
                        <select name="attachment_id" class="form-select" required>
                            <option value="">-- Choose Student --</option>
                            <?php foreach($students as $student): ?>
                                <option value="<?php echo $student['AttachmentID']; ?>">
                                    <?php echo htmlspecialchars($student['FirstName'] . ' ' . $student['LastName'] . ' (' . $student['OrganizationName'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Total Score (0-100)</label>
                        <input type="number" name="total_score" class="form-input" min="0" max="100" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Grade (A, B, C, D, F)</label>
                        <select name="grade" class="form-select" required>
                            <option value="">-- Select Grade --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Comments / Remarks</label>
                        <textarea name="comments" class="form-textarea" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Submit Assessment</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
