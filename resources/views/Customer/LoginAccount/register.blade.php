<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-background">
            <div class="bg-shape shape-1"></div>
            <div class="bg-shape shape-2"></div>
            <div class="bg-shape shape-3"></div>
        </div>

        <div class="auth-content">
            <div class="auth-card">
                <!-- Header -->
                <div class="auth-header">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h1>Tạo tài khoản mới</h1>
                        <p>Tham gia cộng đồng của chúng tôi ngay hôm nay</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="auth-form">
                    <form method="POST" action="{{ route('customer.register.submit') }}" id="registerForm">
                        @csrf

                        <!-- Full Name -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input type="text"
                                       id="customer_name"
                                       name="customer_name"
                                       value="{{ old('customer_name') }}"
                                       class="form-input @error('customer_name') is-invalid @enderror"
                                       placeholder=" "
                                       required />
                                <label for="customer_name" class="form-label">Họ và tên</label>
                                <div class="input-border"></div>
                            </div>
                            @error('customer_name')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="form-input @error('email') is-invalid @enderror"
                                       placeholder=" "
                                       required />
                                <label for="email" class="form-label">Địa chỉ email</label>
                                <div class="input-border"></div>
                            </div>
                            @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <input type="text"
                                       id="phone_number"
                                       name="phone_number"
                                       value="{{ old('phone_number') }}"
                                       class="form-input @error('phone_number') is-invalid @enderror"
                                       placeholder=" "
                                       pattern="[0-9]{10}"
                                       title="Số điện thoại phải có 10 chữ số" />
                                <label for="phone_number" class="form-label">Số điện thoại</label>
                                <div class="input-border"></div>
                            </div>
                            @error('phone_number')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <input type="text"
                                       id="address"
                                       name="address"
                                       value="{{ old('address') }}"
                                       class="form-input @error('address') is-invalid @enderror"
                                       placeholder=" " />
                                <label for="address" class="form-label">Địa chỉ</label>
                                <div class="input-border"></div>
                            </div>
                            @error('address')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-input @error('password') is-invalid @enderror"
                                       placeholder=" "
                                       pattern="^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9])\S{8,}$"
                                       title="Mật khẩu phải chứa ít nhất 1 số, 1 chữ cái, 1 ký tự đặc biệt và dài tối thiểu 8 ký tự"
                                       required />
                                <label for="password" class="form-label">Mật khẩu</label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="input-border"></div>
                            </div>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <span class="strength-text">Độ mạnh mật khẩu</span>
                            </div>
                            @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-input"
                                       placeholder=" "
                                       required />
                                <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="input-border"></div>
                            </div>
                            <div class="password-match">
                                <span class="match-text"></span>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-group">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms" class="checkbox-label">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">
                                        Tôi đồng ý với
                                        <a href="#" class="terms-link">Điều khoản dịch vụ</a>
                                        và
                                        <a href="#" class="terms-link">Chính sách bảo mật</a>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="submit-btn" id="submitBtn">
                                <span class="btn-text">Tạo tài khoản</span>
                                <span class="btn-loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Đang xử lý...
                                </span>
                                <div class="btn-success">
                                    <i class="fas fa-check"></i>
                                    Thành công!
                                </div>
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="auth-footer">
                            <p>Đã có tài khoản?
                                <a href="{{ route('customer.login') }}" class="auth-link">
                                    Đăng nhập ngay
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/signup.js') }}"></script>
</body>
</html>
