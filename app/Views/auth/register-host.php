<?php use App\Core\Helpers; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" class="logo">
            <h1>Host Organization Registration</h1>
            <p>Create an account to post opportunities and manage students.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="<?= Helpers::baseUrl('/auth/register/host') ?>" method="POST">
            <div class="form-group">
                <label for="org_name">Organization Name</label>
                <input type="text" id="org_name" name="org_name" required>
            </div>
            <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" id="contact_person" name="contact_person" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <hr style="border: 1px solid #f1f5f9; margin: 20px 0;">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <button type="submit" class="btn-auth">Register</button>
        </form>
    </div>
</div>
