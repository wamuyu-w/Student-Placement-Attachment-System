<?php use App\Core\Helpers; ?>


    <div class="card">
        <div class="header-actions" style="margin-bottom: 15px;">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search students..." id="searchInput">
            </div>
        </div>

        <div class="table-container">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f1f5f9;">Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f1f5f9;">Course</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f1f5f9;">Host Organization</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f1f5f9;">Status</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f1f5f9;">Assessments</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #f1f5f9;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && $students->num_rows > 0): ?>
                        <?php while($row = $students->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['Course']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['OrganizationName']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($row['AttachmentStatus']); ?></td>
                                <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($row['AssessmentCount']); ?></td>
                                <td style="padding: 12px;">
                                    <a href="<?= Helpers::baseUrl('/staff/assessment/conduct?attachment_id=' . $row['AttachmentID']) ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem; text-decoration: none;">
                                        <i class="fas fa-clipboard-check"></i> Assess
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="padding: 20px; text-align: center;">No supervised students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

