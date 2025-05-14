<?php
<!-- resources/views/customer/register.blade.php -->
<style>
    body {
        background: #f7f7f7;
        font-family: Arial, sans-serif;
    }
    .register-container {
        max-width: 400px;
        margin: 40px auto;
        background: #fff;
        padding: 32px 24px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .register-container h2 {
        text-align: center;
        margin-bottom: 24px;
        color: #333;
    }
    .register-container input[type="text"],
    .register-container input[type="email"],
    .register-container input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 16px;
    }
    .register-container button {
        width: 100%;
        padding: 12px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 12px;
        transition: background 0.2s;
    }
    .register-container button:hover {
        background: #0056b3;
    }
    .error-message {
        color: #d9534f;
        background: #fbeeea;
        border: 1px solid #f5c6cb;
        padding: 10px;
        border-radius: 6px;
        margin-top: 16px;
        text-align: center;
    }
</style>
<div class="register-container">
    <h2>Đăng ký tài khoản</h2>
    <form method="POST" action="{{ url('customer/register') }}">
        @csrf
        <input type="text" name="customer_name" placeholder="Họ và tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone_number" placeholder="Số điện thoại">
        <input type="text" name="address" placeholder="Địa chỉ">
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>
    @if($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif
</div>