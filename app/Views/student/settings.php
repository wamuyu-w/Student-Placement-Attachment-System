<?php use App\Core\Helpers; ?>

<div class="content-grid">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Profile Details</h2>
        <form action="<?= Helpers::baseUrl('/settings/update-profile') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Faculty</label>
                <input type="text" value="<?= htmlspecialchars($profile['Faculty'] ?? 'Not Set') ?>" class="form-control" readonly style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; background-color: #f3f4f6;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Department</label>
                <input type="text" value="<?= htmlspecialchars($profile['Department'] ?? 'Not Set') ?>" class="form-control" readonly style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; background-color: #f3f4f6;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profile['Email']) ?>" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Phone Number</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($profile['PhoneNumber']) ?>" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px; background-color: #8B1538; color: white; border: none; border-radius: 4px; cursor: pointer;">Update Profile</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Change Password</h2>
        <form action="<?= Helpers::baseUrl('/settings/update-password') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Current Password</label>
                <input type="password" name="current_password" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="6" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="6" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px; background-color: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">Change Password</button>
            </div>
        </form>
    </div>
</div>
