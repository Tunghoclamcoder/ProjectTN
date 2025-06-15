<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop')</title>

    <!-- Add search CSS -->
</head>

<body>
    @section('title', 'Kết quả tìm kiếm')

    <div class="container-fluid">
        <div class="search-header">
            <div class="search-title">
                Kết quả tìm kiếm cho "{{ $query }}"
            </div>
            <div class="search-meta">
                Tìm thấy {{ $products->count() }} sản phẩm
            </div>
        </div>

        <div class="product-grid">
            @forelse ($products as $product)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ $product->getMainImage() ? asset('storage/' . $product->getMainImage()->image_url) : asset('images/no-image.png') }}"
                            alt="{{ $product->product_name }}">
                    </div>
                    <div class="product-details">
                        <div class="product-category">
                            {{ $product->getPrimaryCategory()?->category_name ?? 'Chưa phân loại' }}
                        </div>
                        <h3 class="product-name">{{ $product->product_name }}</h3>
                        <div class="product-brand">
                            <i class="fas fa-tag me-1"></i>
                            {{ $product->brand?->brand_name ?? 'Không có thương hiệu' }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="product-price">
                                {{ number_format($product->price) }} VNĐ
                            </div>
                            <span class="product-status {{ $product->status ? 'status-active' : 'status-inactive' }}">
                                {{ $product->status ? 'Đang bán' : 'Ngừng bán' }}
                            </span>
                        </div>
                    </div>
                    <div class="product-actions">
                        <a href="{{ route('admin.product.edit', $product->product_id) }}"
                            class="action-button edit-button">
                            <i class="fas fa-edit me-1"></i>
                            Chỉnh sửa
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
<style>
    .search-results-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .search-query {
        color: #4361ee;
        font-weight: 600;
    }

    .search-count {
        color: #718096;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 1rem;
    }

    .product-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        position: relative;
        padding-top: 75%;
        /* 4:3 Aspect Ratio */
        background: #f8f9fa;
        overflow: hidden;
    }

    .product-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .product-details {
        padding: 1.25rem;
    }

    .product-category {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0.5rem 0;
        line-height: 1.4;
    }

    .product-brand {
        font-size: 0.9rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #48bb78;
    }

    .product-status {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .product-actions {
        padding: 1rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .action-button {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .edit-button {
        background: #e9ecef;
        color: #495057;
    }

    .edit-button:hover {
        background: #dee2e6;
        color: #212529;
    }

    .search-header {
        background: linear-gradient(to right, #4a90e2, #63b3ed);
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        color: white;
    }

    .search-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .search-meta {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>

</html>
