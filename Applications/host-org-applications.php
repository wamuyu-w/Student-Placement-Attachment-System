<?php
require_once '../config.php';
requireLogin('host_org');

$conn = getDBConnection();
$hostOrgId = $_SESSION['host_org_id'];

// Retrieve all applications submitted to this Host Organization
$sql = "SELECT ja.ApplicationDate, s.FirstName, s.LastName, s.Course, ao.Description, ja.Status, ja.OpportunityID, ja.StudentID, ja.ResumePath, ja.ResumeLink, ja.Motivation
        FROM jobapplication ja
        JOIN student s ON ja.StudentID = s.StudentID
        JOIN attachmentopportunity ao ON ja.OpportunityID = ao.OpportunityID
        WHERE ja.HostOrgID = ?
        ORDER BY ja.ApplicationDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hostOrgId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - Host Organization</title>
    <link rel="stylesheet" href="../Dashboards/host-org-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            color: white;
            font-size: 0.9em;
            margin-right: 5px;
        }
        .btn-approve {
            background-color: #10B981;
        }
        .btn-reject {
            background-color: #EF4444;
        }
        .btn-view {
            background-color: #3B82F6;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-pending { background-color: #FEF3C7; color: #D97706; }
        .status-approved { background-color: #D1FAE5; color: #059669; }
        .status-rejected { background-color: #FEE2E2; color: #DC2626; }
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
            <a href="../Dashboards/host-org-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../Opportunities/host-management-opportunities.php" class="nav-item">
                <i class="fas fa-briefcase"></i>
                <span>Opportunities</span>
            </a>
            <a href="host-org-applications.php" class="nav-item active">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="../Students/host-org-students.php" class="nav-item">
                <i class="fas fa-graduation-cap"></i>
                <span>Students</span>
            </a>
            <a href="../Reports/host-org-reports.php" class="nav-item">
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
            <h1 class="page-title">Received Applications</h1>
            <div class="header-actions">
                <div class="user-profile">
                    <div class="profile-img" style="background: #8B1538; color: white; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;">
                         <?php echo strtoupper(substr($_SESSION['organization_name'] ?? 'H', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>
        <div class="content-grid">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <?php if ($result->num_rows > 0): ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                                <th style="padding: 12px;">Date</th>
                                <th style="padding: 12px;">Student</th>
                                <th style="padding: 12px;">Course</th>
                                <th style="padding: 12px;">Opportunity</th>
                                <th style="padding: 12px;">Resume</th>
                                <th style="padding: 12px;">Motivation</th>
                                <th style="padding: 12px;">Status</th>
                                <th style="padding: 12px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['ApplicationDate']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($row['Description']); ?></td>
                                    <td style="padding: 12px;">
                                        <?php if ($row['ResumePath']): ?>
                                            <a href="../assets/uploads/resumes/<?php echo htmlspecialchars($row['ResumePath']); ?>" target="_blank" class="text-blue-500 hover:underline">View PDF</a>
                                        <?php elseif ($row['ResumeLink']): ?>
                                            <a href="<?php echo htmlspecialchars($row['ResumeLink']); ?>" target="_blank" class="text-blue-500 hover:underline">View Link</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <button class="btn-action btn-view" onclick="viewMotivation('<?php echo htmlspecialchars(addslashes($row['Motivation'])); ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td style="padding: 12px;">
                                        <span class="status-badge status-<?php echo strtolower($row['Status']); ?>">
                                            <?php echo htmlspecialchars($row['Status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if ($row['Status'] === 'Pending'): ?>
                                            <button class="btn-action btn-approve" onclick="updateStatus(<?php echo $row['OpportunityID']; ?>, <?php echo $row['StudentID']; ?>, 'Approved')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn-action btn-reject" onclick="updateStatus(<?php echo $row['OpportunityID']; ?>, <?php echo $row['StudentID']; ?>, 'Rejected')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">No applications received yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function viewMotivation(motivation) {
            Swal.fire({
                title: 'Motivation Statement',
                text: motivation || 'No motivation statement provided.',
                confirmButtonText: 'Close'
            });
        }

        function updateStatus(opportunityId, studentId, status) {
            Swal.fire({
                title: 'Confirm Action',
                text: `Are you sure you want to mark this application as ${status}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'Approved' ? '#10B981' : '#EF4444',
                confirmButtonText: `Yes, ${status}`
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('process-application-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `opportunity_id=${opportunityId}&student_id=${studentId}&status=${status}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An unexpected error occurred', 'error');
                        console.error('Error:', error);
                    });
                }
            });
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
