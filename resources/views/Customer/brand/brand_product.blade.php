<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <title>{{ $brand->brand_name }} - Sản phẩm</title>
    <link rel="stylesheet" href="{{ asset('css/brand_products.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="{{ asset('js/alert.js') }}"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
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

    <div class="brand-products-page">
        <!-- Brand Header Section -->
        <section class="brand-header">
            <div class="brand-banner">
                <div class="banner-background">
                    @if ($brand->brand_image)
                        <img src="{{ asset('images/brands/' . $brand->brand_image) }}" alt="{{ $brand->brand_name }}"
                            class="brand-bg-image">
                    @endif
                    <div class="banner-overlay"></div>
                </div>

                <div class="banner-content">
                    <div class="container">
                        <!-- Breadcrumb -->
                        <nav class="breadcrumb" style="color: black">
                            <a href="{{ route('shop.home') }}">
                                <i class="fas fa-home"></i>
                                Trang chủ
                            </a>
                            <span class="separator">/</span>
                            <a href="{{ route('brands.list') }}">Thương hiệu</a>
                            <span class="separator">/</span>
                            <span class="current">{{ $brand->brand_name }}</span>
                        </nav>

                        <!-- Brand Info -->
                        <div class="brand-info">
                            <div class="brand-details">
                                <h1 class="brand-title">{{ $brand->brand_name }}</h1>
                                @if ($brand->description)
                                    <p class="brand-description">{{ $brand->description }}</p>
                                @else
                                    <p class="brand-description">Khám phá bộ sưu tập đa dạng từ thương hiệu
                                        {{ $brand->brand_name }}</p>
                                @endif

                                <div class="brand-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-box"></i>
                                        <span class="stat-number">{{ $products->total() }}</span>
                                        <span class="stat-label">Sản phẩm</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-star"></i>
                                        <span class="stat-number">4.8</span>
                                        <span class="stat-label">Đánh giá</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Navigation Buttons -->
        <section class="navigation-section">
            <div class="container">
                <div class="nav-buttons">
                    <button type="button" class="nav-btn back-btn" onclick="goBack()">
                        <i class="fas fa-arrow-left"></i>
                        <span>Quay lại</span>
                    </button>
                    <a href="{{ route('shop.home') }}" class="nav-btn home-btn">
                        <i class="fas fa-home"></i>
                        <span>Trang chủ</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="products-section">
            <div class="container">
                @if ($products->count() > 0)
                    <!-- Products Header -->
                    <div class="products-header">
                        <div class="header-left">
                            <h2 class="products-title">Sản phẩm từ {{ $brand->brand_name }}</h2>
                            <p class="products-count">{{ $products->total() }} sản phẩm được tìm thấy</p>
                        </div>
                        <div class="header-right">
                            <div class="view-options">
                                <button class="view-btn active" data-view="grid">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button class="view-btn" data-view="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                            <div class="sort-options" style="font-size: 1.6rem">
                                <select class="sort-select" onchange="sortProducts(this.value)">
                                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Sắp
                                        xếp mặc định</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                        Giá: Thấp đến cao</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                        Giá: Cao đến thấp</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên:
                                        A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                                        Tên: Z-A</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="products-grid" id="productsContainer">
                        @foreach ($products as $index => $product)
                            <div class="product-card" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                                <div class="product-image-container">
                                    <div class="product-image">
                                        @php
                                            $mainImage = null;
                                            if ($product->images && $product->images->count() > 0) {
                                                $mainImage = $product->images
                                                    ->where('pivot.image_role', 'main')
                                                    ->first();
                                                if (!$mainImage) {
                                                    $mainImage = $product->images->first();
                                                }
                                            }
                                        @endphp

                                        @if ($mainImage && $mainImage->image_url)
                                            <img src="{{ asset($mainImage->image_url) }}"
                                                alt="{{ $product->product_name }}" loading="lazy"
                                                onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i><span>Không có ảnh</span></div>';">
                                        @else
                                            <div class="no-image">
                                                <i class="fas fa-image"></i>
                                                <span>Không có ảnh</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Discount Badge -->
                                    @if ($product->discount > 0)
                                        <div class="discount-badge">
                                            - {{ $product->discount }}%
                                        </div>
                                    @endif
                                </div>

                                <div class="product-info">
                                    <div class="product-brand">{{ $brand->brand_name }}</div>
                                    <h3 class="product-name">
                                        <a href="{{ route('shop.product.show', $product->product_id) }}">
                                            {{ $product->product_name }}
                                        </a>
                                    </h3>

                                    @if ($product->description)
                                        <p class="product-description">
                                            {{ Str::limit($product->description, 80) }}
                                        </p>
                                    @endif

                                    <div class="product-rating">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span class="rating-text">(4.2)</span>
                                    </div>

                                    <div class="product-price">
                                        @if ($product->discount > 0)
                                            <span class="original-price">{{ number_format($product->price) }}đ</span>
                                            <span
                                                class="current-price">{{ number_format($product->getDiscountedPrice()) }}đ</span>
                                        @else
                                            <span class="current-price">{{ number_format($product->price) }}đ</span>
                                        @endif
                                    </div>

                                    <div class="product-meta">
                                        <span
                                            class="stock-status {{ $product->quantity > 0 ? 'in-stock' : 'out-stock' }}">
                                            <i class="fas fa-{{ $product->quantity > 0 ? 'check' : 'times' }}"></i>
                                            {{ $product->quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                        </span>
                                    </div>

                                    <div class="product-actions-bottom">
                                        @auth('customer')
                                            <form class="add-to-cart-form d-inline" style="display: flex; justify-content: center;"
                                                data-product-id="{{ $product->product_id }}">
                                                @csrf
                                                <input type="hidden" name="product_id"
                                                    value="{{ $product->product_id }}">
                                                <button type="button" class="cart-button"
                                                    {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                                    <span
                                                        class="add-to-cart">{{ $product->quantity > 0 ? 'Thêm vào giỏ hàng' : 'Hết hàng' }}</span>
                                                    <span class="added">Đã thêm !</span>
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <i class="fas fa-box"></i>
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
                    <div class="pagination-section">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- No Products -->
                    <div class="no-products">
                        <div class="no-products-illustration">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h3>Không có sản phẩm</h3>
                        <p>Thương hiệu {{ $brand->brand_name }} hiện chưa có sản phẩm nào.</p>
                        <div class="no-products-actions">
                            <a href="{{ route('brands.list') }}" class="btn-primary">
                                <i class="fas fa-building" style="margin-right: 8px"></i>
                                Xem thương hiệu khác
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Back to Top -->
        <button class="back-to-top" onclick="scrollToTop()">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // View toggle
        document.addEventListener('DOMContentLoaded', function() {
            const viewBtns = document.querySelectorAll('.view-btn');
            const productsContainer = document.getElementById('productsContainer');

            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    viewBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const view = this.dataset.view;
                    productsContainer.className = view === 'list' ? 'products-list' :
                        'products-grid';
                });
            });

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

        // Functions
        function goBack() {
            if (document.referrer && document.referrer.includes(window.location.host)) {
                window.history.back();
            } else {
                window.location.href = "{{ route('brands.list') }}";
            }
        }

        function sortProducts(sortBy) {
            const url = new URL(window.location);
            url.searchParams.set('sort', sortBy);
            window.location.href = url.toString();
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Back to top button visibility
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
    </script>
</body>

</html>
