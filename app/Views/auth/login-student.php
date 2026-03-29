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
                
                <button type="submit" class="sign-in-button">Sign In</button>
            </form>
        </div>
    </div>
</div>
