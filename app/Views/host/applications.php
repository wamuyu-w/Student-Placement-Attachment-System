<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div class="card">
        <?php if ($applications && $applications->num_rows > 0): ?>
            <div class="table-container" style="box-shadow: none; padding: 0;">
            <table>
                <thead>
                    <tr>
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
                    <?php while ($row = $applications->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['ApplicationDate']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['Description']); ?></td>
                            <td style="padding: 12px;">
                                <?php if ($row['ResumePath']): ?>
                                    <a href="<?= Helpers::baseUrl('../assets/uploads/resumes/' . htmlspecialchars($row['ResumePath'])) ?>" target="_blank" class="text-blue-500 hover:underline">View PDF</a>
                                <?php elseif ($row['ResumeLink']): ?>
                                    <a href="<?php echo htmlspecialchars($row['ResumeLink']); ?>" target="_blank" class="text-blue-500 hover:underline">View Link</a>
                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px;">
                                <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8em;" onclick="viewMotivation('<?php echo htmlspecialchars(addslashes($row['Motivation'])); ?>')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td style="padding: 12px;">
                                <span class="status-badge status-<?php echo strtolower($row['Status'] ?? 'pending'); ?>">
                                    <?php echo htmlspecialchars($row['Status']); ?>
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <?php if ($row['Status'] === 'Pending'): ?>
                                    <button class="btn btn-success" style="padding: 4px 8px; font-size: 0.8em;" onclick="updateStatus(<?php echo $row['OpportunityID']; ?>, <?php echo $row['StudentID']; ?>, 'Approved')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-primary" style="padding: 4px 8px; font-size: 0.8em; background-color: #ef4444; border-color: #ef4444;" onclick="updateStatus(<?php echo $row['OpportunityID']; ?>, <?php echo $row['StudentID']; ?>, 'Rejected')">
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
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center py-4">No applications received yet.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                const formData = new FormData();
                formData.append('opportunity_id', opportunityId);
                formData.append('student_id', studentId);
                formData.append('status', status);
                formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('<?= Helpers::baseUrl('/host/applications/update-status') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => Swal.fire('Error', 'An unexpected error occurred', 'error'));
            }
        });
    }
</script>
