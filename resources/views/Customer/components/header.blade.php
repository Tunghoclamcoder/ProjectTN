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
                <a href="{{ route('shop.home') }}">
                    <img src="{{ asset('images/logo.png') }}">
                </a>
            </div>

            <div class="search-container">
                <form action="{{ route('products.search') }}" method="GET" class="search-form" id="searchForm">
                    <div class="search-box">
                        <input type="text" name="query" id="searchInput" placeholder="Tìm kiếm sản phẩm..."
                            value="{{ request('query') }}" class="search-input" autocomplete="off">
                        <button type="submit" class="search-btn">
                            <i class="lni lni-search"></i>
                        </button>
                    </div>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </form>
            </div>

            <div class="menu"><a href="#menu" class="openicon">☰</a>
                <nav id="menu">
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        @auth('customer')
                            <li>
                                <a href="{{ route('cart.view') }}">
                                    <i class="lni lni-shopping-basket"></i> Giỏ hàng
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
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="lni lni-user"></i>
                                    @auth('customer')
                                        {{ Auth::guard('customer')->user()->customer_name }}
                                    @else
                                        Tài khoản
                                    @endauth
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const suggestionsContainer = document.getElementById('searchSuggestions');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/search-suggestions?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(suggestions => {
                    suggestionsContainer.innerHTML = '';

                    if (suggestions.length > 0) {
                        suggestions.forEach(suggestion => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.textContent = suggestion;
                            div.addEventListener('click', () => {
                                searchInput.value = suggestion;
                                searchForm.submit();
                            });
                            suggestionsContainer.appendChild(div);
                        });
                        suggestionsContainer.style.display = 'block';
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchForm.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
});
</script>
