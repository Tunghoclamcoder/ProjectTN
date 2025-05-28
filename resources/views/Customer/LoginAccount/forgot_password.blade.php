@include('Customer.components.header')

<div class="container">
    <h2>Quên mật khẩu</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('customer.send_reset_link') }}">
    @csrf
    <div class="form-group">
        <label for="email">Địa chỉ Email</label>
        <input type="email" name="email" class="form-control" required>
        @error('email')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
</form>

</div>

@include('Customer.components.footer')
