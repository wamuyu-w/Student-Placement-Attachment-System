<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
    <title>Student Login - CUEA</title>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <img src="../assets/cuea-logo.png" alt="CUEA Logo" class="university-logo">
            </div>
            <div class="university-info">
                <h1 class="university-name">THE CATHOLIC UNIVERSITY OF EASTERN AFRICA</h1>
                <p class="university-motto">Consecrate Them in the Truth</p>
            </div>
    <div class="login-form-section">
                <h2 class="login-heading">Student Login</h2>
                <?php
                if (isset($_GET['message'])) {
                    $msgCode = $_GET['message'];
                    $displayMsg = htmlspecialchars($msgCode);
                    if ($msgCode === 'Main_Sucessfully_Submitted_Account_Inactive') {
                        $displayMsg = "Final Report Submitted. Your attachment is now complete and your account has been deactivated.";
                    } elseif ($msgCode === 'account_deactivated') {
                        $displayMsg = "Your account is inactive. Please contact administration if this is an error.";
                    }
                    echo '<div class="success-message">' . $displayMsg . '</div>';
                }
                if (isset($_GET['error'])) {
                    echo '<div class="error-message-general">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                ?>
                <form class="login-form" id="loginForm" method="POST">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
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
    <script src="login.js"></script>
</body>
</html>
