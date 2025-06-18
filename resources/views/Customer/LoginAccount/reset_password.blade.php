<!DOCTYPE html>More actions
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đặt lại mật khẩu - {{ config('app.name', 'Shop') }}</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/customer_auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Meta tags -->
    <meta name="description" content="Đặt lại mật khẩu mới cho tài khoản của bạn">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
    <div class="auth-container">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Đặt lại mật khẩu</h2>
            <p>Tạo mật khẩu mới an toàn cho tài khoản của bạn.</p>
        </div>

        <!-- Body Section -->
        <div class="auth-body">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step completed">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-connector active"></div>
                <div class="step completed">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-connector active"></div>
                <div class="step active">3</div>
            </div>

            <!-- Display Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('customer.reset-password.submit') }}">
                @csrf

                <!-- Hidden Token Field -->
                <input type="hidden" name="token" value="{{ $token ?? request()->token }}">

                <!-- Email Field (readonly) -->
                <div class="form-group">
                <label>
                        <i class="fas fa-envelope"></i>
                        Địa chỉ Email
                    </label>
                    <input
                        type="email"
                        class="form-control"
                        value="{{ $email ?? '' }}"
                        readonly
                        tabindex="-1"
                        style="background: #f5f5f5;"
                    >
                </div>

                <!-- New Password Field -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mật khẩu mới
                    </label>
                    <div style="position: relative;">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu mới"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <span class="password-toggle">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div class="password-strength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <div class="strength-text"></div>
                        <div style="font-size: 0.8rem; color: #6c757d; margin-top: 5px;">
                            <strong>Yêu cầu:</strong> Ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt
                        </div>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-lock"></i>
                        Xác nhận mật khẩu
                    </label>
                    <div style="position: relative;">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Nhập lại mật khẩu mới"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <span class="password-toggle">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                </div>

                <!-- Security Tips -->
                <div class="alert alert-info" style="margin-bottom: 25px;">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Mẹo bảo mật:</strong>
                        <ul style="margin: 8px 0 0 20px; font-size: 0.85rem;">
                            <li>Sử dụng mật khẩu duy nhất cho tài khoản này</li>
                            <li>Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                            <li>Tránh sử dụng thông tin cá nhân dễ đoán</li>
                        </ul>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Đặt lại mật khẩu
                </button>

                <!-- Back to Login -->
                <a href="{{ route('customer.login') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại đăng nhập
                </a>
            </form>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="{{ asset('js/customer_auth.js') }}"></script>

    <!-- Additional Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            // Focus on password input
            passwordInput.focus();

            // Real-time password match checking
            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword && password !== confirmPassword) {
                    confirmPasswordInput.style.borderColor = '#dc3545';
                    showMatchError();
                } else if (confirmPassword && password === confirmPassword) {
                    confirmPasswordInput.style.borderColor = '#28a745';
                    hideMatchError();
                }
            }

            function showMatchError() {
                hideMatchError();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = '<i class="fas fa-times-circle"></i> Mật khẩu không khớp';
                confirmPasswordInput.parentNode.parentNode.appendChild(errorDiv);
            }

            function hideMatchError() {
                const existingError = confirmPasswordInput.parentNode.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
            }

            passwordInput.addEventListener('input', checkPasswordMatch);
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            // Form submission validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Mật khẩu xác nhận không khớp!');
                    confirmPasswordInput.focus();
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    alert('Mật khẩu phải có ít nhất 6 ký tự!');
                    passwordInput.focus();
                    return false;
                }
            });
        });
    </script>
</body>
