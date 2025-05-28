<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết sản phẩm</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
    <script src="{{ asset('js/alert.js') }}"></script>
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

    <div class="pagination">
        <p>Trang chủ > {{ $product->categories->first()->category_name ?? 'Sản phẩm' }} > {{ $product->product_name }}
        </p>
    </div>

    <section class="product-container">
        <!-- Left side - Product Images -->
        <div class="img-card">
            @if ($mainImage)
                <img src="{{ Storage::url($mainImage->image_url) }}" alt="{{ $product->product_name }}"
                    id="featured-image">
            @endif

            <div class="small-Card">
                @if ($mainImage)
                    <img src="{{ Storage::url($mainImage->image_url) }}" alt="Main" class="small-Img active">
                @endif

                @foreach ($subImages as $image)
                    <img src="{{ Storage::url($image->image_url) }}" alt="Sub image" class="small-Img">
                @endforeach
            </div>
        </div>

        <!-- Right side - Product Info -->
        <div class="product-info">
            <h3 style="color: #000000">{{ $product->product_name }}</h3>

            <div class="price-info">
                @if ($product->discount > 0)
                    <h5>Giá: {{ number_format($product->price * (1 - $product->discount / 100)) }}đ
                        <del>{{ number_format($product->price) }}đ</del>
                        <span class="discount-badge">-{{ $product->discount }}%</span>
                    </h5>
                @else
                    <h5>Giá: {{ number_format($product->price) }}đ</h5>
                @endif
            </div>

            <div class="product-details">
                <p><strong style="color: #000000">Thương hiệu:</strong> {{ $product->brand->brand_name }}</p>
                <p><strong style="color: #000000">Chất liệu:</strong> {{ $product->material->material_name }}</p>
                <p>{{ $product->description }}</p>
            </div>

            <div class="sizes">
                <p>Size:</p>
                <select name="size_id" id="size" class="size-option" required>
                    @foreach ($product->sizes as $size)
                        <option style="color: #000000" value="{{ $size->size_id }}">{{ $size->size_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <form action="{{ route('cart.add-to-cart') }}" method="POST" class="product-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                <div class="quantity">
                    <label style="color: #000000">Số lượng:</label>
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}">
                    <button type="submit" class="add-to-cart">
                        <i class="lni lni-cart"></i> Thêm vào giỏ
                    </button>
                </div>
            </form>

            <div class="shipping-info">
                <p><strong style="color: blue">Thông tin vận chuyển:</strong></p>
                <p>Miễn phí vận chuyển cho đơn hàng trên 500.000đ</p>
                <div class="delivery">
                    <p>HÌNH THỨC</p>
                    <p>THỜI GIAN</p>
                    <p>PHÍ VẬN CHUYỂN</p>
                </div>
                <hr>
                <div class="delivery">
                    <p>Giao hàng tiêu chuẩn</p>
                    <p>3-5 ngày</p>
                    <p>30.000đ</p>
                </div>
                <hr>
                <div class="delivery">
                    <p>Giao hàng nhanh</p>
                    <p>1-2 ngày</p>
                    <p>45.000đ</p>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const featuredImg = document.getElementById('featured-image');
            const smallImgs = document.getElementsByClassName('small-Img');

            Array.from(smallImgs).forEach((img, index) => {
                img.addEventListener('click', () => {
                    featuredImg.src = img.src;
                    Array.from(smallImgs).forEach((otherImg) => {
                        otherImg.classList.remove('active');
                    });
                    img.classList.add('active');
                });
            });
        </script>
    @endpush
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const featuredImage = document.getElementById('featured-image');
        const smallImages = document.querySelectorAll('.small-Img');

        smallImages.forEach(smallImg => {
            smallImg.addEventListener('click', function() {
                // Thay đổi ảnh chính
                featuredImage.src = this.src;

                // Xóa class active từ tất cả ảnh nhỏ
                smallImages.forEach(img => {
                    img.classList.remove('active');
                });

                // Thêm class active cho ảnh được click
                this.classList.add('active');
            });
        });
    });
</script>
