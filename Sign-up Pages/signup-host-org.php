<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Organization Sign Up</title>
    <link rel="stylesheet" href="signup.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-form-section">
                <div class="signup-heading">Host Organization Registration</div>
                <form id="signupForm" class="signup-form" method="POST">
                    <div id="messageContainer"></div>

                    <div class="form-group">
                        <label for="organizationName" class="form-label">Organization Name *</label>
                        <input 
                            type="text" 
                            id="organizationName" 
                            name="organizationName"
                            class="form-input" 
                            placeholder="Enter organization name"
                            required
                        >
                        <span class="error-message" id="organizationNameError"></span>
                    </div>

                    <div class="form-group">
                        <label for="contactPerson" class="form-label">Contact Person Name *</label>
                        <input 
                            type="text" 
                            id="contactPerson" 
                            name="contactPerson"
                            class="form-input" 
                            placeholder="Enter contact person name"
                            required
                        >
                        <span class="error-message" id="contactPersonError"></span>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            class="form-input" 
                            placeholder="Enter email address"
                            required
                        >
                        <span class="error-message" id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <label for="phoneNumber" class="form-label">Phone Number *</label>
                        <input 
                            type="tel" 
                            id="phoneNumber" 
                            name="phoneNumber"
                            class="form-input" 
                            placeholder="Enter phone number"
                            required
                        >
                        <span class="error-message" id="phoneNumberError"></span>
                    </div>

                    <div class="form-group">
                        <label for="physicalAddress" class="form-label">Physical Address *</label>
                        <input 
                            type="text" 
                            id="physicalAddress" 
                            name="physicalAddress"
                            class="form-input" 
                            placeholder="Enter physical address"
                            required
                        >
                        <span class="error-message" id="physicalAddressError"></span>
                    </div>

                    <hr style="margin: 16px 0; border: none; border-top: 1px solid #e5e7eb;">

                    <div class="form-group">
                        <label for="username" class="form-label">Username *</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username"
                            class="form-input" 
                            placeholder="Choose a username"
                            required
                        >
                        <span class="error-message" id="usernameError"></span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            class="form-input" 
                            placeholder="Create a password"
                            required
                        >
                        <span class="error-message" id="passwordError"></span>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password *</label>
                        <input 
                            type="password" 
                            id="confirmPassword" 
                            name="confirmPassword"
                            class="form-input" 
                            placeholder="Confirm your password"
                            required
                        >
                        <span class="error-message" id="confirmPasswordError"></span>
                    </div>

                    <button type="submit" class="signup-button">Sign Up</button>

                    <div class="login-link">
                        Already have an account? <a href="../Login Pages/host-organization-login.php">Sign in here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--- Add JS Script at the bottom --->
    <script src="signup-hostorg.js"></script>
</body>
</html>
