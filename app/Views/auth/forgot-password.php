<?php use App\Core\Helpers; ?>
<div class="login-container">
    <div class="login-card">
        <div class="logo-section">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" class="login-logo">
        </div>
        
        <div class="login-form-section">
            <h2 class="login-heading">Forgot Password</h2>
            
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

            <p style="text-align: center; color: #475569; margin-bottom: 24px;">Enter your email address and we'll send you a password reset link.</p>

            <form action="<?= Helpers::baseUrl('/auth/forgot-password/submit') ?>" method="POST" class="login-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                </div>
                
                <button type="submit" class="sign-in-button">Reset Password</button>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="<?= Helpers::baseUrl('/') ?>" style="color: #64748b; font-size: 0.9em; text-decoration: none;">&larr; Back to Role Selection</a>
                </div>
            </form>
        </div>
    </div>
</div>
