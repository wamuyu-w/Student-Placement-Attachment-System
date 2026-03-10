<?php use App\Core\Helpers; ?>

<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">Bulk Supervision Assignment</h1>
    <a href="<?= Helpers::baseUrl('/admin/supervisors') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Supervisors
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="alert alert-info">
        <p><i class="fas fa-info-circle"></i> 
        <strong>Smart Assignment:</strong> Select the students and a pool of lecturers. The system will randomly distribute students among the lecturers while automatically preventing any "Repeat Supervisor" conflicts (ensuring a student gets a different lecturer for their Final attachment than they had for their First).</p>
    </div>

    <form action="<?= Helpers::baseUrl('/admin/supervision/bulk/assign') ?>" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            
            <!-- Student Selection -->
            <div>
                <h3 style="margin-bottom: 1rem; border-bottom: 2px solid #eee; padding-bottom: 0.5rem; color: #8B1538;">1. Select Students (Ongoing)</h3>
                <div style="max-height: 450px; overflow-y: auto; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #ced4da; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                    <?php if (!empty($students)): ?>
                        <div style="margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                            <label style="font-weight: 700; cursor: pointer; color: #1f2937;">
                                <input type="checkbox" id="selectAllStudents" onclick="toggleAll('student_attachments[]', this.checked)"> Select All Students (<?= count($students) ?>)
                            </label>
                        </div>
                        <?php foreach ($students as $student): ?>
                            <div style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                                    <input type="checkbox" name="student_attachments[]" value="<?= $student['AttachmentID'] ?>" style="margin-top: 4px;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($student['FirstName'] . " " . $student['LastName']) ?></div>
                                        <div style="font-size: 0.85rem; color: #6b7280;">
                                            <span style="background: #eef2ff; color: #4338ca; padding: 1px 6px; border-radius: 4px; font-weight: 600; font-size: 0.75rem;"><?= htmlspecialchars($student['AdmNumber']) ?></span>
                                            <span style="margin-left: 5px;">at <?= htmlspecialchars($student['OrganizationName']) ?></span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #6b7280; padding: 40px 20px;">
                            <i class="fas fa-user-check" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                            All active students currently have supervisors assigned.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lecturer Pool Selection -->
            <div>
                <h3 style="margin-bottom: 1rem; border-bottom: 2px solid #eee; padding-bottom: 0.5rem; color: #8B1538;">2. Select Lecturer Pool</h3>
                <div style="max-height: 450px; overflow-y: auto; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #ced4da; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                    <?php if ($lecturers && $lecturers->num_rows > 0): ?>
                        <div style="margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                            <label style="font-weight: 700; cursor: pointer; color: #1f2937;">
                                <input type="checkbox" id="selectAllLecturers" onclick="toggleAll('lecturer_ids[]', this.checked)"> Select All Lecturers (<?= $lecturers->num_rows ?>)
                            </label>
                        </div>
                        <?php while ($lecturer = $lecturers->fetch_assoc()): ?>
                            <div style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                                    <input type="checkbox" name="lecturer_ids[]" value="<?= $lecturer['LecturerID'] ?>">
                                    <span style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($lecturer['Name']) ?></span>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #6b7280; padding: 40px 20px;">
                            <i class="fas fa-chalkboard-teacher" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                            No assignable lecturers found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 2.5rem; border-top: 2px dashed #e5e7eb; padding-top: 2rem; text-align: center;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.15rem; font-weight: 700; border-radius: 50px; box-shadow: 0 4px 15px rgba(139, 21, 56, 0.3);">
                <i class="fas fa-random" style="margin-right: 8px;"></i> Start Bulk Assignment
            </button>
            <p style="margin-top: 1rem; color: #6b7280; font-size: 0.9rem;">Assignments will be processed immediately upon clicking.</p>
        </div>
    </form>
</div>

<script>
function toggleAll(name, checked) {
    const checkboxes = document.getElementsByName(name);
    for (let checkbox of checkboxes) {
        checkbox.checked = checked;
    }
}
</script>

<style>
.alert-info {
    background-color: #f0f7ff;
    border-left: 4px solid #3b82f6;
    color: #1e40af;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}
.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}
.btn-secondary:hover {
    background: #e5e7eb;
}
</style>
