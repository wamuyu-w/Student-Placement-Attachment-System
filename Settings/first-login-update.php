<?php
require_once '../config.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

$userType = $_SESSION['user_type'] ?? '';
$isStudent = ($userType === 'student');
$username = $_SESSION['username'] ?? 'User';

// We might want to pre-fill mostly empty fields if they exist (unlikely for new student, but good for robustness)
// Since we don't have them in session yet (because they were null in DB when logging in), we start fresh.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Required - Update Profile</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .update-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 600px;
        }
        .update-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .update-header h1 {
            color: #8B1538;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .update-header p {
            color: #6b7280;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group.full-width {
            grid-column: span 2;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #8B1538;
            box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1);
        }
        .btn-update {
            width: 100%;
            padding: 0.75rem;
            background-color: #8B1538;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-update:hover {
            background-color: #70102d;
        }
    </style>
</head>
<body>
    <div class="update-card">
        <div class="update-header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
            <p>Please complete your profile setup to continue.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; border: 1px solid #fecaca; font-size: 0.9rem;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="process-first-login-update.php" method="POST">
            <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #374151;">Security</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required minlength="6" placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required minlength="6" placeholder="Confirm new password">
                </div>
            </div>

            <?php if ($isStudent): ?>
            <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #374151; margin-top: 1.5rem;">Profile Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstName" required placeholder="e.g. John">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastName" required placeholder="e.g. Doe">
                </div>
                <div class="form-group full-width">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="e.g. john.doe@student.cuea.edu">
                </div>
                <div class="form-group full-width">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" required placeholder="e.g. 0712345678">
                </div>
                <div class="form-group full-width">
                    <label>Course</label>
                    <input type="text" name="course" required placeholder="e.g. Bachelor of Science in Computer Science">
                </div>
                <div class="form-group">
                    <label>Faculty</label>
                    <input type="text" name="faculty" required placeholder="e.g. Science & Technology">
                </div>
                <div class="form-group">
                    <label>Year of Study</label>
                    <select name="yearOfStudy" required>
                        <option value="">Select Year</option>
                        <option value="1">Year 1</option>
                        <option value="2">Year 2</option>
                        <option value="3">Year 3</option>
                        <option value="4">Year 4</option>
                    </select>
                </div>
            </div>
            <?php elseif ($userType === 'staff' || $userType === 'admin'): ?>
            <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #374151; margin-top: 1.5rem;">Profile Details</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="e.g. Dr. Jane Smith" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" required placeholder="e.g. Computer Science" value="<?php echo htmlspecialchars($_SESSION['department'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Faculty</label>
                    <input type="text" name="faculty" required placeholder="e.g. Science" value="<?php echo htmlspecialchars($_SESSION['faculty'] ?? ''); ?>">
                </div>
            </div>
            <?php elseif ($userType === 'host_org'): ?>
            <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #374151; margin-top: 1.5rem;">Organization Details</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Organization Name</label>
                    <input type="text" name="organization_name" required value="<?php echo htmlspecialchars($_SESSION['organization_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" required value="<?php echo htmlspecialchars($_SESSION['contact_person'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone_number" required placeholder="e.g. 0712345678" value="<?php echo htmlspecialchars($_SESSION['phone_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Physical Address</label>
                    <input type="text" name="physical_address" required placeholder="e.g. Waiyaki Way, Nairobi" value="<?php echo htmlspecialchars($_SESSION['physical_address'] ?? ''); ?>">
                </div>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn-update">Update & Continue</button>
        </form>
    </div>
</body>
</html>
