<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - SportNT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .change-password-container {
            width: 100%;
            max-width: 500px;
        }

        .change-password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideInUp 0.6s ease;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .card-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .card-header p {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
            display: block;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            z-index: 2;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 15px 50px 15px 45px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--light-color);
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: white;
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            z-index: 2;
            padding: 5px;
            transition: color 0.3s ease;
            font-size: 16px;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
            background: var(--danger-color);
        }

        .strength-text {
            color: var(--secondary-color);
            font-size: 12px;
            font-weight: 500;
        }

        .password-requirements {
            background: var(--light-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .password-requirements h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
            font-size: 14px;
            color: var(--secondary-color);
            transition: all 0.3s ease;
        }

        .requirement.valid {
            color: var(--success-color);
        }

        .requirement.valid i {
            color: var(--success-color);
        }

        .requirement i {
            color: var(--danger-color);
            font-size: 12px;
            width: 16px;
            text-align: center;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-height: 50px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            color: var(--secondary-color);
            background: white;
        }

        .btn-outline-secondary:hover {
            background: var(--light-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            color: var(--secondary-color);
        }

        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
            font-weight: 500;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 24px;
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .back-link:hover {
            color: white;
            transform: translateX(-5px);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .card-header {
                padding: 30px 20px;
            }

            .card-body {
                padding: 30px 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                min-width: auto;
            }

            .change-password-container {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .form-control {
                padding: 12px 45px 12px 40px;
                font-size: 14px;
            }

            .input-icon {
                font-size: 14px;
                left: 12px;
            }

            .password-toggle {
                right: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <a href="{{ route('customer.profile') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="change-password-container">
        <div class="change-password-card">
            <!-- Header -->
            <div class="card-header">
                <div class="header-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Đổi mật khẩu</h2>
                <p>Cập nhật mật khẩu để bảo mật tài khoản của bạn</p>
            </div>

            <!-- Form -->
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('customer.change-password.update') }}" method="POST" id="changePasswordForm">
                    @csrf

                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" placeholder="Nhập mật khẩu hiện tại"
                                required>
                            <button type="button" class="password-toggle" data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required>
                            <button type="button" class="password-toggle" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill"></div>
                            </div>
                            <small class="strength-text">Độ mạnh mật khẩu</small>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <input type="password"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                id="new_password_confirmation" name="new_password_confirmation"
                                placeholder="Nhập lại mật khẩu mới" required>
                            <button type="button" class="password-toggle" data-target="new_password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Requirements -->
                    <div class="password-requirements">
                        <h6>Yêu cầu mật khẩu:</h6>
                        <ul class="requirements-list">
                            <li class="requirement" data-requirement="length">
                                <i class="fas fa-times"></i>
                                Ít nhất 8 ký tự
                            </li>
                            <li class="requirement" data-requirement="uppercase">
                                <i class="fas fa-times"></i>
                                Có ít nhất 1 chữ hoa
                            </li>
                            <li class="requirement" data-requirement="lowercase">
                                <i class="fas fa-times"></i>
                                Có ít nhất 1 chữ thường
                            </li>
                            <li class="requirement" data-requirement="number">
                                <i class="fas fa-times"></i>
                                Có ít nhất 1 số
                            </li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Cập nhật mật khẩu
                        </button>
                        <a href="{{ route('customer.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            document.querySelectorAll('.password-toggle').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Password strength checker
            const newPasswordInput = document.getElementById('new_password');
            const strengthBar = document.querySelector('.strength-fill');
            const strengthText = document.querySelector('.strength-text');
            const requirements = document.querySelectorAll('.requirement');

            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', function() {
                    const password = this.value;
                    const strength = checkPasswordStrength(password);

                    // Update strength bar
                    strengthBar.style.width = strength.percentage + '%';
                    strengthBar.style.background = strength.color;
                    strengthText.textContent = strength.text;

                    // Update requirements
                    updateRequirements(password);
                });
            }

            function checkPasswordStrength(password) {
                let score = 0;

                if (password.length >= 8) score++;
                if (/[a-z]/.test(password)) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;

                const percentage = (score / 5) * 100;

                if (score < 2) {
                    return {
                        percentage,
                        color: '#ef4444',
                        text: 'Yếu'
                    };
                } else if (score < 4) {
                    return {
                        percentage,
                        color: '#f59e0b',
                        text: 'Trung bình'
                    };
                } else {
                    return {
                        percentage,
                        color: '#10b981',
                        text: 'Mạnh'
                    };
                }
            }

            function updateRequirements(password) {
                const checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password)
                };

                requirements.forEach(req => {
                    const type = req.getAttribute('data-requirement');
                    const icon = req.querySelector('i');

                    if (checks[type]) {
                        req.classList.add('valid');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-check');
                    } else {
                        req.classList.remove('valid');
                        icon.classList.remove('fa-check');
                        icon.classList.add('fa-times');
                    }
                });
            }

            // Form validation
            const form = document.getElementById('changePasswordForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('new_password_confirmation').value;

                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('Mật khẩu xác nhận không khớp!');
                        return false;
                    }

                    if (newPassword.length < 8) {
                        e.preventDefault();
                        alert('Mật khẩu phải có ít nhất 8 ký tự!');
                        return false;
                    }
                });
            }
        });
    </script>
</body>

</html>
