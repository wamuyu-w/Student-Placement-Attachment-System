<?php use App\Core\Helpers; ?>
<div class="login-container">
    <div class="login-card">
        <div class="logo-section">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" class="login-logo">
        </div>
        
        <div class="login-form-section">
            <h2 class="login-heading">Forgot Password</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div id="success-message" style="display:none;"><?= htmlspecialchars($_GET['success']) ?></div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    var msgDiv = document.getElementById('success-message');
    if (msgDiv && msgDiv.textContent.trim()) {
        var toast = document.createElement('div');
        toast.textContent = msgDiv.textContent.trim();
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.background = 'linear-gradient(135deg, #dcfce7, #a7f3d0)';
        toast.style.color = '#166534';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '8px';
        toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        toast.style.zIndex = '1000';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.4s ease';
        document.body.appendChild(toast);
        requestAnimationFrame(function() { toast.style.opacity = '1'; });
        setTimeout(function() { toast.style.opacity = '0'; setTimeout(function() { toast.remove(); }, 400); }, 3000);
    }
});
</script>
