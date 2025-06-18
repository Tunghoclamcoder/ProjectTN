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

                    <!-- Add discount amount display -->
                    <li class="list-group-item d-flex justify-content-between text-success" id="discount-row"
                        style="display: none;">
                        <div>
                            <h6 class="my-0">Giảm giá</h6>
                            <small id="voucher-code-display" class="text-muted"></small>
                        </div>
                        <div class="text-end">
                            <span id="discount-amount" class="d-block">đ</span>
                            <small id="discount-details" class="text-muted"></small>
                        </div>
                    </li>

                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">Phí vận chuyển</h6>
                            <small class="text-muted" id="shipping-method-name"></small>
                        </div>
                        <span class="text-muted" id="shipping-fee">0đ</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Tổng tiền</span>
                        <strong id="final-total">{{ number_format($total) }}đ</strong>
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
                                    <option value="{{ $method->method_id }}" data-fee="{{ $method->shipping_fee }}">
                                        {{ $method->method_name }} ({{ number_format($method->shipping_fee) }}đ)
                                    </option>
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
    document.addEventListener('DOMContentLoaded', function() {
        const shippingMethod = document.getElementById('shipping_method');
        const shippingFeeDisplay = document.getElementById('shipping-fee');
        const finalTotalDisplay = document.getElementById('final-total');
        let voucherDiscount = 0;
        let baseTotal = 0;
        let currentShippingFee = 0;

        // Calculate product total (excluding shipping)
        document.querySelectorAll('.list-group-item span.text-muted').forEach(item => {
            if (!item.id || item.id !== 'shipping-fee') {
                const amount = parseFloat(item.textContent.replace(/[^\d]/g, ''));
                if (!isNaN(amount)) {
                    baseTotal += amount;
                }
            }
        });

        function updateTotalDisplay(total) {
            finalTotalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        }

        function calculateTotal() {
            // Get current shipping fee
            currentShippingFee = parseFloat(shippingMethod.options[shippingMethod.selectedIndex].dataset.fee) ||
                15000;

            // Update shipping fee display
            shippingFeeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(currentShippingFee) + 'đ';

            // Calculate total with proper order
            const subtotal = baseTotal; // Product total
            const shipping = currentShippingFee; // Shipping fee
            const discount = voucherDiscount; // Voucher discount

            // Final calculation: (subtotal + shipping) - discount
            const finalTotal = (subtotal + shipping) - discount;

            // Update total display
            updateTotalDisplay(finalTotal);

            console.log('Calculation Details:', {
                subtotal,
                shipping,
                discount,
                finalTotal,
                formula: `(${subtotal} + ${shipping}) - ${discount} = ${finalTotal}`
            });

            return finalTotal;
        }

        // Handle voucher application
        document.getElementById('voucherForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedOption = document.getElementById('voucher_select').options[
                document.getElementById('voucher_select').selectedIndex
            ];

            if (!selectedOption.value) return;

            const formData = new FormData(this);

            fetch('{{ route('voucher.apply') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Set voucher discount
                        voucherDiscount = parseFloat(selectedOption.dataset.discount) || 0;

                        // Update discount display
                        const discountRow = document.getElementById('discount-row');
                        const discountAmount = document.getElementById('discount-amount');
                        discountRow.style.display = 'flex';
                        discountAmount.textContent = '-' + new Intl.NumberFormat('vi-VN').format(
                            voucherDiscount) + 'đ';

                        // Calculate new total with shipping included
                        calculateTotal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    voucherDiscount = 0;
                    calculateTotal();
                });
        });

        // Update total when shipping method changes
        shippingMethod.addEventListener('change', calculateTotal);

        // Initial calculation
        calculateTotal();
    });

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

        const selectedOption = document.getElementById('voucher_select').options[
            document.getElementById('voucher_select').selectedIndex
        ];
        const originalTotal = {{ $total }};

        if (!selectedOption.value) {
            document.getElementById('voucher-message').innerHTML =
                '<div class="alert alert-danger">Vui lòng chọn mã giảm giá</div>';
            discountRow.style.display = 'none';
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('voucher_code', selectedOption.value);

        fetch('{{ route('voucher.apply') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const discountRow = document.getElementById('discount-row');
                const discountAmount = document.getElementById('discount-amount');
                const discountDetails = document.getElementById('discount-details');
                const voucherCodeDisplay = document.getElementById('voucher-code-display');

                if (data.success) {
                    // Calculate discount
                    const discountValue = originalTotal - data.new_total;

                    // Show discount row
                    discountRow.style.display = 'flex';

                    // Update discount amount
                    discountDetails.textContent = '-' + new Intl.NumberFormat('vi-VN').format(
                        discountValue) + 'đ';

                    // Display voucher details
                    const isPercentage = selectedOption.dataset.percentage;
                    const discountText = isPercentage ?
                        `Giảm ${selectedOption.dataset.percentage}%` :
                        `Giảm ${new Intl.NumberFormat('vi-VN').format(selectedOption.dataset.discount)}đ`;

                    voucherCodeDisplay.textContent = `Mã: ${selectedOption.value}`;
                    discountDetails.textContent = discountText;

                    // Update final total
                    document.getElementById('final-total').textContent =
                        new Intl.NumberFormat('vi-VN').format(data.new_total) + 'đ';
                } else {
                    discountRow.style.display = 'none';
                }

                // Show message
                document.getElementById('voucher-message').innerHTML =
                    `<div class="alert alert-${data.success ? 'success' : 'danger'}">${data.message}</div>`;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('discount-row').style.display = 'none';
                document.getElementById('voucher-message').innerHTML =
                    '<div class="alert alert-danger">Có lỗi xảy ra khi áp dụng mã giảm giá</div>';
            });
    });

    document.getElementById('shipping_method').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const shippingFee = parseFloat(selectedOption.dataset.fee);
        const methodName = selectedOption.text.split(' (')[0];
        const originalTotal = {{ $total }};

        // Update shipping fee display
        document.getElementById('shipping-fee').textContent =
            new Intl.NumberFormat('vi-VN').format(shippingFee) + 'đ';
        document.getElementById('shipping-method-name').textContent = methodName;

        // Calculate new total with shipping
        let finalTotal = originalTotal + shippingFee;

        // If there's a discount applied, subtract it
        const discountAmount = document.getElementById('discount-amount');
        if (discountAmount && discountAmount.style.display !== 'none') {
            const discount = parseInt(discountAmount.textContent.replace(/[^\d]/g, ''));
            finalTotal -= discount;
        }

        // Update final total
        document.getElementById('final-total').textContent =
            new Intl.NumberFormat('vi-VN').format(finalTotal) + 'đ';
    });

    // Trigger change event to set initial shipping fee
    document.getElementById('shipping_method').dispatchEvent(new Event('change'));
</script>

</html>
