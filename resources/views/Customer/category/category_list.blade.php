<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop')</title>

    <!-- Add search CSS -->
    <link rel="stylesheet" href="{{ asset('css/category_list.css') }}">
</head>

<body>
    @section('title', 'Danh mục sản phẩm')

    <div class="category-list-container">
        <!-- Hero Banner -->

        <!-- Categories Section -->
        <div class="categories-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Tất cả danh mục</h2>
                    <p class="section-subtitle">Chọn danh mục để khám phá những sản phẩm tuyệt vời</p>
                </div>

                @if ($categories->count() > 0)
                    <div class="categories-grid">
                        @foreach ($categories as $index => $category)
                            <div class="category-card" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                <div class="card-inner">
                                    <div class="category-image-wrapper">
                                        <div class="category-image">
                                            {{-- Sử dụng ảnh mặc định dựa trên tên danh mục --}}
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
                                                    'yoga' => 'yoga.jpg',
                                                    'bảo hộ' => 'bao-ho.jpg',
                                                    'tank-top' => 'tank-top.jpg',
                                                    't-shirt' => 't-shirt.jpg',
                                                    'gym' => 'gym.jpg',
                                                    'quần' => 'quan-short.jpg',
                                                    'polo' => 'polo.jpg',
                                                ];

                                                $imageName = 'default-category.jpg';
                                                foreach ($defaultImages as $keyword => $image) {
                                                    if (stripos($category->category_name, $keyword) !== false) {
                                                        $imageName = $image;
                                                        break;
                                                    }
                                                }
                                            @endphp

                                            <img src="{{ asset('images/categories/' . $imageName) }}"
                                                alt="{{ $category->category_name }}"
                                                onerror="this.parentElement.innerHTML='<div class=\'default-category-image\'><i class=\'fas fa-tags\'></i></div>';">
                                        </div>
                                        <div class="image-overlay">
                                            <div class="overlay-content">
                                                <i class="fas fa-arrow-right"></i>
                                                <span>Khám phá ngay</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="category-content">
                                        <div class="category-header">
                                            <h3 class="category-name">{{ $category->category_name }}</h3>
                                            <div class="category-badge">
                                                <span class="product-count">{{ $category->products_count }} SP</span>
                                            </div>
                                        </div>

                                        <p class="category-description">
                                            Khám phá những sản phẩm chất lượng cao trong danh mục
                                            {{ $category->category_name }}.
                                            Đa dạng mẫu mã, phong cách và giá cả phù hợp.
                                        </p>

                                        <div class="category-footer">
                                            <a href="{{ route('categories.show', $category->category_id) }}"
                                                class="view-category-btn">
                                                <span>Xem sản phẩm</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-categories">
                        <div class="no-categories-illustration">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h3>Chưa có danh mục nào</h3>
                        <p>Hiện tại chưa có danh mục sản phẩm nào được hiển thị.</p>
                        <a href="{{ route('shop.home') }}" class="back-home-btn">
                            <i class="fas fa-home"></i>
                            Về trang chủ
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Featured Categories -->
        @if ($categories->count() >= 3)
            <div class="featured-section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title">Danh mục nổi bật</h2>
                        <p class="section-subtitle">Những danh mục có nhiều sản phẩm nhất</p>
                    </div>

                    <div class="featured-grid">
                        @foreach ($categories->sortByDesc('products_count')->take(3) as $category)
                            <div class="featured-card">
                                <div class="featured-image">
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
                                            'yoga' => 'yoga.jpg',
                                            'bảo hộ' => 'bao-ho.jpg',
                                            'tank-top' => 'tank-top.jpg',
                                            't-shirt' => 't-shirt.jpg',
                                            'gym' => 'gym.jpg',
                                        ];

                                        $imageName = 'default-category.jpg';
                                        foreach ($defaultImages as $keyword => $image) {
                                            if (stripos($category->category_name, $keyword) !== false) {
                                                $imageName = $image;
                                                break;
                                            }
                                        }
                                    @endphp

                                    <img src="{{ asset('images/categories/' . $imageName) }}"
                                        alt="{{ $category->category_name }}"
                                        onerror="this.parentElement.innerHTML='<div class=\'default-featured-image\'><i class=\'fas fa-star\'></i></div>';">

                                    <div class="featured-overlay">
                                        <div class="featured-badge">
                                            <i class="fas fa-crown"></i>
                                            <span>Nổi bật</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <h4>{{ $category->category_name }}</h4>
                                    <p>{{ $category->products_count }} sản phẩm có sẵn</p>
                                    <a href="{{ route('categories.show', $category->category_id) }}"
                                        class="featured-btn">
                                        Khám phá ngay
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
</body>

</html>
