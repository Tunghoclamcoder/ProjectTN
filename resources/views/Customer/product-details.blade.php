<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết sản phẩm</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
</head>

<body>
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
            <h3>{{ $product->product_name }}</h3>

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
                <p><strong>Thương hiệu:</strong> {{ $product->brand->brand_name }}</p>
                <p><strong>Chất liệu:</strong> {{ $product->material->material_name }}</p>
                <p>{{ $product->description }}</p>
            </div>

            <form action="{{ route('cart.add-to-cart') }}" method="POST" class="product-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                <div class="sizes">
                    <p>Size:</p>
                    <select name="size_id" id="size" class="size-option" required>
                        <option value="">Chọn size</option>
                        @foreach ($product->sizes as $size)
                            <option value="{{ $size->size_id }}">{{ $size->size_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="quantity">
                    <label>Số lượng:</label>
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}">
                    <button type="submit" class="add-to-cart">
                        <i class="lni lni-cart"></i> Thêm vào giỏ
                    </button>
                </div>
            </form>

            <div class="shipping-info">
                <p><strong>Thông tin vận chuyển:</strong></p>
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

<style>

    header {
        margin-bottom: 20px;
    }

    .product-container {
        display: flex;
        justify-content: start;
        align-items: start;
        gap: 40px;
    }

    /* .img-card{
    width: 40%;
} */

    .img-card img {
        width: 100%;
        flex-shrink: 0;
        border-radius: 4px;
        height: 520px;
        object-fit: cover;
    }

    .small-Card {
        display: flex;
        justify-content: start;
        align-items: center;
        margin-top: 15px;
        gap: 12px;
    }

    .small-Card img {
        width: 104px;
        height: 104px;
        border-radius: 4px;
        cursor: pointer;
    }

    .small-Card img:active {
        border: 1px solid #17696a;
    }

    .sm-card {
        border: 2px solid darkred;
    }

    .product-info {
        width: 60%;
    }

    .product-info h3 {
        font-size: 32px;
        font-family: Lato;
        font-weight: 600;
        line-height: 130%;
    }

    .product-info h5 {
        font-size: 24px;
        font-family: Lato;
        font-weight: 500;
        line-height: 130%;
        color: #ff4242;
        margin: 6px 0;
    }

    .product-info del {
        color: #a9a9a9;
    }

    .product-info p {
        color: #424551;
        margin: 15px 0;
        width: 70%;
    }

    .sizes p {
        font-size: 22px;
        color: black;
    }

    .size-option {
        width: 200px;
        height: 30px;
        margin-bottom: 15px;
        padding: 5px;
    }

    .quantity input {
        width: 51px;
        height: 33px;
        margin-bottom: 15px;
        padding: 6px;
    }

    button {
        background: #17696a;
        border-radius: 4px;
        padding: 10px 37px;
        border: none;
        color: white;
        font-weight: 600;
    }

    button:hover {
        background: #ff4242;
        transition: ease-in 0.4s;
    }

    .delivery {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 70%;
        color: #787a80;
        font-size: 12px;
        font-family: Lato;
        line-height: 150%;
        letter-spacing: 1px;
    }

    hr {
        color: #787a80;
        width: 58%;
        opacity: 0.67;
    }

    .pagination {
        color: #787a80;
        margin: 15px 0;
        cursor: pointer;
    }

    @media screen and (max-width: 576px) {
        .product-container {
            flex-direction: column;
        }

        .small-Card img {
            width: 80px;
        }

        .product-info {
            width: 100%;
        }

        echo "# product-details-page-html-css-js">>README.md .product-info p {
            width: 100%;
        }

        .delivery {
            width: 100%;
        }

        hr {
            width: 100%;
        }
    }
</style>
<script>
    let featuedImg = document.getElementById('featured-image');
    let smallImgs = document.getElementsByClassName('small-Img');

    smallImgs[0].addEventListener('click', () => {
        featuedImg.src = smallImgs[0].src;
        smallImgs[0].classList.add('sm-card')
        smallImgs[1].classList.remove('sm-card')
        smallImgs[2].classList.remove('sm-card')
        smallImgs[3].classList.remove('sm-card')
        smallImgs[4].classList.remove('sm-card')
    })
    smallImgs[1].addEventListener('click', () => {
        featuedImg.src = smallImgs[1].src;
        smallImgs[0].classList.remove('sm-card')
        smallImgs[1].classList.add('sm-card')
        smallImgs[2].classList.remove('sm-card')
        smallImgs[3].classList.remove('sm-card')
        smallImgs[4].classList.remove('sm-card')
    })
    smallImgs[2].addEventListener('click', () => {
        featuedImg.src = smallImgs[2].src;
        smallImgs[0].classList.remove('sm-card')
        smallImgs[1].classList.remove('sm-card')
        smallImgs[2].classList.add('sm-card')
        smallImgs[3].classList.remove('sm-card')
        smallImgs[4].classList.remove('sm-card')
    })
    smallImgs[3].addEventListener('click', () => {
        featuedImg.src = smallImgs[3].src;
        smallImgs[0].classList.remove('sm-card')
        smallImgs[1].classList.remove('sm-card')
        smallImgs[2].classList.remove('sm-card')
        smallImgs[3].classList.add('sm-card')
        smallImgs[4].classList.remove('sm-card')
    })
    smallImgs[4].addEventListener('click', () => {
        featuedImg.src = smallImgs[4].src;
        smallImgs[0].classList.remove('sm-card')
        smallImgs[1].classList.remove('sm-card')
        smallImgs[2].classList.remove('sm-card')
        smallImgs[3].classList.remove('sm-card')
        smallImgs[4].classList.add('sm-card')
    })
</script>
