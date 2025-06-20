document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.querySelector('.password-toggle');
    const loginBtn = document.querySelector('.btn-login');
    const rememberCheckbox = document.getElementById('remember');

    // Password toggle functionality
    if (passwordToggle) {
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            if (type === 'password') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Form validation
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePassword(password) {
        return password.length >= 6;
    }

    function showError(input, message) {
        removeError(input);

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;

        input.parentNode.appendChild(errorDiv);
        input.style.borderColor = '#dc3545';
    }

    function removeError(input) {
        const errorMessage = input.parentNode.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        input.style.borderColor = '#e1e5e9';
    }

    function showSuccess(input) {
        removeError(input);
        input.style.borderColor = '#28a745';
    }

    // Real-time validation
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (!email) {
            showError(this, 'Email là bắt buộc');
        } else if (!validateEmail(email)) {
            showError(this, 'Email không hợp lệ');
        } else {
            showSuccess(this);
        }
    });

    emailInput.addEventListener('input', function() {
        if (this.value.trim()) {
            removeError(this);
        }
    });

    passwordInput.addEventListener('blur', function() {
        const password = this.value;
        if (!password) {
            showError(this, 'Mật khẩu là bắt buộc');
        } else if (!validatePassword(password)) {
            showError(this, 'Mật khẩu phải có ít nhất 6 ký tự');
        } else {
            showSuccess(this);
        }
    });

    passwordInput.addEventListener('input', function() {
        if (this.value) {
            removeError(this);
        }
    });

    // Form submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = emailInput.value.trim();
            const password = passwordInput.value;
            let isValid = true;

            // Validate email
            if (!email) {
                showError(emailInput, 'Email là bắt buộc');
                isValid = false;
            } else if (!validateEmail(email)) {
                showError(emailInput, 'Email không hợp lệ');
                isValid = false;
            }

            // Validate password
            if (!password) {
                showError(passwordInput, 'Mật khẩu là bắt buộc');
                isValid = false;
            } else if (!validatePassword(password)) {
                showError(passwordInput, 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }

            if (isValid) {
                // Show loading state
                loginBtn.classList.add('loading');
                loginBtn.textContent = 'Đang đăng nhập...';

                // Submit form (you can customize this part)
                setTimeout(() => {
                    this.submit();
                }, 1000);
            }
        });
    }

    // Remember me functionality
    if (rememberCheckbox) {
        // Load saved credentials
        const savedEmail = localStorage.getItem('rememberedEmail');
        if (savedEmail) {
            emailInput.value = savedEmail;
            rememberCheckbox.checked = true;
        }

        // Save credentials when form is submitted
        loginForm.addEventListener('submit', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Add floating label effect
    const formControls = document.querySelectorAll('.form-control');
    formControls.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('focused');
            }
        });

        // Check if input has value on page load
        if (input.value) {
            input.parentNode.classList.add('focused');
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            const form = e.target.closest('form');
            if (form) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        }
    });

    // Add ripple effect to button
    loginBtn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple effect CSS
const rippleCSS = `
.btn-login {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
`;

// Inject ripple CSS
const style = document.createElement('style');
style.textContent = rippleCSS;
document.head.appendChild(style);
