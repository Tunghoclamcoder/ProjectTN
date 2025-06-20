<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đăng nhập - {{ config('app.name', 'Shop') }}</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/customer_login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Meta tags for SEO -->
    <meta name="description" content="Đăng nhập vào tài khoản của bạn để mua sắm và quản lý đơn hàng">
    <meta name="keywords" content="đăng nhập, login, tài khoản, shop">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Welcome Section -->
        <div class="login-left">
            <div class="welcome-icon">
                <a href="{{ route('shop.home') }}">
                <i class="fas fa-shopping-bag"></i>
                </a>
            </div>
            <h2>Chào mừng trở lại!</h2>
            <p>Đăng nhập để tiếp tục mua sắm và trải nghiệm những sản phẩm tuyệt vời nhất từ chúng tôi.</p>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h3>Đăng nhập</h3>
                <p>Nhập thông tin của bạn để đăng nhập</p>
            </div>

            <!-- Display Success Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Display Error Messages -->
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

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('customer.login.submit') }}">
                @csrf

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
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

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mật khẩu
                    </label>
                    <div style="position: relative;">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu của bạn"
                            required
                            autocomplete="current-password"
                        >
                        <span class="password-toggle">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                </div>

                <!-- Form Options -->
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" style="color: #fff">Ghi nhớ đăng nhập</label>
                    </div>
                    <a href="{{ route('customer.forgot-password') }}" class="forgot-password">
                        Quên mật khẩu?
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>

                <!-- Divider -->
                <div class="divider">
                    <span>hoặc</span>
                </div>

                <!-- Register Link -->
                <div class="register-link">
                    Chưa có tài khoản?
                    <a href="{{ route('customer.register') }}">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="{{ asset('js/customer_login.js') }}"></script>

    <!-- Additional Scripts -->
    <script>
        // Add any additional JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on first input if no errors
            @if(!$errors->any() && !session('error'))
                document.getElementById('email').focus();
            @endif

            // Auto-fill demo credentials (remove in production)
            @if(config('app.env') === 'local')
                const demoBtn = document.createElement('button');
                demoBtn.type = 'button';
                demoBtn.className = 'btn-demo';
                demoBtn.innerHTML = '<i class="fas fa-user-cog"></i> Demo Account';
                demoBtn.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 10px 15px;
                    background: #28a745;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 0.9rem;
                    z-index: 1000;
                `;

                demoBtn.addEventListener('click', function() {
                    document.getElementById('email').value = 'demo@example.com';
                    document.getElementById('password').value = 'password1@';
                });

                document.body.appendChild(demoBtn);
            @endif
        });
    </script>
</body>

</html>
