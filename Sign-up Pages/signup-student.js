 const form = document.getElementById('signupForm');
        const messageContainer = document.getElementById('messageContainer');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear previous messages
            messageContainer.innerHTML = '';
            clearErrorMessages();

            // Get form data
            const formData = new FormData(form);
            const data = {
                firstName: formData.get('firstName').trim(),
                lastName: formData.get('lastName').trim(),
                email: formData.get('email').trim(),
                phoneNumber: formData.get('phoneNumber').trim(),
                course: formData.get('course').trim(),
                faculty: formData.get('faculty').trim(),
                yearOfStudy: formData.get('yearOfStudy').trim(),
                username: formData.get('username').trim(),
                password: formData.get('password'),
                confirmPassword: formData.get('confirmPassword')
            };

            // Client-side validation
            if (!data.firstName) {
                showError('firstNameError', 'First name is required');
                return;
            }
            if (!data.lastName) {
                showError('lastNameError', 'Last name is required');
                return;
            }
            if (!data.email) {
                showError('emailError', 'Email address is required');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
                showError('emailError', 'Please enter a valid email address');
                return;
            }
            if (!data.phoneNumber) {
                showError('phoneNumberError', 'Phone number is required');
                return;
            }
            if (!data.course) {
                showError('courseError', 'Course is required');
                return;
            }
            if (!data.faculty) {
                showError('facultyError', 'Faculty is required');
                return;
            }
            if (!data.yearOfStudy) {
                showError('yearOfStudyError', 'Year of study is required');
                return;
            }
            if (!data.username || data.username.length < 3) {
                showError('usernameError', 'Username must be at least 3 characters long');
                return;
            }
            if (!data.password || data.password.length < 6) {
                showError('passwordError', 'Password must be at least 6 characters long');
                return;
            }
            if (data.password !== data.confirmPassword) {
                showError('confirmPasswordError', 'Passwords do not match');
                return;
            }

            // Submit to server
            try {
                const response = await fetch('process-signup-student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showSuccessMessage('Sign up successful! Redirecting to login...');
                    setTimeout(() => {
                        window.location.href = '../Login Pages/student-login.php';
                    }, 2000);
                } else {
                    showErrorMessage(result.message || 'An error occurred during sign up');
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            showError(field + 'Error', result.errors[field]);
                        });
                    }
                }
            } catch (error) {
                showErrorMessage('Network error. Please try again.');
                console.error('Error:', error);
            }
        });

        function showError(fieldId, message) {
            const errorElement = document.getElementById(fieldId);
            if (errorElement) {
                errorElement.textContent = message;
                const inputId = fieldId.replace('Error', '');
                const input = document.getElementById(inputId);
                if (input) {
                    input.classList.add('error');
                }
            }
        }

        function clearErrorMessages() {
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
            });
            document.querySelectorAll('.form-input, .form-select').forEach(el => {
                el.classList.remove('error');
            });
        }

        function showErrorMessage(message) {
            messageContainer.innerHTML = `<div class="error-message-general">${message}</div>`;
        }

        function showSuccessMessage(message) {
            messageContainer.innerHTML = `<div class="success-message">${message}</div>`;
        }
