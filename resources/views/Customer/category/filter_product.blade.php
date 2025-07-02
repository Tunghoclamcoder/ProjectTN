<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Danh sách sản phẩm')</title>
    <script src="{{ asset('js/alert.js') }}"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Add search CSS -->
    <link rel="stylesheet" href="{{ asset('css/category_products.css') }}">
</head>

<body>
    @section('title', content: $category->category_name)

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

    <div class="category-products-container">
        <!-- Category Header -->
        <div class="category-header">
            <div class="category-banner">
                @php
                    $defaultImages = [
                        'áo' => 'ao-thun.jpg',
                        'quần' => 'quan-jeans.jpg',
                        'giày' => 'giay.jpg',
                        'phụ kiện' => 'phu-kien.jpg',
                        'khoác' => 'ao-khoac.jpg',
                        'túi' => 'tui-deo.jpg',
                        'găng tay' => 'gang-tay.jpg',
                        'vợt' => 'vot.jpg',
                        'bóng' => 'bong-da.jpg',
                        'mũ' => 'mu.jpg',
                    ];

                    $imageName = 'default-category.jpg';
                    foreach ($defaultImages as $keyword => $image) {
                        if (stripos($category->category_name, $keyword) !== false) {
                            $imageName = $image;
                            break;
                        }
                    }
                @endphp

                <img src="{{ asset('images/categories/' . $imageName) }}" alt="{{ $category->category_name }}"
                    class="banner-image" onerror="this.style.display='none';">

                <div class="banner-overlay">
                    <div class="banner-content">
                        <nav class="breadcrumb">
                            <a href="{{ route('shop.home') }}">Trang chủ</a>
                            <span>/</span>
                            <a href="{{ route('categories.list') }}">Danh mục</a>
                            <span>/</span>
                            <span>{{ $category->category_name }}</span>
                        </nav>
                        <h1>{{ $category->category_name }}</h1>
                        <p>Khám phá bộ sưu tập {{ strtolower($category->category_name) }} đa dạng và chất lượng</p>
                        <div class="category-stats">
                            <span class="stat-item">
                                <i class="fas fa-box"></i>
                                {{ $products->total() }} sản phẩm
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
            <a class="nav-btn back-btn" href="{{ route('categories.list') }}">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại danh muc</span>
            </a>
            <a href="{{ route('shop.home') }}" class="nav-btn home-btn">
                <i class="fas fa-home"></i>
                <span>Trang chủ</span>
            </a>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            @if ($products->count() > 0)
                <div class="products-header">
                    <h2>Sản phẩm trong danh mục {{ $category->category_name }}</h2>
                    <div class="products-filter">
                        <select class="filter-select" onchange="sortProducts(this.value)">
                            <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Sắp xếp mặc
                                định</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp
                                đến cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao
                                đến thấp</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A-Z
                            </option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên: Z-A
                            </option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất
                            </option>
                        </select>
                    </div>
                </div>

                <div class="products-grid">
                    @foreach ($products as $product)
                        <div class="product-card">
                            <div class="product-image">
                                @if ($mainImage = $product->getMainImage())
                                    <img src="{{ asset($mainImage->image_url) }}" alt="{{ $product->product_name }}"
                                        onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i></div>';"
                                        style="max-width: 100%; height: auto;">
                                @else
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="product-info">
                                <div class="product-brand">Thương hiệu: {{ $product->brand->brand_name ?? 'N/A' }}
                                </div>
                                <h3 class="product-name">
                                    <a href="{{ route('shop.product.show', $product->product_id) }}">
                                        {{ $product->product_name }}
                                    </a>
                                </h3>
                                <div class="product-price">
                                    @if ($product->discount > 0)
                                        <span class="current-price">
                                            {{ number_format($product->price * (1 - $product->discount / 100)) }}đ
                                        </span>
                                        <del class="old-price"
                                            style="font-size: 14px; color: gray">{{ number_format($product->price) }}đ</del>
                                    @else
                                        <span class="current-price">{{ number_format($product->price) }}đ</span>
                                    @endif
                                </div>

                                <div class="product-actions-bottom">
                                    @auth('customer')
                                        <form class="add-to-cart-form d-inline"
                                            style="display: flex; justify-content: center;"
                                            data-product-id="{{ $product->product_id }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                            <button type="button" class="cart-button"
                                                {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                                <span
                                                    class="add-to-cart">{{ $product->quantity > 0 ? 'Thêm vào giỏ hàng' : 'Hết hàng' }}</span>
                                                <span class="added">Đã thêm !</span>
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('customer.login') }}" class="cart-button">
                                            <span class="add-to-cart">Đăng nhập để mua hàng</span>
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @else
                <div class="no-products">
                    <div class="no-products-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>Chưa có sản phẩm</h3>
                    <p>Danh mục {{ $category->category_name }} hiện chưa có sản phẩm nào.</p>
                    <a href="{{ route('categories.list') }}" class="back-categories-btn">
                        <i class="fas fa-arrow-left"></i>
                        Xem danh mục khác
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function sortProducts(sortBy) {
            const url = new URL(window.location);
            url.searchParams.set('sort', sortBy);
            window.location.href = url.toString();
        }

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

        // Smooth scroll to top when clicking navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navButtons = document.querySelectorAll('.nav-btn, .bottom-nav-btn');
            navButtons.forEach(button => {
                if (button.onclick || button.href) {
                    button.addEventListener('click', function() {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    });
                }
            });

        });
    </script>
</body>

</html>
