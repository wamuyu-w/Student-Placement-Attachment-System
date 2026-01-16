// Client-side validation and form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const submitButton = document.querySelector('.sign-in-button');
    
    // Determine user type from current page
    const currentPage = window.location.pathname;
    let userType = '';
    let loginEndpoint = '';
    
    if (currentPage.includes('student-login')) {
        userType = 'student';
        loginEndpoint = 'login-student.php';
    } else if (currentPage.includes('staff-login')) {
        userType = 'staff';
        loginEndpoint = 'login-staff.php';
    } else if (currentPage.includes('host-organization-login')) {
        userType = 'host_org';
        loginEndpoint = 'login-host-org.php';
    }
    
    // Real-time validation
    usernameInput.addEventListener('blur', validateUsername);
    passwordInput.addEventListener('blur', validatePassword);
    
    // Form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        clearErrors();
        
        // Validate all fields
        const isUsernameValid = validateUsername();
        const isPasswordValid = validatePassword();
        
        if (isUsernameValid && isPasswordValid) {
            submitLogin();
        }
    });
    
    function validateUsername() {
        const username = usernameInput.value.trim();
        const usernameError = document.getElementById('username-error');
        
        if (username === '') {
            showError('username', 'Username is required');
            return false;
        }
        
        // Remove error if valid
        if (usernameError) {
            usernameError.remove();
        }
        
        return true;
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        const passwordError = document.getElementById('password-error');
        
        if (password === '') {
            showError('password', 'Password is required');
            return false;
        }
        
        if (password.length < 6) {
            showError('password', 'Password must be at least 6 characters');
            return false;
        }
        
        // Remove error if valid
        if (passwordError) {
            passwordError.remove();
        }
        
        return true;
    }
    
    function showError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const errorId = fieldName + '-error';
        
        // Remove existing error
        const existingError = document.getElementById(errorId);
        if (existingError) {
            existingError.remove();
        }
        
        // Create error element
        const errorDiv = document.createElement('div');
        errorDiv.id = errorId;
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        // Add error styling to input
        field.classList.add('error');
        
        // Insert error after input
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }
    
    function clearErrors() {
        // Remove all error messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
        
        // Remove error styling from inputs
        usernameInput.classList.remove('error');
        passwordInput.classList.remove('error');
    }
    
    function submitLogin() {
        // Disable submit button
        submitButton.disabled = true;
        submitButton.textContent = 'Signing In...';
        
        // Create form data
        const formData = new FormData();
        formData.append('username', usernameInput.value.trim());
        formData.append('password', passwordInput.value);
        
        // Send AJAX request
        fetch(loginEndpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showSuccessMessage(data.message);
                
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                // Show error message
                showErrorMessage(data.message || 'Login failed. Please try again.');
                
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Sign In';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('An error occurred. Please try again.');
            
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = 'Sign In';
        });
    }
    
    function showSuccessMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'success-message';
        messageDiv.textContent = message;
        loginForm.insertBefore(messageDiv, loginForm.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }
    
    function showErrorMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'error-message-general';
        messageDiv.textContent = message;
        loginForm.insertBefore(messageDiv, loginForm.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
});
