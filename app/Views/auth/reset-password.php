<?php use App\Core\Helpers; ?>
<div class="login-container">
    <div class="login-card">
        <div class="logo-section">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" class="login-logo">
        </div>
        
        <div class="login-form-section">
            <h2 class="login-heading">Set New Password</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" style="margin-bottom: 20px; padding: 10px; background-color: #dcfce7; color: #166534; border-radius: 4px;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message-general" style="margin-bottom: 20px; padding: 10px; background-color: #fee2e2; color: #991b1b; border-radius: 4px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <p style="text-align: center; color: #475569; margin-bottom: 24px;">Please enter your new password below.</p>

            <form action="<?= Helpers::baseUrl('/auth/reset-password/submit') ?>" method="POST" class="login-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Enter new password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirm new password" required minlength="6">
                </div>
                
                <button type="submit" class="sign-in-button">Reset Password</button>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="<?= Helpers::baseUrl('/') ?>" style="color: #64748b; font-size: 0.9em; text-decoration: none;">&larr; Back to Role Selection</a>
                </div>
            </form>
        </div>
    </div>
</div>
