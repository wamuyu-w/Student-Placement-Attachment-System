<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <div class="table-container mb-4">
        <h2 class="section-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); padding: 0 1rem;">Program Applications (Attachment Clearance)</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Intended Host</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($programApplications && $programApplications->num_rows > 0): ?>
                    <?php while($row = $programApplications->fetch_assoc()): ?>
                        <?php 
                        $statusClass = '';
                        $status = strtolower($row['ApplicationStatus']);
                        if (strpos($status, 'pending') !== false) $statusClass = 'status-pending';
                        elseif (strpos($status, 'approve') !== false) $statusClass = 'status-approved';
                        elseif (strpos($status, 'reject') !== false) $statusClass = 'status-rejected';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['ApplicationDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                            <td><?php echo htmlspecialchars($row['OrganizationName'] ?? 'Not Specified'); ?></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['ApplicationStatus']); ?></span>
                            </td>
                            <td>
                                <?php if ($row['ApplicationStatus'] == 'Pending'): ?>
                                <form action="<?= Helpers::baseUrl('/admin/applications/program-status') ?>" method="POST" class="status-form">
                                    <input type="hidden" name="application_id" value="<?php echo $row['ApplicationID']; ?>">
                                    <div style="display: flex; gap: 8px;">
                                        <button type="submit" name="status" value="Approved" title="Approve" style="background: none; border: none; cursor: pointer; color: var(--success-color);">
                                            <i class="fas fa-check-circle fa-lg"></i>
                                        </button>
                                        <button type="submit" name="status" value="Rejected" title="Reject" style="background: none; border: none; cursor: pointer; color: var(--danger-color);">
                                            <i class="fas fa-times-circle fa-lg"></i>
                                        </button>
                                    </div>
                                </form>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No program applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h2 class="section-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); padding: 0 1rem; margin-top: 2rem;">Job Applications (Specific Opportunities)</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Opportunity</th>
                    <th>Host Org</th>
                    <th>Status / Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jobApplications && $jobApplications->num_rows > 0): ?>
                    <?php while($row = $jobApplications->fetch_assoc()): ?>
                        <?php 
                        $statusClass = '';
                        $status = strtolower($row['Status']);
                        if (strpos($status, 'pending') !== false) $statusClass = 'status-pending';
                        elseif (strpos($status, 'approve') !== false) $statusClass = 'status-approved';
                        elseif (strpos($status, 'reject') !== false) $statusClass = 'status-rejected';
                        elseif (strpos($status, 'accept') !== false) $statusClass = 'status-approved';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['ApplicationDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                            <td><?php echo htmlspecialchars($row['Description']); ?></td>
                            <td><?php echo htmlspecialchars($row['OrganizationName']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['Status']); ?></span>
                                
                                <form action="<?= Helpers::baseUrl('/admin/applications/job-status') ?>" method="POST" class="status-form">
                                    <input type="hidden" name="opportunity_id" value="<?php echo $row['OpportunityID']; ?>">
                                    <input type="hidden" name="student_id" value="<?php echo $row['StudentID']; ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="" disabled selected>Update</option>
                                        <option value="Approved">Approve</option>
                                        <option value="Rejected">Reject</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
