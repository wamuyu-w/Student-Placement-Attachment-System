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
            <a href="#" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Applications</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Logbook</span>
            </a>
            <a href="#" class="nav-item">
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
                                <button class="btn btn-apply" onclick="openApplicationForm(<?php echo $opp['OpportunityID']; ?>)">
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

    <!-- Application Modal -->
    <div id="applicationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-contract"></i> Attachment Application Form</h2>
                <button type="button" class="modal-close" onclick="closeApplicationForm()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="applicationForm" method="POST" action="process-apply-opportunity.php" enctype="multipart/form-data">
                <input type="hidden" id="opportunityId" name="opportunity_id" value="">

                <!-- Personal Details Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user"></i>
                        <h3>Personal Details</h3>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" id="fullName" name="full_name" class="form-control" placeholder="Enter your full name" required readonly value="<?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="studentId" class="form-label">Student ID Number</label>
                            <input type="text" id="studentId" name="student_id" class="form-control" placeholder="e.g., 102XXXX" required readonly value="<?php echo htmlspecialchars($_SESSION['student_id']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">University Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="username@student.cuea.edu" required readonly value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="+254 XXX XXX XXX" required readonly value="<?php echo htmlspecialchars($_SESSION['phone']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Academic Information</h3>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="faculty" class="form-label">Faculty / Department</label>
                            <input type="text" id="faculty" name="faculty" class="form-control" placeholder="Your faculty" required readonly value="<?php echo htmlspecialchars($_SESSION['faculty']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="program" class="form-label">Program of Study</label>
                            <input type="text" id="program" name="program" class="form-control" placeholder="Your course/program" required readonly value="<?php echo htmlspecialchars($_SESSION['course']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="yearOfStudy" class="form-label">Year of Study</label>
                            <input type="text" id="yearOfStudy" name="year_of_study" class="form-control" placeholder="e.g., Year 3" required readonly value="<?php echo htmlspecialchars($_SESSION['year_of_study']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Motivation Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-lightbulb"></i>
                        <h3>Application Statement</h3>
                    </div>
                    <div class="form-group full-width">
                        <label for="motivation" class="form-label">Why are you interested in this opportunity? (Max 500 words)</label>
                        <textarea id="motivation" name="motivation" class="form-control" placeholder="Tell us why you're interested in this opportunity and how it aligns with your career goals..." rows="6" maxlength="500" required></textarea>
                        <span class="char-count"><span id="charCount">0</span>/500</span>
                    </div>
                </div>

                <!-- Supporting Documents Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-file-upload"></i>
                        <h3>Supporting Documents</h3>
                    </div>
                    <div class="form-group full-width">
                        <label for="resume" class="form-label">Upload your Resume/CV</label>
                        <div class="file-upload">
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            <div class="upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag and drop</p>
                                <span class="upload-hint">PDF, DOCX format up to 5MB</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="resume_link" class="form-label">Or provide a link to your Resume/CV (Google Drive, Dropbox, etc.)</label>
                        <input type="url" id="resume_link" name="resume_link" class="form-control" placeholder="https://your-resume-link.com">
                        <span class="upload-hint">If you provide a link, uploading a file is optional.</span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeApplicationForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-check"></i> Submit Application
                    </button>
                </div>

                <!-- Alert Messages -->
                <div id="formAlert" class="alert" style="display: none; margin-top: 16px;">
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
    </style>

    <script>
        function openApplicationForm(opportunityId) {
            document.getElementById('opportunityId').value = opportunityId;
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
                showAlert('An error occurred. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('applicationModal');
            if (event.target === modal) {
                closeApplicationForm();
            }
        });

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
