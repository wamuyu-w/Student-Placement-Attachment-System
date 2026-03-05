<?php use App\Core\Helpers; ?>


    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Academic Supervisor Details</h2>
        
        <?php if ($supervisor): ?>
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #9ca3af;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h3 style="margin: 0; color: #111827; font-size: 1.2rem;"><?= htmlspecialchars($supervisor['Name']) ?></h3>
                    <p style="margin: 5px 0 0; color: #6b7280;"><?= htmlspecialchars($supervisor['Department']) ?></p>
                    <p style="margin: 2px 0 0; color: #6b7280; font-size: 0.9rem;"><?= htmlspecialchars($supervisor['Faculty']) ?></p>
                </div>
            </div>
            
            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #6b7280; margin-bottom: 5px;">ASSIGNED DATE</label>
                        <div style="color: #111827;"><?= date('F j, Y', strtotime($supervisor['AssignedDate'])) ?></div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #6b7280; margin-bottom: 5px;">STATUS</label>
                        <span class="status-badge status-active">Active</span>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 30px; background: #f9fafb; padding: 20px; border-radius: 8px;">
                <h4 style="margin: 0 0 10px; font-size: 1rem; color: #374151;">Contact Information</h4>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 10px;">
                    Please contact your supervisor for any academic guidance regarding your attachment.
                </p>
                <button class="btn btn-primary" onclick="alert('Messaging feature coming soon!')">
                    <i class="fas fa-envelope"></i> Send Message
                </button>
            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-user-slash" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p>You have not been assigned an academic supervisor yet.</p>
                <p style="font-size: 0.9rem;">This usually happens once your placement is registered and approved.</p>
            </div>
        <?php endif; ?>
    </div>

