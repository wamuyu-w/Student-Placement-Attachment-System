<?php use App\Core\Helpers; ?>


    <div class="header-actions" style="margin-bottom: 20px; display: flex; gap: 10px; justify-content: flex-end;">
        <button onclick="document.getElementById('addSupervisorModal').style.display='block'" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Supervisor
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

    <!-- Assignment Form -->
    <div class="card mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Assign Supervisor to Student</h2>
        <form action="<?= Helpers::baseUrl('/admin/supervisors/assign') ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div>
                <label class="form-label">Select Student (Ongoing Attachment)</label>
                <select name="attachment_id" required class="form-control">
                    <option value="">-- Choose Student --</option>
                    <?php if ($assignableStudents && $assignableStudents->num_rows > 0): ?>
                        <?php while($row = $assignableStudents->fetch_assoc()): ?>
                            <?php $label = ($row['SupCount'] == 0) ? " (Needs 1st Supervisor)" : " (Needs 2nd Supervisor)"; ?>
                            <option value="<?= $row['AttachmentID'] ?>"><?= htmlspecialchars($row['FirstName'] . " " . $row['LastName'] . " - " . $row['OrganizationName'] . $label) ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>No students pending assignment</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="form-label">Select Supervisor</label>
                <select name="lecturer_id" required class="form-control">
                    <option value="">-- Choose Lecturer --</option>
                    <?php if ($assignableLecturers && $assignableLecturers->num_rows > 0): ?>
                        <?php while($row = $assignableLecturers->fetch_assoc()): ?>
                            <option value="<?= $row['LecturerID'] ?>"><?= htmlspecialchars($row['Name']) ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Assign</button>
        </form>
    </div>

    <!-- Supervisor List -->
    <div class="table-container">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Supervisor List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Faculty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($supervisors && $supervisors->num_rows > 0): ?>
                    <?php while($row = $supervisors->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Name']) ?></td>
                            <td><?= htmlspecialchars($row['Department']) ?></td>
                            <td><?= htmlspecialchars($row['Faculty']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($row['Status']) === 'active' ? 'approved' : 'rejected' ?>">
                                    <?= htmlspecialchars($row['Status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">No supervisors found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


<!-- Add Supervisor Modal -->
<div id="addSupervisorModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="modal-header">
            <h2 style="margin: 0;">Add New Supervisor</h2>
            <span onclick="document.getElementById('addSupervisorModal').style.display='none'" class="modal-close" style="cursor: pointer; float: right; font-size: 1.5rem;">&times;</span>
        </div>
        <form action="<?= Helpers::baseUrl('/admin/supervisors/create') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="form-group">
                <label>Staff Number</label>
                <input type="text" name="staffNumber" required class="form-control">
                <p class="form-hint"><i class="fas fa-info-circle"></i> A username and default password (<code>Changeme123!</code>) will be generated.</p>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addSupervisorModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Supervisor</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div id="bulkUploadModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="modal-header">
            <h2 style="margin: 0;">Bulk Upload Supervisors</h2>
            <span onclick="document.getElementById('bulkUploadModal').style.display='none'" class="modal-close" style="cursor: pointer; float: right; font-size: 1.5rem;">&times;</span>
        </div>
        <form action="<?= Helpers::baseUrl('/admin/supervisors/bulk-upload') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="form-group">
                <label>Select Faculty</label>
                <select name="faculty" required class="form-control">
                    <option value="">-- Choose Faculty --</option>
                    <option value="Science">Science</option>
                    <option value="Law">Law</option>
                    <option value="Business">Business & Economics</option>
                    <option value="Arts & Social Sciences">Arts & Social Sciences</option>
                    <option value="Education">Education</option>
                    <option value="Theology">Theology</option>
                    <option value="Nursing">Nursing</option>
                </select>
            </div>
            <div class="form-group">
                <label>Select CSV File</label>
                <input type="file" name="csvFile" required accept=".csv" class="form-control">
                <div class="form-hint" style="background: #f8fafc; padding: 10px; margin-top: 10px;">
                    <p style="margin: 0 0 5px 0;"><strong>CSV Format (Headers required):</strong></p>
                    <code>StaffNumber, Name, Department</code>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('bulkUploadModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-success">Upload & Import</button>
            </div>
        </form>
    </div>
</div>
