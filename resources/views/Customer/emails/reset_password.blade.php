<p>Xin chào {{ $customer->customer_name }},</p>
<p>Bạn vừa yêu cầu đặt lại mật khẩu. Nhấn vào liên kết dưới đây để đặt lại mật khẩu:</p>
<p>
    <a href="{{ route('customer.reset_password', ['token' => $token]) }}">
        Đặt lại mật khẩu
    </a>
</p>
<p>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
