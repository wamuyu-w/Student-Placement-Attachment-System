// Client-side validation and form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const submitButton = document.querySelector('.btn-login') || document.querySelector('button[type="submit"]');
    
    if (!loginForm) return;

    // Real-time validation
    if (usernameInput) usernameInput.addEventListener('blur', validateUsername);
    if (passwordInput) passwordInput.addEventListener('blur', validatePassword);
    
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
        
        if (usernameError) usernameError.remove();
        return true;
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        const passwordError = document.getElementById('password-error');
        
        if (password === '') {
            showError('password', 'Password is required');
            return false;
        }
    
        if (passwordError) passwordError.remove();
        return true;
    }
    
    function showError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const errorId = fieldName + '-error';
        
        // Remove existing error
        const existingError = document.getElementById(errorId);
        if (existingError) existingError.remove();
        
        // Create error element
        const errorDiv = document.createElement('div');
        errorDiv.id = errorId;
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.color = '#ef4444';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        
        // Insert error after input
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }
    
    function clearErrors() {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
        
        const generalError = document.querySelector('.error-message-general');
        if (generalError) generalError.remove();
    }
    
    function submitLogin() {
        // Disable submit button
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Signing In...';
        
        const formData = new FormData(loginForm);
        
        // Determine user type from URL
        const path = window.location.pathname.toLowerCase();
        let userType = 'student';
        if (path.includes('staff') || path.includes('admin')) {
            userType = 'staff';
        } else if (path.includes('host')) {
            userType = 'host_org';
        }
        formData.set('user_type', userType);
        
        // Construct MVC endpoint
        let endpoint = loginForm.action;
        if (window.location.pathname.includes('/public/')) {
             const basePath = window.location.pathname.substring(0, window.location.pathname.indexOf('/public/') + 8);
             endpoint = basePath + 'auth/login';
        }
        
        fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showSuccessMessage('Login successful! Redirecting...');
                
                // Redirect
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 500);
            } else {
                showErrorMessage(data.message || 'Login failed. Please try again.');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('An error occurred. Please try again.');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    }
    
    function showSuccessMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'success-message';
        messageDiv.style.color = '#10b981';
        messageDiv.style.textAlign = 'center';
        messageDiv.style.marginBottom = '1rem';
        messageDiv.textContent = message;
        loginForm.insertBefore(messageDiv, loginForm.firstChild);
    }
    
    function showErrorMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'error-message-general';
        messageDiv.style.color = '#ef4444';
        messageDiv.style.textAlign = 'center';
        messageDiv.style.marginBottom = '1rem';
        messageDiv.textContent = message;
        loginForm.insertBefore(messageDiv, loginForm.firstChild);
    }
});
