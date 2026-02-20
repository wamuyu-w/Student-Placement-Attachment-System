<?php
require_once '../config.php';
requireLogin('student');

$conn = getDBConnection();
$studentId = $_SESSION['student_id'] ?? null;

if (!$studentId) {
    header("Location: ../Login Pages/login-student.php");
    exit();
}

// Get available opportunities
$oppStmt = $conn->prepare("
    SELECT 
        OpportunityID,
        Description,
        EligibilityCriteria,
        ApplicationStartDate,
        ApplicationEndDate,
        Status,
        ho.OrganizationName
    FROM attachmentopportunity ao
    INNER JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
    WHERE ao.Status = 'Active' 
    AND NOW() BETWEEN ao.ApplicationStartDate AND ao.ApplicationEndDate
    AND NOT EXISTS (
        SELECT 1 FROM attachment 
        WHERE StudentID = ? AND AttachmentStatus = 'Active'
    )
    ORDER BY ao.ApplicationEndDate ASC
");
$oppStmt->bind_param("i", $studentId);
$oppStmt->execute();
$opportunities = $oppStmt->get_result();
$oppStmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Opportunities - CUEA</title>
    <link rel="stylesheet" href="../Dashboards/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../assets/cuea-logo.png" height="20px" width="20px" alt="cuea university logo" srcset="">
            </div>
            <h1>CUEA Attachment System</h1>
        </div>
        
        <nav class="sidebar-nav">
            <a href="../Dashboards/student-dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="student-opportunities.php" class="nav-item active">
                <i class="fas fa-briefcase"></i>
                <span>Opportunities</span>
            </a>
            <a href="../Applications/student-applications.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>My Applications</span>
            </a>
            <a href="../Logbook/student-logbook.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="../Reports/student-reports.php" class="nav-item">
                <i class="fas fa-file-pdf"></i>
                <span>Reports</span>
            </a>
            <a href="../Supervisor/student-supervisor.php" class="nav-item">
                <i class="fas fa-user-tie"></i>
                <span>Supervisor</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../Settings/student-settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="../Login Pages/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <h1 class="page-title">Available Opportunities</h1>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search opportunities..." id="searchInput">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile">
                    <div class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; background: #8B1538; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($_SESSION['first_name'][0] ?? 'S', 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
                        <div class="profile-role">Student</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Opportunities Grid -->
        <div class="opportunities-section">
            <div class="section-header">
                <h2><i class="fas fa-briefcase"></i> Active Opportunities</h2>
                <p class="section-subtitle">Browse and apply for attachment opportunities</p>
            </div>

            <?php if ($opportunities->num_rows > 0): ?>
                <div class="opportunities-grid">
                    <?php while ($opp = $opportunities->fetch_assoc()): ?>
                        <div class="opportunity-card">
                            <div class="opportunity-header">
                                <div class="org-badge">
                                    <i class="fas fa-building"></i>
                                    <?php echo htmlspecialchars(substr($opp['OrganizationName'], 0, 3)); ?>
                                </div>
                                <span class="deadline-badge">
                                    Closes: <?php echo date('M d', strtotime($opp['ApplicationEndDate'])); ?>
                                </span>
                            </div>
                            <div class="opportunity-content">
                                <h3><?php echo htmlspecialchars(substr($opp['Description'], 0, 80)); ?></h3>
                                <p class="organization"><?php echo htmlspecialchars($opp['OrganizationName']); ?></p>
                                <p class="description"><?php echo htmlspecialchars(substr($opp['Description'], 0, 120)); ?>...</p>
                                
                                <div class="opportunity-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Eligibility Criteria</span>
                                    </div>
                                    <p class="meta-text"><?php echo htmlspecialchars(substr($opp['EligibilityCriteria'], 0, 100)); ?>...</p>
                                </div>
                            </div>
                            <div class="opportunity-footer">
                                <button class="btn btn-view" 
                                        data-org="<?php echo htmlspecialchars($opp['OrganizationName']); ?>"
                                        data-desc="<?php echo htmlspecialchars($opp['Description']); ?>"
                                        data-crit="<?php echo htmlspecialchars($opp['EligibilityCriteria']); ?>"
                                        data-deadline="<?php echo date('M d, Y', strtotime($opp['ApplicationEndDate'])); ?>"
                                        data-id="<?php echo $opp['OpportunityID']; ?>"
                                        onclick="handleViewDetails(this)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="btn btn-apply-card" 
                                        data-id="<?php echo $opp['OpportunityID']; ?>"
                                        data-org="<?php echo htmlspecialchars($opp['OrganizationName']); ?>"
                                        data-desc="<?php echo htmlspecialchars($opp['Description']); ?>"
                                        onclick="handleApplyForm(this)">
                                    <i class="fas fa-arrow-right"></i> Apply Now
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Active Opportunities</h3>
                    <p>There are currently no active opportunities available. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Opportunity Details</h2>
                <button type="button" class="modal-close" onclick="closeDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-content">
                <div class="detail-header">
                    <h3 id="detailsRole" class="detail-title"></h3>
                    <div class="detail-org">
                        <i class="fas fa-building"></i>
                        <span id="detailsOrg"></span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label"><i class="fas fa-align-left"></i> Description</div>
                    <p id="detailsDesc" class="detail-text"></p>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label"><i class="fas fa-graduation-cap"></i> Eligibility Criteria</div>
                    <p id="detailsCriteria" class="detail-text"></p>
                </div>

                <div class="detail-section">
                    <div class="detail-label"><i class="fas fa-clock"></i> Application Deadline</div>
                    <p id="detailsDeadline" class="detail-text" style="font-weight: 600; color: #b91c1c;"></p>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button class="btn btn-view" onclick="closeDetailsModal()">Close</button>
                    <button class="btn btn-apply-card" id="detailsApplyBtn">Apply Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-contract"></i> Apply for Attachment</h2>
                <button type="button" class="modal-close" onclick="closeApplicationForm()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="application-summary-header">
                <div class="app-org-name" id="modalOrg"></div>
                <div class="app-role-title" id="modalRole"></div>
            </div>

            <form id="applicationForm" method="POST" action="process-apply-opportunity.php" enctype="multipart/form-data">
                <input type="hidden" id="opportunityId" name="opportunity_id" value="">
                
                <!-- Personal & Academic Details (Read-only Summary) -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        <h3>Applicant Details</h3>
                    </div>
                    
                    <div class="student-summary-card">
                        <div class="summary-row">
                            <div class="summary-item">
                                <span class="label">Full Name</span>
                                <span class="value"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Admission Number</span>
                                <span class="value"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-item">
                                <span class="label">Email</span>
                                <span class="value"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Phone</span>
                                <span class="value"><?php echo htmlspecialchars($_SESSION['phone']); ?></span>
                            </div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-item">
                                <span class="label">Program</span>
                                <span class="value"><?php echo htmlspecialchars($_SESSION['course']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Year of Study</span>
                                <span class="value"><?php echo htmlspecialchars($_SESSION['year_of_study']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs to submit strict data if needed, though mostly backend should rely on session/db -->
                    <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($_SESSION['student_id']); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
                    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone']); ?>">
                    <input type="hidden" name="faculty" value="<?php echo htmlspecialchars($_SESSION['faculty']); ?>">
                    <input type="hidden" name="program" value="<?php echo htmlspecialchars($_SESSION['course']); ?>">
                    <input type="hidden" name="year_of_study" value="<?php echo htmlspecialchars($_SESSION['year_of_study']); ?>">
                </div>

                <!-- Motivation Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-pen-fancy"></i>
                        <h3>Application Statement</h3>
                    </div>
                    <div class="form-group full-width">
                        <label for="motivation" class="form-label">Why are you interested in this opportunity? <span style="color: #6b7280; font-weight: 400; font-size: 0.85em;">(Max 500 words)</span></label>
                        <textarea id="motivation" name="motivation" class="form-control" placeholder="Explain your interest and how this attachment aligns with your career goals..." rows="6" maxlength="2500" required></textarea>
                        <span class="char-count"><span id="charCount">0</span> characters</span>
                    </div>
                </div>

                <!-- Supporting Documents Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-paperclip"></i>
                        <h3>Supporting Documents</h3>
                    </div>
                    <div class="form-group full-width">
                        <label for="resume" class="form-label">Upload Resume/CV</label>
                        <div class="file-upload">
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            <div class="upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div>
                                    <p style="margin-bottom: 4px; font-weight: 500;">Click to upload or drag & drop</p>
                                    <span class="upload-hint">PDF or DOCX (Max 5MB)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group full-width" style="margin-top: 16px;">
                        <label for="resume_link" class="form-label">Or provide a Link (Optional)</label>
                        <div class="input-with-icon">
                            <i class="fas fa-link"></i>
                            <input type="url" id="resume_link" name="resume_link" class="form-control" placeholder="https://drive.google.com/..." style="padding-left: 36px;">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeApplicationForm()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Application</button>
                </div>

                <!-- Alert Messages -->
                <div id="formAlert" class="alert" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    <span id="alertMessage"></span>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Styles -->
    <style>
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert i {
            font-size: 16px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            border: 1px solid #888;
            width: 90%; 
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Improved Modal Styling */
        .modal-body-content {
            padding: 24px;
        }

        .detail-header {
            margin-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
        }

        .detail-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #8B1538;
            margin-bottom: 8px;
        }

        .detail-org {
            font-size: 1.1rem;
            color: #4b5563;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-section {
            margin-bottom: 24px;
            background-color: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #f3f4f6;
        }

        .detail-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-text {
            color: #1f2937;
            line-height: 1.6;
            font-size: 1rem;
        }

        .opportunity-footer {
            display: flex;
            gap: 12px;
            padding: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .btn-view {
            background-color: #f3f4f6;
            color: #1f2937;
            flex: 1;
        }

        .btn-view:hover {
            background-color: #e5e7eb;
        }

        .btn-apply-card {
            background-color: #8B1538;
            color: white;
            flex: 1;
        }

        .btn-apply-card:hover {
            background-color: #70102d;
        }

        /* Application Modal Improvements */
        .application-summary-header {
            background-color: #f8fafc;
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .app-org-name {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .app-role-title {
            font-size: 1.25rem;
            color: #8B1538;
            font-weight: 700;
            line-height: 1.4;
        }

        .student-summary-card {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }

        .summary-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-item .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
            font-weight: 600;
        }

        .summary-item .value {
            font-size: 0.95rem;
            color: #334155;
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-secondary {
            background-color: white;
            border: 1px solid #cbd5e1;
            color: #475569;
        }
        
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        .btn-primary {
            background-color: #8B1538;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #70102d;
        }

        /* File Upload Styling */
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        .upload-area:hover {
            border-color: #8B1538;
            background-color: #fff1f2;
        }
        
        .upload-area i {
            font-size: 32px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .upload-hint {
            font-size: 0.85rem;
            color: #64748b;
            display: block;
        }
        
        .file-upload input[type="file"] {
            display: none;
        }

        /* Focus states */
        .form-control:focus {
            outline: none;
            border-color: #8B1538;
            box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1);
        }
    </style>

    <script>
        function handleViewDetails(btn) {
            const orgName = btn.getAttribute('data-org');
            const description = btn.getAttribute('data-desc');
            const criteria = btn.getAttribute('data-crit');
            const deadline = btn.getAttribute('data-deadline');
            const opportunityId = btn.getAttribute('data-id');
            openDetailsModal(orgName, description, criteria, deadline, opportunityId);
        }

        function handleApplyForm(btn) {
            const opportunityId = btn.getAttribute('data-id');
            const orgName = btn.getAttribute('data-org');
            const description = btn.getAttribute('data-desc');
            openApplicationForm(opportunityId, orgName, description);
        }

        function openDetailsModal(orgName, description, criteria, deadline, opportunityId) {
            document.getElementById('detailsOrg').textContent = orgName;
            document.getElementById('detailsRole').textContent = description; 
            document.getElementById('detailsDesc').textContent = description; // In real app, might separate Title from Desc
            document.getElementById('detailsCriteria').textContent = criteria;
            document.getElementById('detailsDeadline').textContent = deadline;
            
            const applyBtn = document.getElementById('detailsApplyBtn');
            applyBtn.onclick = function() {
                closeDetailsModal();
                openApplicationForm(opportunityId, orgName, description);
            };
            
            document.getElementById('detailsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openApplicationForm(opportunityId, orgName, description) {
            document.getElementById('opportunityId').value = opportunityId;
            document.getElementById('modalOrg').textContent = orgName;
            document.getElementById('modalRole').textContent = description;
            
            document.getElementById('applicationForm').reset();
            document.getElementById('charCount').textContent = '0';
            document.getElementById('formAlert').style.display = 'none';
            document.getElementById('applicationModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeApplicationForm() {
            document.getElementById('applicationModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function showAlert(message, type = 'success') {
            const alertDiv = document.getElementById('formAlert');
            const alertMessage = document.getElementById('alertMessage');
            
            alertDiv.className = 'alert ' + type;
            alertMessage.textContent = message;
            alertDiv.style.display = 'flex';
        }

        // Character count for motivation
        document.getElementById('motivation').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Form submission
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            const formData = new FormData(this);
            
            fetch('process-apply-opportunity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        closeApplicationForm();
                        location.reload();
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const appModal = document.getElementById('applicationModal');
            const detailsModal = document.getElementById('detailsModal');
            if (event.target === appModal) {
                closeApplicationForm();
            }
            if (event.target === detailsModal) {
                closeDetailsModal();
            }
        };

        // Drag and drop for file upload
        const fileUpload = document.getElementById('resume');
        const uploadArea = fileUpload.parentElement.querySelector('.upload-area');

        uploadArea.addEventListener('click', () => fileUpload.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.backgroundColor = '#f0f0f0';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.backgroundColor = '';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUpload.files = e.dataTransfer.files;
            uploadArea.style.backgroundColor = '';
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.opportunity-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
