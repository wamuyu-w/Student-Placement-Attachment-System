<?php use App\Core\Helpers; ?>


    <div class="header-actions" style="margin-bottom: 20px; display: flex; gap: 10px; justify-content: flex-end;">
        <button onclick="document.getElementById('addStudentModal').style.display='block'" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Student
        </button>
        <button onclick="document.getElementById('bulkUploadModal').style.display='block'" class="btn btn-success">
            <i class="fas fa-file-csv"></i> Bulk Upload
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="table-container">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Registered Students</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Admission No.</th>
                    <th>Course</th>
                    <th>Faculty</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students && $students->num_rows > 0): ?>
                    <?php while($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                            <td style="padding: 12px; color: #6b7280;"><?php echo htmlspecialchars($row['AdmissionNumber']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['Faculty']); ?></td>
                            <td style="padding: 12px;">
                                <?php 
                                $status = $row['EligibilityStatus'];
                                $badgeClass = match($status) {
                                    'Eligible' => 'status-approved',
                                    'not eligible' => 'status-rejected',
                                    'Cleared' => 'status-cleared',
                                    'Attachment Ongoing' => 'status-ongoing',
                                    default => 'status-neutral'
                                };
                                echo "<span class='status-badge $badgeClass'>" . htmlspecialchars($status) . "</span>";
                                ?>
                            </td>
                            <td style="padding: 12px;">
                                <?php if ($status == 'Eligible' || $status == 'Attachment Ongoing'): ?>
                                    <form action="<?= Helpers::baseUrl('/admin/students/clear') ?>" method="POST" onsubmit="return confirm('Are you sure you want to clear this student? This will complete their attachment process.');" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <input type="hidden" name="student_id" value="<?php echo $row['StudentID']; ?>">
                                        <button type="submit" style="background-color: #10b981; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                            <i class="fas fa-check"></i> Clear
                                        </button>
                                    </form>
                                    <a href="<?= Helpers::baseUrl('/admin/students/progress?id=' . $row['StudentID']) ?>" style="background-color: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 4px; text-decoration: none; font-size: 0.85rem; display: inline-block;">
                                        Progress
                                    </a>
                                <?php elseif ($status == 'Cleared'): ?>
                                    <span style="color: #10b981;"><i class="fas fa-check-circle"></i> Done</span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="padding: 20px; text-align: center;">No students found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


<!-- Add Student Modal -->
<div id="addStudentModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 1.25rem;">Add New Student</h2>
            <span onclick="document.getElementById('addStudentModal').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
        </div>
        <form action="<?= Helpers::baseUrl('/admin/students/create') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Faculty</label>
                <select name="faculty" id="singleFaculty" onchange="updateDepartments('singleFaculty', 'singleDepartment')" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                    <option value="">-- Choose Faculty --</option>
                    <option value="Science">Science</option>
                    <option value="Law">Law</option>
                    <option value="Business">Business & Economics</option>
                    <option value="Arts & Social Sciences">Arts & Social Sciences</option>
                    <option value="Education">Education</option>
                    <option value="Theology">Theology</option>
                    <option value="Nursing">Nursing</option>
                </select>
                
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Department</label>
                <select name="department" id="singleDepartment" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                    <option value="">-- Choose Department --</option>
                </select>

                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Admission Number (Username)</label>
                <input type="text" name="admNumber" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <p style="font-size: 0.85rem; color: #6b7280; margin-top: 5px;">
                    <i class="fas fa-info-circle"></i> A default password <code>Changeme123!</code> will be generated.
                </p>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('addStudentModal').style.display='none'" style="padding: 8px 16px; margin-right: 10px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button type="submit" style="background-color: #8B1538; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Add Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div id="bulkUploadModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 1.25rem;">Bulk Upload Students</h2>
            <span onclick="document.getElementById('bulkUploadModal').style.display='none'" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
        </div>
        <form action="<?= Helpers::baseUrl('/admin/students/bulk-upload') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Faculty</label>
                <select name="faculty" id="bulkFaculty" onchange="updateDepartments('bulkFaculty', 'bulkDepartment')" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                    <option value="">-- Choose Faculty --</option>
                    <option value="Science">Science</option>
                    <option value="Law">Law</option>
                    <option value="Business">Business & Economics</option>
                    <option value="Arts & Social Sciences">Arts & Social Sciences</option>
                    <option value="Education">Education</option>
                    <option value="Theology">Theology</option>
                    <option value="Nursing">Nursing</option>
                </select>
                
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Department</label>
                <select name="department" id="bulkDepartment" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                    <option value="">-- Choose Department --</option>
                </select>

                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select CSV File</label>
                <input type="file" name="csvFile" required accept=".csv" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <div style="background-color: #f8fafc; padding: 10px; margin-top: 10px; border-radius: 4px; font-size: 0.9em; color: #64748b;">
                    <p style="margin: 0 0 5px 0;"><strong>CSV Format (Headers required):</strong></p>
                    <code style="display: block; background: #e2e8f0; padding: 5px; border-radius: 3px;">AdmissionNumber, FirstName, LastName</code>
                    <p style="margin: 5px 0 0 0;">Passwords will be set to default: <code>Changeme123!</code></p>
                </div>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('bulkUploadModal').style.display='none'" style="padding: 8px 16px; margin-right: 10px; background: #e5e7eb; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button type="submit" style="background-color: #059669; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Upload & Import</button>
            </div>
        </form>
    </div>
</div>
<script>
const facultyDepartments = {
    'Science': ['Computer Science', 'Mathematics', 'Physics', 'Biology'],
    'Law': ['Public Law', 'Private Law'],
    'Business': ['Accounting', 'Finance', 'Management'],
    'Business & Economics': ['Accounting', 'Finance', 'Management'],
    'Arts & Social Sciences': ['Sociology', 'History', 'Literature'],
    'Education': ['Early Childhood', 'Special Needs'],
    'Theology': ['Biblical Studies', 'Pastoral Theology'],
    'Nursing': ['Midwifery', 'General Nursing']
};

function updateDepartments(facultySelectId, departmentSelectId) {
    const faculty = document.getElementById(facultySelectId).value;
    const deptSelect = document.getElementById(departmentSelectId);
    
    // Clear existing
    deptSelect.innerHTML = '<option value="">-- Choose Department --</option>';
    
    if (faculty && facultyDepartments[faculty]) {
        facultyDepartments[faculty].forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            deptSelect.appendChild(option);
        });
    }
}
</script>
