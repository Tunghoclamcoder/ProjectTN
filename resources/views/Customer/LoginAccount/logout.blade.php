<!-- resources/views/customer/logout.blade.php -->
<style>
    body {
        background: #f7f7f7;
        font-family: Arial, sans-serif;
    }
    .logout-container {
        max-width: 400px;
        margin: 80px auto;
        background: #fff;
        padding: 32px 24px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
    }
    .logout-container h2 {
        color: #333;
        margin-bottom: 18px;
    }
    .logout-container a {
        display: inline-block;
        margin-top: 18px;
        padding: 10px 24px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 16px;
        transition: background 0.2s;
    }
    .logout-container a:hover {
        background: #0056b3;
    }
</style>
<div class="logout-container">
    <h2>Đăng xuất thành công!</h2>
    <p>Bạn đã đăng xuất khỏi tài khoản.</p>
    <a href="{{ url('customer/login') }}">Quay lại đăng nhập</a>
</div>