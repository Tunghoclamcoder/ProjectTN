<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết sản phẩm</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
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

    <div class="pagination">
        <p>Trang chủ > {{ $product->categories->first()->category_name ?? 'Sản phẩm' }} > {{ $product->product_name }}
        </p>
    </div>

    <section class="product-container">
        <!-- Left side - Product Images -->
        <div class="img-card" style="min-width: 35%;">
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

            @if ($product->quantity > 0)
                @auth('customer')
                    <form action="{{ route('cart.add-to-cart') }}" method="POST" class="product-form" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <input type="hidden" name="size_id" id="selected_size_id">
                        <div class="quantity">
                            <label style="color: #000000">Số lượng:</label>
                            <input type="number" name="quantity" value="1" min="1"
                                max="{{ $product->quantity }}">
                            <button type="submit" class="add-to-cart">
                                <i class="lni lni-cart"></i> Thêm vào giỏ
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning mt-2">
                        Vui lòng <a href="{{ route('customer.login') }}">đăng nhập</a> để thêm sản phẩm vào giỏ hàng.
                    </div>
                @endauth
            @else
                <div class="out-of-stock-message">
                    <span class="text-danger">
                        Sản phẩm đã hết hàng
                    </span>
                </div>
            @endif

            <div class="shipping-info">
                <p><strong style="color: blue">Thông tin vận chuyển:</strong></p>
                <p>Miễn phí vận chuyển cho đơn hàng trên 500.000đ</p>

                <div class="delivery">
                    <p>HÌNH THỨC</p>
                    <p>PHÍ VẬN CHUYỂN</p>
                </div>

                @forelse($shippingMethods as $method)
                    <hr>
                    <div class="delivery">
                        <p>{{ $method->method_name }}</p>
                        <p>{{ number_format($method->shipping_fee) }}đ</p>
                    </div>
                @empty
                    <hr>
                    <div class="delivery">
                        <p>Giao hàng tiêu chuẩn</p>
                        <p>30.000đ</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- @push('scripts')
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
    @endpush --}}
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý ảnh
        const featuredImage = document.getElementById('featured-image');
        const smallImages = document.querySelectorAll('.small-Img');

        smallImages.forEach(smallImg => {
            smallImg.addEventListener('click', function() {
                featuredImage.src = this.src;
                smallImages.forEach(img => {
                    img.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Xử lý form add to cart
        const form = document.getElementById('addToCartForm');
        const sizeSelect = document.getElementById('size');
        const selectedSizeInput = document.getElementById('selected_size_id');

        // Set initial size value
        if (sizeSelect && selectedSizeInput) {
            selectedSizeInput.value = sizeSelect.value;

            // Update size when changed
            sizeSelect.addEventListener('change', function() {
                selectedSizeInput.value = this.value;
            });
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật số lượng giỏ hàng
                            const cartCountElement = document.querySelector('.cart-count');
                            if (cartCountElement && data.cartCount) {
                                cartCountElement.textContent = data.cartCount;
                            }
                            // Hiển thị thông báo thành công
                            alert(data.message);
                        } else {
                            // Xử lý khi có lỗi
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                alert(data.message || 'Có lỗi xảy ra');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
                    });
            });
        }
    });
</script>
