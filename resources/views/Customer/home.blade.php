<!DOCTYPE html>
<html>

<head>
    <title>Website bán đồ</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>
    <header>
        <div class="container">
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}">
                </div>

                <div class="menu"><a href="#menu" class="openicon">☰</a>
                    <nav id="menu">
                        <a href="#" class="closeicon">✕</a>
                        <ul>
                            <li>Home</li>
                            <li>About Us</li>
                            <li>Shop Now</li>
                            <li>Category</li>
                            <li>Register</li>
                            <li>Contact us</li>
                        </ul>
                    </nav>
                </div>
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

</html>
