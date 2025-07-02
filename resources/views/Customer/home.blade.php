<!DOCTYPE html>
<html>

<head>
    <title>Website bán đồ</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <script src="{{ asset('js/alert.js') }}"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
</head>

<body>
    @include('Customer.components.header')

    <div class="alerts-container" style="display: flex; justify-content: center;">
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
            <div class="alert alert-success alert-session">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <img src="{{ asset('images/slider.png') }}" class="w-100" alt="Slider">
    <div class="container">
        <div class="banner">
            <img src="{{ asset('images/banner-1.png') }}" class="w-100" alt="Banner 1">
            <img src="{{ asset('images/banner-2.png') }}" class="w-100" alt="Banner 2">
        </div>
    </div>
    <!----------PRODUCT HTML STARTS----->

    <div class="container">
        <div class="product-grid">
            @forelse($products as $product)
                <div class="pro">
                    <div class="product-image-container position-relative">
                        @if ($mainImage = $product->getMainImage())
                            <img src="{{ asset($mainImage->image_url) }}" class="w-100"
                                alt="{{ $product->product_name }}">

                            @if ($product->discount > 0)
                                <div
                                    class="discount-label position-absolute top-0 start-0 bg-danger text-white px-2 py-1">
                                    SALE {{ $product->discount }}%
                                </div>
                            @endif

                            @if ($product->quantity <= 0)
                                <div class="out-of-stock-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                    style="background-color: rgba(0, 0, 0, 0.5); color: white;">
                                    <span class="fw-bold">Đã bán hết</span>
                                </div>
                            @endif
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="w-100" alt="No image available">
                        @endif
                    </div>


                    <p class="prd-name">{{ $product->product_name }}</p>
                    <p>
                        @if ($product->discount > 0)
                            <b>{{ number_format($product->getDiscountedPrice()) }} VNĐ</b>
                            <strike>{{ number_format($product->price) }} VNĐ</strike>
                        @else
                            <b>{{ number_format($product->price) }} VNĐ</b>
                        @endif
                    </p>

                    <div class="product-actions">
                        <a href="{{ route('shop.product.show', $product->product_id) }}" class="btn-view">
                            Xem chi tiết
                        </a>

                        {{-- Nút add to cart --}}
                        @auth('customer')
                            <form class="add-to-cart-form d-inline" data-product-id="{{ $product->product_id }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                                <button type="button" class="cart-button" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                    <span style="width: 100%" class="add-to-cart">
                                        {{ $product->quantity > 0 ? 'Thêm vào giỏ hàng' : 'Hết hàng' }}
                                    </span>
                                    <span class="added">Đã thêm !</span>
                                    <i class="fas fa-shopping-cart"></i>
                                    <i class="fas fa-box"></i>
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="no-products">
                    <p>Không có sản phẩm nào.</p>
                </div>
            @endforelse
        </div>
        {{-- Hiển thị 3 feedback mới nhất --}}
        @if (isset($latestFeedbacks) && $latestFeedbacks->count())
            <div class="container mb-4 feedback-section">
                <h2 class="text-center mb-4" style="font-weight:600; color:#1a237e;">Khách hàng nói gì về chúng tôi?
                </h2>
                <div class="feedback-row">
                    @foreach ($latestFeedbacks as $feedback)
                        <div class="feedback-col">
                            <div class="card h-100 shadow-sm w-100">
                                <div class="card-body">
                                    <!-- Nội dung feedback như cũ -->
                                    <div class="d-flex align-items-center mb-2">
                                        <span
                                            class="fw-bold me-2">{{ $feedback->customer->customer_name ?? 'Khách hàng' }}
                                        </span>
                                        <span>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star{{ $i <= $feedback->rating ? ' text-warning' : ' text-secondary' }}"></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <div class="mb-2" style="margin: 10px 0 20px 0">
                                        <small class="text-muted" style="color: black">
                                            Đơn hàng #{{ $feedback->order_id }}

                                        </small>
                                    </div>
                                    <div>
                                        <p class="mb-0">"{{ $feedback->comment }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @include('Customer.components.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const cartButtons = document.querySelectorAll('.cart-button');

            cartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (button.disabled) return;

                    const form = button.closest('.add-to-cart-form');
                    const productId = form.dataset.productId;

                    // Add clicked animation
                    button.classList.add('clicked');

                    // Send AJAX request
                    fetch('{{ route('cart.add-to-cart') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update cart count if you have one
                                if (data.cartCount) {
                                    document.querySelector('.cart-count').textContent = data
                                        .cartCount;
                                }

                                // Show success message
                                showAlert('success', 'Sản phẩm đã được thêm vào giỏ hàng');
                            } else {
                                showAlert('error', data.message || 'Có lỗi xảy ra');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('error', 'Có lỗi xảy ra khi thêm sản phẩm');
                        })
                        .finally(() => {
                            // Remove clicked animation after 2 seconds
                            setTimeout(() => {
                                button.classList.remove('clicked');
                            }, 2000);
                        });
                });
            });
        });

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;

            const alertsContainer = document.querySelector('.alerts-container');
            alertsContainer.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    </script>

</html>
</body>
