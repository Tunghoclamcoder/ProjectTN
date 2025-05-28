<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trang đăng ký tài khoản khách hàng</title>
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

    <div class="main">
        <h2>Đăng Ký Tài Khoản</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.register.submit') }}">
            @csrf

            <div class="form-group">
                <label for="customer_name">Họ và tên:</label>
                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                    class="@error('customer_name') is-invalid @enderror" required />
                @error('customer_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="@error('email') is-invalid @enderror" required />
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" class="@error('password') is-invalid @enderror"
                    pattern="^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9])\S{8,}$"
                    title="Mật khẩu phải chứa ít nhất 1 số, 1 chữ cái, 1 ký tự đặc biệt và dài tối thiểu 8 ký tự"
                    required />
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Nhập lại mật khẩu:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required />
            </div>

            <div class="form-group">
                <label for="phone_number">Số điện thoại:</label>
                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                    class="@error('phone_number') is-invalid @enderror" pattern="[0-9]{10}"
                    title="Số điện thoại phải có 10 chữ số" />
                @error('phone_number')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ:</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}"
                    class="@error('address') is-invalid @enderror" />
                @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit">Đăng Ký</button>

            <p class="text-center mt-3">
                Đã có tài khoản?
                <a href="{{ route('customer.login') }}">Đăng nhập tại đây</a>
            </p>
        </form>
    </div>
</body>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .main {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        padding: 20px;
        width: 550px;
    }

    .main h2 {
        color: #4caf50;
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
        width: 100%;
        margin-bottom: 15px;
        padding: 10px;
        box-sizing: border-box;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    button[type="submit"] {
        padding: 15px;
        border-radius: 10px;
        border: none;
        background-color: #4caf50;
        color: white;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
</style>
<script>
    $(document).ready(function() {
        $('.log-btn').click(function() {
            $('.log-status').addClass('wrong-entry');
            $('.alert').fadeIn(500);
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);

        });
        $('.form-control').keypress(function() {
            $('.log-status').removeClass('wrong-entry');
        });

    });
</script>

</html>
