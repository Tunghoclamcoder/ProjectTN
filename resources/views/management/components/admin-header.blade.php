<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon" />
    <title>Admin Dashboard</title>

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/materialdesignicons.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
</head>

<header class="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-6">
                <div class="header-left d-flex align-items-center">
                    <div class="menu-toggle-btn mr-15">
                        <button id="menu-toggle" class="main-btn primary-btn btn-hover">
                            <i class="lni lni-chevron-left me-2"></i> Menu
                        </button>
                    </div>
                    <div class="header-search d-none d-md-flex">
                        <form action="{{ route('admin.search') }}" method="GET" id="adminSearchForm">
                            <input type="text" name="query" id="adminSearchInput"
                                placeholder="Tìm kiếm sản phẩm..." autocomplete="off" />
                            <button type="submit">
                                <i class="lni lni-search-alt"></i>
                            </button>
                        </form>
                        <div id="adminSearchSuggestions" class="search-suggestions"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-6">
                <div class="header-right">
                    <!-- profile start -->
                    <div class="profile-box ml-15">
                        <button class="dropdown-toggle bg-transparent border-0" type="button" id="profile"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-info">
                                <div class="info">
                                    <div class="image">
                                        <img src="{{ asset('images/GloryMU.jpg') }}" alt="" />
                                    </div>
                                    <div>
                                        @if (Auth::guard('owner')->check())
                                            <h6 class="fw-500">{{ Auth::guard('owner')->user()->owner_name }}
                                            </h6>
                                            <p>Chủ cửa hàng</p>
                                        @elseif(Auth::guard('employee')->check())
                                            <h6 class="fw-500">
                                                {{ Auth::guard('employee')->user()->employee_name }}</h6>
                                            <p>Nhân viên cửa hàng</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile">
                            <li>
                                <div class="author-info flex items-center !p-1">
                                    <div class="image">
                                        <img src="{{ asset('images/GloryMU.jpg') }}" alt="" />
                                    </div>
                                    <div class="content">
                                        @if (Auth::guard('owner')->check())
                                            <h4 class="text-sm">{{ Auth::guard('owner')->user()->owner_name }}
                                            </h4>
                                            <a class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white text-xs"
                                                href="#">{{ Auth::guard('owner')->user()->email }}</a>
                                        @elseif(Auth::guard('employee')->check())
                                            <h4 class="text-sm">
                                                {{ Auth::guard('employee')->user()->employee_name }}</h4>
                                            <a class="text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white text-xs"
                                                href="#">{{ Auth::guard('employee')->user()->email }}</a>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="{{ route('admin.profile.show') }}">
                                    <i class="lni lni-user"></i> Xem Profile
                                </a>
                            </li>
                            <li>
                                <a href="#0">
                                    <i class="lni lni-alarm"></i> Notifications
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.change-password') }}">
                                    <i class="lni lni-key"></i> Đổi mật khẩu
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
                                    @csrf
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                                        <i class="lni lni-exit"></i> Đăng xuất
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <!-- profile end -->
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
    }

    .suggestion-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .suggestion-item:hover {
        background-color: #f8f9fa;
    }

    .suggestion-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        margin-right: 12px;
        border-radius: 4px;
    }

    .suggestion-info {
        flex: 1;
    }

    .suggestion-name {
        font-weight: 500;
        margin-bottom: 4px;
        color: #333;
    }

    .suggestion-meta {
        font-size: 0.85em;
        color: #666;
        display: flex;
        justify-content: space-between;
    }

    .no-results {
        color: #666;
        text-align: center;
        padding: 16px;
    }

    #adminSearchSuggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
    }
</style>

<script>

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            return new Promise((resolve) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => resolve(func.apply(this, args)), wait);
            });
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('adminSearchInput');
        const searchForm = document.getElementById('adminSearchForm');
        const suggestionsBox = document.getElementById('adminSearchSuggestions');
        let currentSearchTerm = '';

        const updateSuggestions = (products, query) => {
            suggestionsBox.innerHTML = '';

            if (products && products.length > 0) {
                products.forEach(product => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.innerHTML = `
                    <div class="d-flex align-items-center">
                        <img src="${product.image_url}" alt="${product.name}" class="suggestion-image">
                        <div class="suggestion-info">
                            <div class="suggestion-name">${product.name}</div>
                            <div class="suggestion-meta">
                                <span class="suggestion-category">${product.category}</span>
                                <span class="suggestion-price">${product.price}</span>
                            </div>
                        </div>
                    </div>
                `;

                    div.addEventListener('click', () => {
                        searchInput.value = product.name;
                        suggestionsBox.style.display = 'none';
                        searchForm.submit();
                    });

                    suggestionsBox.appendChild(div);
                });
            } else {
                suggestionsBox.innerHTML =
                    '<div class="suggestion-item no-results">Không tìm thấy kết quả</div>';
            }

            if (query === currentSearchTerm) {
                suggestionsBox.style.display = 'block';
            }
        };

        const searchProducts = async (query) => {
            try {
                const response = await fetch(
                    `/admin/search/suggestions?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                console.log('Search response:', data); // Debug

                if (!data.products) {
                    console.warn('Invalid response format');
                    return [];
                }

                return data.products; // Thêm dòng này để trả về dữ liệu products
            } catch (error) {
                console.error('Search error:', error);
                return [];
            }
        };

        const performSearch = debounce(async (query) => {
            currentSearchTerm = query;
            console.log('Searching for:', query); // Debug

            if (query.length < 2) {
                suggestionsBox.style.display = 'none';
                return;
            }

            const products = await searchProducts(query);
            console.log('Received products:', products); // Debug

            if (query === currentSearchTerm) {
                updateSuggestions(products, query);
            }
        }, 600);

        searchInput.addEventListener('input', async function() {
            suggestionsBox.innerHTML = '<div class="searching">Đang tìm kiếm...</div>';
            const query = this.value.trim();
            await performSearch(query);
        });

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length > 0) {
                window.location.href = `/admin/search?query=${encodeURIComponent(query)}`;
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
    });
</script>
