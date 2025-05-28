<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết sản phẩm</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <script src="{{ asset('js/alert.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    @include('Customer.components.header')

    <div class="container py-5">
        <h1 class="mb-5">Giỏ hàng của bạn</h1>
        <div class="row">
            <div class="col-lg-8" style="width: 100%;">
                <!-- Cart Items -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Tổng đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính</span>
                            <span>{{ number_format(
                                $cartItems->sum(function ($item) {
                                    return $item->sold_quantity * $item->product->getDiscountedPrice();
                                }),
                            ) }}
                                VNĐ</span>
                        </div>

                        @if ($cartOrder && $cartOrder->orderDetails->count() > 0)
                            @foreach ($cartOrder->orderDetails as $detail)
                                <div class="row cart-item mb-3">
                                    <div class="col-md-3">
                                        @if ($detail->product->getMainImage())
                                            <img src="{{ Storage::url($detail->product->getMainImage()->image_url) }}"
                                                alt="{{ $detail->product->product_name }}" class="img-fluid rounded">
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        <h5>{{ $detail->product->product_name }}</h5>
                                        <p>Đơn giá: {{ number_format($detail->sold_price) }} VNĐ</p>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <input type="number" class="form-control quantity-input"
                                                value="{{ $detail->sold_quantity }}" min="1"
                                                max="{{ $detail->product->quantity }}"
                                                data-price="{{ $detail->sold_price }}"
                                                data-product-id="{{ $detail->product_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="product-total" id="total-{{ $detail->product_id }}">
                                            {{ number_format($detail->sold_price * $detail->sold_quantity) }} VNĐ
                                        </p>

                                        <button class="btn btn-danger btn-sm delete-item"
                                            data-product-id="{{ $detail->product_id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex justify-content-end mb-3">
                                @if ($cartOrder && $cartOrder->orderDetails->count() > 0)
                                    <button class="btn btn-danger clear-cart">
                                        <i class="bi bi-trash"></i> Xóa tất cả
                                    </button>
                                @endif
                            </div>
                        @else
                            <p>Giỏ hàng trống</p>
                        @endif
                    </div>
                </div>
                <!-- Continue Shopping Button -->
                <div class="text-start mb-4">
                    <a href="{{ route('shop.home') }}" class="btn btn-outline-primary">
                        <i style="color: blue" class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>

                @if ($cartItems->count() > 0)
                    <a href="{{ route('checkout') }} #" class="btn btn-primary w-100">
                        Checkout
                    </a>
                @endif
            </div>
        </div>
    </div>

    @include('Customer.components.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.quantity-input');

            quantityInputs.forEach(input => {
                input.addEventListener('change', async function() {
                    let quantity = parseInt(this.value);
                    const maxQuantity = parseInt(this.getAttribute('max'));
                    const productId = this.dataset.productId;
                    const price = parseFloat(this.dataset.price);

                    // Validate quantity
                    if (isNaN(quantity) || quantity < 1) {
                        alert('Số lượng không thể nhỏ hơn 1!');
                        quantity = 1;
                        this.value = 1;
                    } else if (quantity > maxQuantity) {
                        alert(`Số lượng tối đa có sẵn là ${maxQuantity}`);
                        quantity = maxQuantity;
                        this.value = maxQuantity;
                    }

                    try {
                        const response = await fetch('/cart/update-quantity', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                quantity: quantity
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Cập nhật tổng tiền của sản phẩm
                            const total = quantity * price;
                            const formattedTotal = new Intl.NumberFormat('vi-VN').format(total);
                            const productTotalElement = document.getElementById(
                                `total-${productId}`);
                            if (productTotalElement) {
                                productTotalElement.textContent = `${formattedTotal} VNĐ`;
                            }

                            // Cập nhật tổng giỏ hàng
                            if (data.cartTotal) {
                                const tempTotal = new Intl.NumberFormat('vi-VN').format(data
                                    .cartTotal);
                                // Cập nhật tất cả các phần tử hiển thị tổng tiền
                                document.querySelectorAll(
                                        '.d-flex.justify-content-between span:last-child')
                                    .forEach(element => {
                                        element.textContent = `${tempTotal} VNĐ`;
                                    });
                            }
                        } else {
                            throw new Error(data.message ||
                                'Có lỗi xảy ra khi cập nhật số lượng');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert(error.message);
                    }
                });

                // Prevent negative numbers and 'e' character
                input.addEventListener('keydown', function(e) {
                    if (e.key === '-' || e.key === 'e') {
                        e.preventDefault();
                    }
                });
            });

            function updateQuantity(productId, newQuantity) {
                fetch('/cart/update-quantity', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: newQuantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật tổng giỏ hàng nếu cần
                            if (data.cartTotal) {
                                const cartTotalElements = document.querySelectorAll('.cart-total');
                                cartTotalElements.forEach(element => {
                                    element.textContent = new Intl.NumberFormat('vi-VN').format(data
                                        .cartTotal) + ' VNĐ';
                                });
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi cập nhật số lượng');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi cập nhật số lượng');
                    });
            }

            const deleteButtons = document.querySelectorAll('.delete-item');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                        deleteCartItem(productId);
                    }
                });
            });

            function deleteCartItem(productId) {
                fetch(`/cart/delete/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show updated cart
                            window.location.reload();
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xóa sản phẩm');
                    });
            }

            // Clear entire cart
            const clearCartButton = document.querySelector('.clear-cart');
            if (clearCartButton) {
                clearCartButton.addEventListener('click', function() {
                    if (confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
                        clearCart();
                    }
                });
            }

            function deleteCartItem(productId) {
                fetch(`/cart/delete/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xóa sản phẩm');
                    });
            }

            function clearCart() {
                fetch('/cart/clear', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xóa giỏ hàng');
                    });
            }
        });
    </script>
</body>
