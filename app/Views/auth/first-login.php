<?php use App\Core\Helpers; ?>

<div class="login-container">
    <div class="login-card" style="max-width: 600px;">
        <div class="logo-section">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" alt="CUEA Logo" class="login-logo">
        </div>
        <div class="university-info">
            <h1 class="university-name">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
            <p class="university-motto">Please complete your profile setup to continue.</p>
        </div>

        <div class="login-form-section">
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message-general">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= Helpers::baseUrl('/auth/first-login/save') ?>" method="POST" class="first-login-form">
                <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #374151;">Security</h3>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-input" required minlength="6" placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input" required minlength="6" placeholder="Confirm new password">
                </div>

                <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #374151; margin-top: 1.5rem;">Profile Details</h3>
                
                <?php if ($role === 'student'): ?>
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="firstName" class="form-input" required value="<?= htmlspecialchars($profile['FirstName'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lastName" class="form-input" required value="<?= htmlspecialchars($profile['LastName'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" required value="<?= htmlspecialchars($profile['Email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" required value="<?= htmlspecialchars($profile['PhoneNumber'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Course</label>
                        <input type="text" name="course" class="form-input" required value="<?= htmlspecialchars($profile['Course'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Faculty</label>
                        <input type="text" name="faculty" class="form-input" required value="<?= htmlspecialchars($profile['Faculty'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Year of Study</label>
                        <select name="yearOfStudy" class="form-input" required>
                            <option value="">Select Year</option>
                            <option value="1" <?= ($profile['YearOfStudy'] ?? '') == 1 ? 'selected' : '' ?>>Year 1</option>
                            <option value="2" <?= ($profile['YearOfStudy'] ?? '') == 2 ? 'selected' : '' ?>>Year 2</option>
                            <option value="3" <?= ($profile['YearOfStudy'] ?? '') == 3 ? 'selected' : '' ?>>Year 3</option>
                            <option value="4" <?= ($profile['YearOfStudy'] ?? '') == 4 ? 'selected' : '' ?>>Year 4</option>
                        </select>
                    </div>

                <?php elseif ($role === 'staff' || $role === 'admin'): ?>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" required value="<?= htmlspecialchars($profile['Name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-input" required value="<?= htmlspecialchars($profile['Department'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Faculty</label>
                        <input type="text" name="faculty" class="form-input" required value="<?= htmlspecialchars($profile['Faculty'] ?? '') ?>">
                    </div>

                <?php elseif ($role === 'host_org'): ?>
                    <div class="form-group">
                        <label class="form-label">Organization Name</label>
                        <input type="text" name="organization_name" class="form-input" required value="<?= htmlspecialchars($profile['OrganizationName'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-input" required value="<?= htmlspecialchars($profile['ContactPerson'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" required value="<?= htmlspecialchars($profile['Email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone_number" class="form-input" required value="<?= htmlspecialchars($profile['PhoneNumber'] ?? '') ?>">
                    </div>
                <?php endif; ?>

                <button type="submit" class="sign-in-button">Update & Continue</button>
            </form>
        </div>
    </div>
</div>
