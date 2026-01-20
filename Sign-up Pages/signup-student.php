<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-form-section">
                <div class="signup-heading">Student Registration</div>
                <form id="signupForm" class="signup-form" method="POST">
                    <div id="messageContainer"></div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName" class="form-label">First Name *</label>
                            <input 
                                type="text" 
                                id="firstName" 
                                name="firstName"
                                class="form-input" 
                                placeholder="Enter first name"
                                required
                            >
                            <span class="error-message" id="firstNameError"></span>
                        </div>

                        <div class="form-group">
                            <label for="lastName" class="form-label">Last Name *</label>
                            <input 
                                type="text" 
                                id="lastName" 
                                name="lastName"
                                class="form-input" 
                                placeholder="Enter last name"
                                required
                            >
                            <span class="error-message" id="lastNameError"></span>
                        </div>
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
                        <label for="course" class="form-label">Course *</label>
                        <input 
                            type="text" 
                            id="course" 
                            name="course"
                            class="form-input" 
                            placeholder="Enter course name"
                            required
                        >
                        <span class="error-message" id="courseError"></span>
                    </div>

                    <div class="form-group">
                        <label for="faculty" class="form-label">Faculty *</label>
                        <input 
                            type="text" 
                            id="faculty" 
                            name="faculty"
                            class="form-input" 
                            placeholder="Enter faculty name"
                            required
                        >
                        <span class="error-message" id="facultyError"></span>
                    </div>

                    <div class="form-group">
                        <label for="yearOfStudy" class="form-label">Year of Study *</label>
                        <select 
                            id="yearOfStudy" 
                            name="yearOfStudy"
                            class="form-select" 
                            required
                        >
                            <option value="">Select year of study</option>
                            <option value="1">Year 1</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                            <option value="5">Year 5</option>
                        </select>
                        <span class="error-message" id="yearOfStudyError"></span>
                    </div>

                    <hr style="margin: 16px 0; border: none; border-top: 1px solid #e5e7eb;">

                    <!--- Username and Password Fields 

                    In this field, the username(stored in the db) is the Student ID.
                    To make the system more secure and easy, no additional usernames will be required outside 
                    that of the CUEA Eco System
                    
                    -->
                    <div class="form-group">
                        <label for="username" class="form-label"> Enter Student ID*</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username"
                            class="form-input" 
                            placeholder="Enter StudentID"
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
                        Already have an account? <a href="../Login Pages/student-login.php">Sign in here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="signup-student.js"></script>
</body>
</html>
