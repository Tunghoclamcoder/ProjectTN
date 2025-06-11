<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <title>Thương hiệu - Shop</title>
    <link rel="stylesheet" href="{{ asset('css/brand_list.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <div class="brand-page">
        <!-- Breadcrumb -->
        <section class="breadcrumb-section">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="{{ route('shop.home') }}" class="breadcrumb-item">
                        <i class="fas fa-home"></i>
                        Trang chủ
                    </a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current">Thương hiệu</span>
                </nav>
            </div>
        </section>

        <!-- Brands Grid Section -->
        <section class="brands-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title" style="color: #000000">Tất cả thương hiệu</h2>
                    <p class="section-subtitle">Chọn thương hiệu yêu thích của bạn để khám phá sản phẩm</p>
                </div>

                @if ($brands->count() > 0)
                    <div class="brands-grid">
                        @foreach ($brands as $index => $brand)
                            <div class="brand-card" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                <div class="brand-card-header">
                                    <div class="brand-logo-container">
                                        @php
                                            $displayImage = null;
                                            $imageType = 'placeholder';

                                            // Kiểm tra brand_image từ storage
                                            if ($brand->brand_image) {
                                                $displayImage = $brand->brand_image;
                                                $imageType = 'brand';
                                            } else {
                                                // Fallback to product image if no brand image
                                                $firstProduct = $brand->products->first();
                                                if ($firstProduct && $firstProduct->images->count() > 0) {
                                                    $mainImage = $firstProduct->images
                                                        ->where('pivot.image_role', 'main')
                                                        ->first();
                                                    if (!$mainImage) {
                                                        $mainImage = $firstProduct->images->first();
                                                    }
                                                    if ($mainImage && $mainImage->image_url) {
                                                        $displayImage = $mainImage->image_url;
                                                        $imageType = 'product';
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if ($displayImage)
                                            @if ($imageType === 'brand')
                                                <img src="{{ Storage::url($brand->brand_image) }}"
                                                    alt="{{ $brand->brand_name }}" class="brand-logo"
                                                    onerror="this.parentElement.innerHTML='<div class=\'brand-placeholder\'><i class=\'fas fa-building\'></i><span>{{ substr($brand->brand_name, 0, 2) }}</span></div>';">
                                            @else
                                                <img src="{{ Storage::url($displayImage) }}"
                                                    alt="{{ $brand->brand_name }}" class="brand-product-img"
                                                    onerror="this.parentElement.innerHTML='<div class=\'brand-placeholder\'><i class=\'fas fa-building\'></i><span>{{ substr($brand->brand_name, 0, 2) }}</span></div>';">
                                            @endif
                                            <div class="image-type-badge {{ $imageType }}">
                                                @if ($imageType === 'brand')
                                                    <i class="fas fa-certificate"></i>
                                                @else
                                                    <i class="fas fa-image"></i>
                                                @endif
                                            </div>
                                        @else
                                            <div class="brand-placeholder">
                                                <i class="fas fa-building"></i>
                                                <span>{{ substr($brand->brand_name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="brand-overlay">
                                        <div class="overlay-content">
                                            <i class="fas fa-arrow-right"></i>
                                            <span>Xem sản phẩm</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="brand-card-body">
                                    <div class="brand-info">
                                        <h3 class="brand-name">{{ $brand->brand_name }}</h3>
                                        <div class="brand-stats">
                                            <span class="product-count">
                                                <i class="fas fa-box"></i>
                                                {{ $brand->products_count }} sản phẩm
                                            </span>
                                        </div>
                                    </div>

                                    <div class="brand-description">
                                        @if ($brand->description)
                                            <p>{{ Str::limit($brand->description, 100) }}</p>
                                        @else
                                            <p>Khám phá bộ sưu tập đa dạng từ thương hiệu {{ $brand->brand_name }} với
                                                {{ $brand->products_count }} sản phẩm chất lượng cao.</p>
                                        @endif
                                    </div>

                                    <div class="brand-action">
                                        <a href="{{ route('brands.show', $brand->brand_id) }}" class="brand-btn">
                                            <span>Khám phá ngay</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-brands">
                        <div class="no-brands-illustration">
                            <i class="fas fa-store-slash"></i>
                        </div>
                        <h3>Chưa có thương hiệu nào</h3>
                        <p>Hiện tại chưa có thương hiệu nào được hiển thị.</p>
                        <a href="{{ route('shop.home') }}" class="btn-back-home">
                            <i class="fas fa-home"></i>
                            Về trang chủ
                        </a>
                    </div>
                @endif
            </div>
        </section>

        <!-- Featured Brands Section -->
        @if ($brands->count() >= 3)
            <section class="featured-brands-section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">Thương hiệu nổi bật</h2>
                        <p class="section-subtitle">Những thương hiệu có nhiều sản phẩm nhất</p>
                    </div>

                    <div class="featured-brands-slider">
                        @foreach ($brands->sortByDesc('products_count')->take(3) as $brand)
                            <div class="featured-brand-card">
                                <div class="featured-brand-bg">
                                    @php
                                        $featuredImage = null;
                                        if ($brand->brand_image) {
                                            $featuredImage = 'brands/' . $brand->brand_image;
                                        } else {
                                            $featuredProduct = $brand->products->first();
                                            if ($featuredProduct && $featuredProduct->images->count() > 0) {
                                                $mainImage = $featuredProduct->images
                                                    ->where('pivot.image_role', 'main')
                                                    ->first();
                                                if (!$mainImage) {
                                                    $mainImage = $featuredProduct->images->first();
                                                }
                                                if ($mainImage && $mainImage->image_url) {
                                                    $featuredImage = $mainImage->image_url;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if ($featuredImage)
                                        <img src="{{ asset('images/' . $featuredImage) }}"
                                            alt="{{ $brand->brand_name }}">
                                    @endif
                                    <div class="featured-overlay"></div>
                                </div>

                                <div class="featured-content">
                                    <div class="featured-badge">
                                        <i class="fas fa-star"></i>
                                        <span>Nổi bật</span>
                                    </div>
                                    <h4 class="featured-brand-name">{{ $brand->brand_name }}</h4>
                                    <p class="featured-brand-count">{{ $brand->products_count }} sản phẩm</p>
                                    @if ($brand->description)
                                        <p class="featured-brand-desc">{{ Str::limit($brand->description, 80) }}</p>
                                    @endif
                                    <a href="{{ route('brands.show', $brand->brand_id) }}" class="featured-btn">
                                        Khám phá collection
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Back to Top Button -->
        <button class="back-to-top" onclick="scrollToTop()">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Back to top functionality
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Show/hide back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        // Brand card hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const brandCards = document.querySelectorAll('.brand-card');

            brandCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>

</html>
