document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');

    // Password strength checker
    if (passwordInput) {
        const strengthBar = document.querySelector('.password-strength');
        const strengthFill = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);

            if (password.length > 0) {
                strengthBar.classList.add('show');
                updateStrengthBar(strength, strengthFill, strengthText);
            } else {
                strengthBar.classList.remove('show');
            }
        });
    }

    // Password confirmation checker
    if (confirmPasswordInput) {
        const matchIndicator = document.querySelector('.password-match');
        const matchText = document.querySelector('.match-text');

        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (confirmPassword.length > 0) {
                matchIndicator.classList.add('show');

                if (password === confirmPassword) {
                    matchText.textContent = 'Mật khẩu khớp';
                    matchText.className = 'match-text match';
                } else {
                    matchText.textContent = 'Mật khẩu không khớp';
                    matchText.className = 'match-text no-match';
                }
            } else {
                matchIndicator.classList.remove('show');
            }
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            // Simulate form submission (replace with actual submission)
            setTimeout(() => {
                // Remove loading, add success
                submitBtn.classList.remove('loading');
                submitBtn.classList.add('success');

                // Actually submit the form after animation
                setTimeout(() => {
                    form.submit();
                }, 1000);
            }, 2000);
        });
    }

    // Add fade-in animation to form
    document.querySelector('.auth-card').classList.add('fade-in');
});

// Password toggle function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = input.parentElement.querySelector('.password-toggle i');

    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;

    // Length check
    if (password.length >= 8) strength++;

    // Lowercase check
    if (/[a-z]/.test(password)) strength++;

    // Uppercase check
    if (/[A-Z]/.test(password)) strength++;

    // Number check
    if (/\d/.test(password)) strength++;

    // Special character check
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    return strength;
}

// Update strength bar
function updateStrengthBar(strength, fill, text) {
    fill.className = 'strength-fill';

    switch (strength) {
        case 0:
        case 1:
            fill.classList.add('weak');
            text.textContent = 'Mật khẩu yếu';
            break;
        case 2:
            fill.classList.add('fair');
            text.textContent = 'Mật khẩu trung bình';
            break;
        case 3:
        case 4:
            fill.classList.add('good');
            text.textContent = 'Mật khẩu tốt';
            break;
        case 5:
            fill.classList.add('strong');
            text.textContent = 'Mật khẩu mạnh';
            break;
    }
}

// Input validation effects
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-input');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !this.checkValidity()) {
                this.parentElement.classList.add('shake');
                setTimeout(() => {
                    this.parentElement.classList.remove('shake');
                }, 500);
            }
        });
    });
});
