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
            <div class="alert alert-success">
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
                    <div class="product-image-container">
                        @if ($mainImage = $product->getMainImage())
                            <img src="{{ Storage::url($mainImage->image_url) }}" class="w-100"
                                alt="{{ $product->product_name }}">
                            @if ($product->discount > 0)
                                <div class="discount-label">
                                    SALE {{ $product->discount }}%
                                </div>
                            @endif
                            @if ($product->quantity <= 0)
                                <div class="out-of-stock-overlay">
                                    <span>Đã bán hết</span>
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

        {{-- Phân trang --}}
        <div class="pagination-container">
            {{ $products->links() }}
        </div>
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
