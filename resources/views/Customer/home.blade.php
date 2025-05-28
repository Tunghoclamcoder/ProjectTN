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

    <img src="{{ asset('images/slider.png') }}" class="w-100" alt="Slider">
    <div class="container">
        <div class="banner">
            <img src="{{ asset('images/banner-1.png') }}" class="w-100" alt="Banner 1">
            <img src="{{ asset('images/banner-2.png') }}" class="w-100" alt="Banner 2">
        </div>
    </div>
    <!----------PRODUCT HTML STARTS----->

    <div class="container">
        <div class="product">
            @forelse($products as $product)
                <div class="pro">
                    @if ($mainImage = $product->getMainImage())
                        <img src="{{ Storage::url($mainImage->image_url) }}" class="w-100"
                            alt="{{ $product->product_name }}">
                    @else
                        <img src="{{ asset('images/no-image.png') }}" class="w-100" alt="No image available">
                    @endif

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
                            <form action="{{ route('cart.add-to-cart') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                                <button class="cart-button">
                                    <span style="width: 100%" class="add-to-cart">Thêm vào giỏ hàng</span>
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
        // JS nút Add to cart
        const cartButtons = document.querySelectorAll('.cart-button');

        cartButtons.forEach(button => {
            button.addEventListener('click', cartClick);
        });

        function cartClick() {
            let button = this;
            button.classList.add('clicked');
        }
    </script>

</html>
</body>
