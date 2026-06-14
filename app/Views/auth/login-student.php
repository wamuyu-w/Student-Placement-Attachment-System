<?php use App\Core\Helpers; ?>
<div class="login-container">
    <div class="login-card">
        <div class="logo-section">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" class="login-logo">
        </div>
        <div class="university-info">
            <h1 class="university-name">THE CATHOLIC UNIVERSITY OF EASTERN AFRICA</h1>
            <p class="university-motto">Consecrate Them in the Truth</p>
        </div>
        
        <div class="login-form-section">
            <h2 class="login-heading">Student Login</h2>
            
            <?php if (isset($_GET['success'])): ?>
    <div id="login-success-message" style="display:none;"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="error-message-general">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

            <form action="<?= Helpers::baseUrl('/auth/login') ?>" method="POST" class="login-form">
                <input type="hidden" name="role" value="student">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="form-group">
                    <label for="username" class="form-label">Registration Number</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="e.g. 1023456" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                
                <div style="text-align: right; margin-bottom: 15px;">
                    <a href="<?= Helpers::baseUrl('/auth/forgot-password') ?>" style="color: #3b82f6; font-size: 0.9em; text-decoration: none; font-weight: 500;">Forgot Password?</a>
                </div>

                <button type="submit" class="sign-in-button">Sign In</button>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var msgDiv = document.getElementById('login-success-message');
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
        requestAnimationFrame(function(){ toast.style.opacity = '1'; });
        setTimeout(function(){ toast.style.opacity = '0'; setTimeout(function(){ toast.remove(); }, 400); }, 3000);
    }
});
</script>
