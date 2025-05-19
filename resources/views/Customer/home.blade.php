<!DOCTYPE html>
<html>

<head>
    <title>Website bán đồ</title>
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

    <header>
        <div class="container">
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}">
                </div>

                <div class="menu"><a href="#menu" class="openicon">☰</a>
                    <nav id="menu">
                        <ul>
                            <li><a href="{{ route('shop.home') }}">Trang chủ</a></li>
                            <li><a href="#">Giới thiệu</a></li>
                            <li><a href="#">Mua ngay</a></li>
                            <li><a href="#">Danh mục</a></li>
                            @guest('customer')
                                <li><a href="{{ route('customer.login') }}">Đăng nhập</a></li>
                                <li><a href="{{ route('customer.register') }}">Đăng ký</a></li>
                            @else
                                <li class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle">
                                        <i class="lni lni-user"></i> Tài khoản của tôi
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
    </header>
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

            <div class="pro">
                <img src="images/p-1.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-2.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-3.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-4.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-5.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-6.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-7.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-8.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-9.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-10.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-11.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

            <div class="pro">
                <img src="images/p-12.jpg" class="w-100">
                <p>Laced Stylish Jooger For Men's</p>
                <p><b>$20.99</b> <strike>$30.99</strike> </p>
                <a href="#">Show Now</a>
            </div>

        </div>
    </div>
    <!----------PRODUCT HTML ENDS----->
    <!----------FOOTER HTML STARTS----->

    <footer>
        <div class="container">
            <div class="foot">
                <div class="column">
                    <h3>About Us</h3>
                    <p>If you are going to use of Lorem Ipsum need to be sure there isn't hidden of text. If you are
                        going to use of Lorem Ipsum need to be sure there isn't hidden of text If you are going to use
                        of Lorem Ipsum need to be sure there isn't hidden of text. </p>
                </div>
                <div class="column">
                    <h3>Opening Timing</h3>
                    <table>
                        <tr>
                            <td>Mon-Fri...................</td>
                            <td>10:00 AM - 08:00 PM</td>
                        </tr>
                        <tr>
                            <td>Saturday.................</td>
                            <td>8:00 AM - 02:00 PM</td>
                        </tr>
                        <tr>
                            <td>Sunday....................</td>
                            <td>Closed</td>
                        </tr>
                    </table>
                </div>
                <div class="column">
                    <h3>Contact Us</h3>
                    <ul class="contactus">
                        <li>3A-25 Ring road North-east, <br>
                            Delhi, India, 462001</li>
                        <li>info@ecommerce.com</li>
                        <li>+0 123 456 7890</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!----------FOOTER HTML ENDS----->
</body>
<script>
    $(document).ready(function() {
        $('.log-btn').click(function() {
            $('.log-status').addClass('wrong-entry');
            $('.alert').fadeIn(500);
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);

        });
        $('.form-control').keypress(function() {
            $('.log-status').removeClass('wrong-entry');
        });

    });
</script>

</html>
