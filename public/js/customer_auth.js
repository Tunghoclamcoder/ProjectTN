document.addEventListener("DOMContentLoaded", function () {
    // Elements
    const forms = document.querySelectorAll("form");
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    const passwordToggles = document.querySelectorAll(".password-toggle");
    const submitButtons = document.querySelectorAll(".btn-primary");

    // Password toggle functionality
    passwordToggles.forEach((toggle) => {
        toggle.addEventListener("click", function () {
            const passwordInput = this.parentNode.querySelector(
                'input[type="password"], input[type="text"]'
            );
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);

            const icon = this.querySelector("i");
            if (type === "password") {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
    });

    // Email validation
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) {
            strength += 1;
        } else {
            feedback.push("Ít nhất 8 ký tự");
        }

        if (/[a-z]/.test(password)) {
            strength += 1;
        } else {
            feedback.push("Chữ thường");
        }

        if (/[A-Z]/.test(password)) {
            strength += 1;
        } else {
            feedback.push("Chữ hoa");
        }

        if (/[0-9]/.test(password)) {
            strength += 1;
        } else {
            feedback.push("Số");
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            strength += 1;
        } else {
            feedback.push("Ký tự đặc biệt");
        }

        return { strength, feedback };
    }

    // Update password strength indicator
    function updatePasswordStrength(input, strengthContainer) {
        const password = input.value;
        const { strength, feedback } = checkPasswordStrength(password);

        const strengthBar = strengthContainer.querySelector(".strength-bar");
        const strengthText = strengthContainer.querySelector(".strength-text");

        // Remove existing classes
        strengthContainer.classList.remove(
            "strength-weak",
            "strength-medium",
            "strength-strong"
        );

        if (password.length === 0) {
            strengthContainer.style.display = "none";
            return;
        }

        strengthContainer.style.display = "block";

        if (strength <= 2) {
            strengthContainer.classList.add("strength-weak");
            strengthText.textContent = `Yếu - Cần: ${feedback.join(", ")}`;
        } else if (strength <= 4) {
            strengthContainer.classList.add("strength-medium");
            strengthText.textContent = `Trung bình - Cần: ${feedback.join(
                ", "
            )}`;
        } else {
            strengthContainer.classList.add("strength-strong");
            strengthText.textContent = "Mạnh - Mật khẩu an toàn";
        }
    }

    // Initialize password strength indicators
    passwordInputs.forEach((input) => {
        if (input.name === "password" || input.name === "new_password") {
            const strengthContainer =
                input.parentNode.parentNode.querySelector(".password-strength");
            if (strengthContainer) {
                input.addEventListener("input", function () {
                    updatePasswordStrength(this, strengthContainer);
                });
            }
        }
    });

    // Form validation functions
    function showError(input, message) {
        removeError(input);

        const errorDiv = document.createElement("div");
        errorDiv.className = "error-message";
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

        input.parentNode.appendChild(errorDiv);
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
    }

    function showSuccess(input, message = "") {
        removeError(input);
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");

        if (message) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
            input.parentNode.appendChild(successDiv);
        }
    }

    function removeError(input) {
        const errorMessage = input.parentNode.querySelector(".error-message");
        const successMessage =
            input.parentNode.querySelector(".success-message");

        if (errorMessage) errorMessage.remove();
        if (successMessage) successMessage.remove();

        input.classList.remove("is-invalid", "is-valid");
    }

    // Real-time validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach((input) => {
        input.addEventListener("blur", function () {
            const email = this.value.trim();
            if (!email) {
                showError(this, "Email là bắt buộc");
            } else if (!validateEmail(email)) {
                showError(this, "Email không hợp lệ");
            } else {
                showSuccess(this, "Email hợp lệ");
            }
        });

        input.addEventListener("input", function () {
            if (this.value.trim()) {
                removeError(this);
            }
        });
    });

    // Password validation
    passwordInputs.forEach((input) => {
        input.addEventListener("blur", function () {
            const password = this.value;
            const fieldName = this.getAttribute("placeholder") || "Mật khẩu";

            if (!password) {
                showError(this, `${fieldName} là bắt buộc`);
            } else if (password.length < 6) {
                showError(this, `${fieldName} phải có ít nhất 6 ký tự`);
            } else {
                showSuccess(this);
            }
        });

        input.addEventListener("input", function () {
            if (this.value) {
                removeError(this);
            }
        });
    });

    // Confirm password validation
    const confirmPasswordInputs = document.querySelectorAll(
        'input[name="password_confirmation"], input[name="confirm_password"]'
    );
    confirmPasswordInputs.forEach((input) => {
        input.addEventListener("blur", function () {
            const password = document.querySelector(
                'input[name="password"], input[name="new_password"]'
            ).value;
            const confirmPassword = this.value;

            if (!confirmPassword) {
                showError(this, "Xác nhận mật khẩu là bắt buộc");
            } else if (password !== confirmPassword) {
                showError(this, "Mật khẩu xác nhận không khớp");
            } else {
                showSuccess(this, "Mật khẩu khớp");
            }
        });
    });

    // Form submission
    forms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector(".btn-primary");
            const inputs = this.querySelectorAll("input[required]");
            let isValid = true;

            // Validate all required inputs
            inputs.forEach((input) => {
                const value = input.value.trim();

                if (!value) {
                    showError(
                        input,
                        `${
                            input.getAttribute("placeholder") || "Trường này"
                        } là bắt buộc`
                    );
                    isValid = false;
                } else if (input.type === "email" && !validateEmail(value)) {
                    showError(input, "Email không hợp lệ");
                    isValid = false;
                } else if (input.type === "password" && value.length < 6) {
                    showError(input, "Mật khẩu phải có ít nhất 6 ký tự");
                    isValid = false;
                }
            });

            // Check password confirmation
            const confirmPasswordInput = this.querySelector(
                'input[name="password_confirmation"], input[name="confirm_password"]'
            );
            if (confirmPasswordInput) {
                const passwordInput = this.querySelector(
                    'input[name="password"], input[name="new_password"]'
                );
                if (passwordInput.value !== confirmPasswordInput.value) {
                    showError(
                        confirmPasswordInput,
                        "Mật khẩu xác nhận không khớp"
                    );
                    isValid = false;
                }
            }

            if (isValid) {
                // Show loading state
                submitBtn.classList.add("loading");
                const originalText = submitBtn.textContent;
                submitBtn.textContent = "Đang xử lý...";

                // Submit form after delay
                setTimeout(() => {
                    this.submit();
                }, 1000);
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-10px)";
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Add ripple effect to buttons
    submitButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            const ripple = document.createElement("span");
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + "px";
            ripple.style.left = x + "px";
            ripple.style.top = y + "px";
            ripple.classList.add("ripple");

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Keyboard navigation
    document.addEventListener("keydown", function (e) {
        if (
            e.key === "Enter" &&
            e.target.tagName !== "BUTTON" &&
            e.target.tagName !== "TEXTAREA"
        ) {
            const form = e.target.closest("form");
            if (form) {
                const submitBtn = form.querySelector(
                    'button[type="submit"], .btn-primary'
                );
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        }
    });

    // Focus management
    const firstInput = document.querySelector('input:not([type="hidden"])');
    if (firstInput) {
        firstInput.focus();
    }
});
