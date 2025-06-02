<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="{{ asset('js/alert.js') }}"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<header>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}">
            </div>

            <div class="search-container">
                <form action="{{ route('products.search') }}" method="GET" class="search-form">
                    <div class="search-box">
                        <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..."
                            value="{{ request('query') }}" class="search-input">
                        <button type="submit" class="search-btn">
                            <i class="lni lni-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="menu"><a href="#menu" class="openicon">☰</a>
                <nav id="menu">
                    <ul>
                        <li><a href="{{ route('shop.home') }}">Trang chủ</a></li>
                        <li><a href="#">Giới thiệu</a></li>
                        @auth('customer')
                            <li>
                                <a href="{{ route('cart.view') }}">
                                    <i class="lni lni-shopping-basket"></i> Mua ngay
                                </a>
                            </li>
                        @endauth
                        <li><a href="{{ route(name: 'categories.list') }}">Danh mục</a></li>
                        <li><a href="{{ route(name: 'brands.list') }}">Thương hiệu</a></li>
                        @guest('customer')
                            <li><a href="{{ route('customer.login') }}">Đăng nhập</a></li>
                            <li><a href="{{ route('customer.register') }}">Đăng ký</a></li>
                        @else
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle">
                                    <i class="lni lni-user"></i> Tài khoản
                                </a>
                                <div class="dropdown-menu">
                                    <a href="{{ route('customer.profile') }}" class="dropdown-item">
                                        <i class="lni lni-user"></i> Thông tin tài khoản
                                    </a>
                                    <a href="{{ route('customer.orders') }}" class="dropdown-item">
                                        <i class="lni lni-shopping-basket"></i> Đơn hàng của tôi
                                    </a>
                                    <form action="{{ route('customer.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="lni lni-exit"></i> Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endguest
                        <li><a href="#">Liên hệ với chúng tôi</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
