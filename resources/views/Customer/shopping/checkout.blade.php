<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">
        <div class="py-5 text-center">
            <h1>Thanh Toán Đơn Hàng</h1>
        </div>

        <div class="row">
            <!-- Cart Summary -->
            <div class="col-md-4 order-2">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Giỏ hàng của bạn</span>
                    <span class="badge bg-primary rounded-pill">{{ count($cartItems) }}</span>
                </h4>

                <ul class="list-group mb-3">
                    @foreach ($cartItems as $item)
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">{{ $item->product->product_name }}</h6>
                                <small class="text-muted">Số lượng: {{ $item->sold_quantity }}</small>
                            </div>
                            <span
                                class="text-muted">{{ number_format($item->sold_price * $item->sold_quantity) }}đ</span>
                        </li>
                    @endforeach

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tổng tiền</span>
                        <strong>{{ number_format($total) }}đ</strong>
                    </li>
                </ul>

                <form class="card p-2" id="voucherForm">
                    @csrf
                    <div class="input-group">
                        <select class="form-select" name="voucher_code" id="voucher_select">
                            <option value="">Chọn mã giảm giá</option>
                            @foreach ($activeVouchers as $voucher)
                                <option value="{{ $voucher->code }}" data-discount="{{ $voucher->discount_amount }}"
                                    data-percentage="{{ $voucher->discount_percentage }}"
                                    data-min="{{ $voucher->minimum_purchase_amount }}">
                                    {{ $voucher->code }}
                                    @if ($voucher->discount_percentage)
                                        (Giảm {{ $voucher->discount_percentage }}%)
                                    @else
                                        (Giảm {{ number_format($voucher->discount_amount) }}đ)
                                    @endif
                                    @if ($voucher->minimum_purchase_amount)
                                        - Đơn tối thiểu {{ number_format($voucher->minimum_purchase_amount) }}đ
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-secondary">Áp dụng</button>
                    </div>
                    <div id="voucher-message" class="mt-2"></div>
                </form>
            </div>

            <!-- Checkout Form -->
            <div class="col-md-8 order-1">
                <h4 class="mb-3">Thông tin giao hàng</h4>
                <form action="{{ route('order.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="voucher_id" value="{{ session('voucher_id') }}">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="receiver_name" class="form-label">Tên người nhận</label>
                            <input type="text" class="form-control" id="receiver_name" name="receiver_name" required>
                        </div>

                        <div class="col-12">
                            <label for="receiver_phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="receiver_phone" name="receiver_phone"
                                required>
                        </div>

                        <div class="col-12">
                            <label for="receiver_address" class="form-label">Địa chỉ giao hàng</label>
                            <input type="text" class="form-control" id="receiver_address" name="receiver_address"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                            <select class="form-select" id="payment_method" name="payment_method_id" required>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->method_id }}">{{ $method->method_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="shipping_method" class="form-label">Phương thức vận chuyển</label>
                            <select class="form-select" id="shipping_method" name="shipping_method_id" required>
                                @foreach ($shippingMethods as $method)
                                    <option value="{{ $method->method_id }}">{{ $method->method_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button class="w-100 btn btn-primary btn-lg" type="submit">Đặt hàng</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    // Xử lý khi thay đổi voucher để kiểm tra điều kiện
    document.getElementById('voucher_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const total = {{ $total }};

        if (this.value) {
            const minPurchase = parseFloat(selectedOption.dataset.min);
            if (minPurchase && total < minPurchase) {
                alert(
                    `Đơn hàng cần tối thiểu ${new Intl.NumberFormat('vi-VN').format(minPurchase)}đ để sử dụng voucher này`
                );
                this.value = '';
                return;
            }
        }
    });

    // Xử lý submit form voucher
    document.getElementById('voucherForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const voucherCode = document.getElementById('voucher_select').value;

        // Kiểm tra nếu chưa chọn voucher
        if (!voucherCode) {
            const messageDiv = document.getElementById('voucher-message');
            messageDiv.className = 'text-danger mt-2';
            messageDiv.textContent = 'Vui lòng chọn mã giảm giá trước khi áp dụng';
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('voucher_code', voucherCode);

        fetch('{{ route('voucher.apply') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('voucher-message');

                if (data.success) {
                    messageDiv.className = 'alert alert-success mt-2';
                    // Cập nhật tổng tiền
                    document.querySelector('.list-group-item strong').textContent =
                        new Intl.NumberFormat('vi-VN').format(data.new_total) + 'đ';

                    // Lưu voucher_id vào form checkout
                    const checkoutForm = document.querySelector(
                        'form[action="{{ route('order.store') }}"]');
                    let voucherInput = checkoutForm.querySelector('input[name="voucher_id"]');
                    if (!voucherInput) {
                        voucherInput = document.createElement('input');
                        voucherInput.type = 'hidden';
                        voucherInput.name = 'voucher_id';
                        checkoutForm.appendChild(voucherInput);
                    }
                    voucherInput.value = data.voucher_id;
                } else {
                    messageDiv.className = 'alert alert-danger mt-2';
                }
                messageDiv.textContent = data.message;
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('voucher-message');
                messageDiv.className = 'alert alert-danger mt-2';
                messageDiv.textContent = 'Có lỗi xảy ra khi áp dụng mã giảm giá';
            });
    });
</script>

</html>
