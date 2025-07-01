<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop')</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/search-admin.css') }}" />

    <!-- Add search CSS -->
</head>

<body>
    @include('management.components.admin-header')

    @section('title', 'Kết quả tìm kiếm')

    <div class="container-fluid">
        <div class="search-header">
            <div class="search-title">
                Kết quả tìm kiếm cho "{{ $query }}"
            </div>
            <div class="search-meta">
                Tìm thấy {{ $products->count() }} sản phẩm
            </div>

            <div class="come-back-button">
                <a href="{{ route('admin.dashboard') }}" class="btn back-btn">
                    <i class="fa fa-arrow-left" style="margin-right: 5px"></i>
                    <span style="font-size: 12px; font-weight: 500"> Quay lại Dashboard</span>
                </a>
            </div>
        </div>

        <div class="product-grid">
            @forelse ($products as $product)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ $product->getMainImage() ? asset($product->getMainImage()->image_url) : asset('images/no-image.png') }}"
                            alt="{{ $product->product_name }}">
                    </div>
                    <div class="product-details">
                        <div class="product-category">
                            {{ $product->getPrimaryCategory()?->category_name ?? 'Chưa phân loại' }}
                        </div>
                        <h3 class="product-name">{{ $product->product_name }}</h3>
                        <div class="product-brand">
                            Thương hiệu:
                            {{ $product->brand?->brand_name ?? 'Không có thương hiệu' }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="product-price">
                                @if ($product->discount > 0)
                                    <span class="original-price text-decoration-line-through text-muted">
                                        {{ number_format($product->price) }} VNĐ
                                    </span>
                                    <span class="discounted-price text-danger">
                                        {{ number_format($product->price * (1 - $product->discount / 100)) }} VNĐ
                                    </span>
                                @else
                                    <span>{{ number_format($product->price) }} VNĐ</span>
                                @endif
                            </div>
                            <span class="product-status {{ $product->status ? 'status-active' : 'status-inactive' }}">
                                {{ $product->status ? 'Đang bán' : 'Ngừng bán' }}
                            </span>
                        </div>
                    </div>
                    <div class="product-actions">
                        <a href="{{ route('admin.product.edit', $product->product_id) }}"
                            class="action-button edit-button"> Chỉnh sửa
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Không tìm thấy sản phẩm nào</h4>
                        <p class="text-muted">Vui lòng thử lại với từ khóa khác</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</body>

</html>
