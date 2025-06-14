<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://fonts.googleapis.com/css?family=Vibur" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/alert.js') }}"></script>
</head>

<body>
    <div class="alerts-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>


    <div class="login-form">
        <h1>N & T</h1>
        <form method="POST" action="{{ route('customer.login.submit') }}">
            @csrf

            <div class="form-group">
                <input type="email" name="email" class="form-control @error('email') wrong-entry @enderror"
                    placeholder="Email" value="{{ old('email') }}" required>
                <i class="fa fa-user"></i>
                @error('email')
                    <span class="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group log-status">
                <input type="password" name="password" class="form-control @error('password') wrong-entry @enderror"
                    placeholder="Mật khẩu" required>
                <i class="fa fa-lock"></i>
                @error('password')
                    <span class="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>

            @if (session('error'))
                <span class="alert">{{ session('error') }}</span>
            @endif

            {{-- <a class="link" href="{{ route('customer.password.request') }}">Quên mật khẩu?</a> --}}

            <button type="submit" class="log-btn">Đăng nhập</button>

            <div style="text-align: center; margin-top: 15px;">
                <span>Chưa có tài khoản? </span>
                <a href="{{ route('customer.register') }}">Đăng ký tại đây</a>
            </div>

            <div style="display: flex; align-items: center; justify-content: center; margin-top: 10px;">
                <a href="{{ route('customer.forgot_password') }}">Quên mật khẩu?</a>
            </div>
        </form>
    </div>

</body>

<style>
    @import "compass/css3";
    @import url(https://fonts.googleapis.com/css?family=Vibur);

    <style> {
        box-sizing: border-box;
        foFnt-family: arial;
    }

    body {
        background: #FF9000;
    }

    h1 {
        color: #ccc;
        text-align: center;
        font-family: 'Vibur', cursive;
        font-size: 50px;
    }

    .login-form {
        width: 350px;
        padding: 40px 30px;
        background: #eee;
        border-radius: 4px;
        margin: auto;
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
    }

    .form-control {
        width: 93%;
        height: 50px;
        border: none;
        padding: 5px 7px 5px 15px;
        background: #fff;
        color: #666;
        border: 2px solid #ddd;
        border-radius: 4px;
    }

    .form-control:focus,
    .form-control:focus+.fa {
        border-color: #10CE88;
        color: #10CE88;
    }

    .log-status.wrong-entry {
        animation: wrong-log 0.3s;
    }

    .log-btn {
        background: #0AC986;
        display: inline-block;
        width: 100%;
        font-size: 16px;
        height: 50px;
        color: #fff;
        text-decoration: none;
        border: none;
        border-radius: 4px;
    }

    .link {
        text-decoration: none;
        color: #C6C6C6;
        float: right;
        font-size: 12px;
        margin-bottom: 15px;
    }

    .link:hover {
        text-decoration: underline;
        color: #8C918F;
    }

    .alerts-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        width: 80%;
        max-width: 400px;
    }

    .alert {
        display: none;
        /* Ẩn mặc định */
        margin-bottom: 10px;
        padding: 15px;
        border-radius: 4px;
        text-align: center;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }

    .form-group .alert {
        position: absolute;
        bottom: -20px;
        left: 0;
        width: 100%;
        font-size: 12px;
        background: none;
        border: none;
        padding: 0;
    }

    .form-group {
        margin-bottom: 15px;
        position: relative;
    }

    .fa {
        position: absolute;
        right: 15px;
        top: 17px;
        color: #999;
    }

    .wrong-entry {
        border-color: #f00 !important;
    }
</style>
<script>
    $(document).ready(function() {
        // Xóa sự kiện click cũ
        $('.log-btn').off('click');

        // Xử lý form submission
        $('form').on('submit', function(e) {
            // Reset trạng thái
            $('.log-status').removeClass('wrong-entry');
            $('.alert').hide();

            // Kiểm tra validation phía client
            let hasError = false;
            $('.form-control').each(function() {
                if (!$(this).val()) {
                    hasError = true;
                    $(this).closest('.form-group').addClass('wrong-entry');
                }
            });
        });

        // Xử lý khi nhập vào input
        $('.form-control').on('input', function() {
            $(this).closest('.form-group').removeClass('wrong-entry');
            $(this).closest('.form-group').find('.alert').fadeOut('slow');
        });

        // Hiển thị alert message nếu có
        @if (session('error'))
            $('.alert-danger').fadeIn(500).delay(3000).fadeOut('slow');
        @endif

        @if (session('success'))
            $('.alert-success').fadeIn(500).delay(3000).fadeOut('slow');
        @endif
    });
</script>

</html>
