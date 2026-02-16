<?php
require_once '../config.php';
requireLogin('staff');

$conn = getDBConnection();
$staffID = $_SESSION['LecturerID'] ?? null;

if (!$staffID) {
    // Fallback logic similar to dashboard
    $staffNumber = $_SESSION['staff_number'] ?? null;
    if ($staffNumber) {
        $stmt = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
        $stmt->bind_param("s", $staffNumber);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($data = $res->fetch_assoc()) {
            $staffID = $data['LecturerID'];
        }
    }
}

if (!$staffID) {
    header("Location: ../Login Pages/login-staff.php");
    exit();
}

// Fetch supervised students
$sql = "SELECT 
            u.Username as AdmissionNumber, s.FirstName, s.LastName, s.Course, s.YearOfStudy,
            a.AttachmentID, a.AttachmentStatus, ho.OrganizationName,
            (SELECT COUNT(*) FROM assessment WHERE AttachmentID = a.AttachmentID) as AssessmentCount,
            (SELECT MAX(AssessmentDate) FROM assessment WHERE AttachmentID = a.AttachmentID) as LastAssessment
        FROM supervision sv
        JOIN attachment a ON sv.AttachmentID = a.AttachmentID
        JOIN student s ON a.StudentID = s.StudentID
        JOIN users u ON s.UserID = u.UserID
        JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID
        WHERE sv.LecturerID = ? AND (a.AttachmentStatus = 'Active' OR a.AttachmentStatus = 'Completed' OR a.AttachmentStatus = 'Ongoing')
        ORDER BY s.FirstName ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staffID);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervision - Staff Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Dashboards/staff-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .btn-schedule {
            background-color: #8B1538;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn-schedule:hover {
            background-color: #70112d;
        }
        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
            margin-right: 0.5rem;
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
            <a href="../Reports/staff-reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="staff-supervision.php" class="nav-item active">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Supervision</span>
            </a>
            <a href="../Settings/staff-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
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
            <h1 class="page-title">Supervision Management</h1>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="profile-img" style="background: #8B1538; color: white; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;">
                        <?php echo strtoupper(substr($_SESSION['name'] ?? 'S', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 20px; font-size: 1.25rem; font-weight: 600;">Assigned Students</h2>
                <?php if ($result->num_rows > 0): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                                <th style="padding: 12px;">Student</th>
                                <th style="padding: 12px;">Course</th>
                                <th style="padding: 12px;">Host Organization</th>
                                <th style="padding: 12px;">Status</th>
                                <th style="padding: 12px;">Assessments</th>
                                <th style="padding: 12px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 500;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></div>
                                        <div style="font-size: 0.85em; color: #6b7280;"><?php echo htmlspecialchars($row['AdmissionNumber']); ?></div>
                                    </td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?> (Y<?php echo $row['YearOfStudy']; ?>)</td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['OrganizationName']); ?></td>
                                    <td style="padding: 12px;">
                                        <span class="status-badge" style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 0.85em;">
                                            <?php echo htmlspecialchars($row['AttachmentStatus']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if ($row['LastAssessment']): ?>
                                            <div>Last: <?php echo date('M d', strtotime($row['LastAssessment'])); ?></div>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">None</span>
                                        <?php endif; ?>
                                        <div style="font-size: 0.85em; color: #6b7280;">Total: <?php echo $row['AssessmentCount']; ?></div>
                                    </td>
                                    <td style="padding: 12px;">
                                        <button class="btn-schedule" onclick="openScheduleModal(<?php echo $row['AttachmentID']; ?>, '<?php echo htmlspecialchars(addslashes($row['FirstName'] . ' ' . $row['LastName'])); ?>')">
                                            <i class="fas fa-calendar-plus"></i> Schedule
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #6b7280; text-align: center; padding: 20px;">No students assigned for supervision yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 1rem;">Schedule Assessment</h3>
            <p style="margin-bottom: 1rem; color: #4b5563;">Student: <span id="modalStudentName" style="font-weight: 600;"></span></p>
            
            <form id="scheduleForm" onsubmit="handleScheduleSubmit(event)">
                <input type="hidden" id="attachmentId" name="attachment_id">
                
                <div class="form-group">
                    <label for="assessmentType">Assessment Type</label>
                    <select id="assessmentType" name="assessment_type" class="form-control" required>
                        <option value="First Assessment">First Assessment</option>
                        <option value="Mid-Term">Mid-Term Assessment</option>
                        <option value="Final">Final Assessment</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="assessmentDate">Date</label>
                    <input type="date" id="assessmentDate" name="assessment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="remarks">Notes (Optional)</label>
                    <textarea id="remarks" name="remarks" class="form-control" rows="3" placeholder="Any specific instructions..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" class="btn-schedule btn-secondary" onclick="closeScheduleModal()">Cancel</button>
                    <button type="submit" class="btn-schedule">Schedule Assessment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openScheduleModal(attachmentId, studentName) {
            document.getElementById('attachmentId').value = attachmentId;
            document.getElementById('modalStudentName').textContent = studentName;
            document.getElementById('scheduleModal').style.display = 'flex';
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').style.display = 'none';
        }

        function handleScheduleSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            fetch('process-schedule-assessment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Assessment scheduled successfully.',
                        icon: 'success',
                        confirmButtonColor: '#8B1538'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to schedule assessment',
                        icon: 'error',
                        confirmButtonColor: '#8B1538'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'An unexpected error occurred', 'error');
            });
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('scheduleModal')) {
                closeScheduleModal();
            }
        }
    </script>
</body>
</html>
