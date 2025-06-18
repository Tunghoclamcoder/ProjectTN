
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quên mật khẩu - {{ config('app.name', 'Shop') }}</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/customer_auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Meta tags -->
    <meta name="description" content="Khôi phục mật khẩu cho tài khoản của bạn">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
    <div class="auth-container">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-key"></i>
            </div>
            <h2>Quên mật khẩu?</h2>
            <p>Đừng lo lắng! Nhập email của bạn và chúng tôi sẽ gửi liên kết đặt lại mật khẩu.</p>
        </div>

        <!-- Body Section -->
        <div class="auth-body">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active">1</div>
                <div class="step-connector"></div>
                <div class="step">2</div>
                <div class="step-connector"></div>
                <div class="step">3</div>
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

            <!-- Information Alert -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Nhập địa chỉ email đã đăng ký để nhận liên kết đặt lại mật khẩu.
            </div>

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('customer.forgot-password.submit') }}">
                @csrf

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Địa chỉ Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Nhập địa chỉ email của bạn"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                    >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Gửi liên kết đặt lại
                </button>

                <!-- Back to Login -->
                <a href="{{ route('customer.login') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại đăng nhập
                </a>
            </form>

            <!-- Additional Help -->
            <div class="back-link">
                <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 10px;">
                    Không nhận được email?
                </p>
                <a href="#" onclick="resendEmail()" style="font-size: 0.9rem;">
                    <i class="fas fa-redo"></i>
                    Gửi lại email
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="{{ asset('js/customer_auth.js') }}"></script>

    <!-- Additional Scripts -->
    <script>
        function resendEmail() {
            const email = document.getElementById('email').value;
            if (!email) {
                alert('Vui lòng nhập email trước khi gửi lại.');
                document.getElementById('email').focus();
                return;
            }

            // You can implement resend functionality here
            alert('Đã gửi lại email đặt lại mật khẩu!');
        }

        // Auto-focus on email input
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html>
