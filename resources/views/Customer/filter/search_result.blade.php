<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop')</title>

    <!-- Add search CSS -->
    <link rel="stylesheet" href="{{ asset('css/search_result.css') }}">
</head>

<body>
    @section('title', 'Kết quả tìm kiếm: ' . $query)

    <div class="search-results-container">
        <div class="search-header">
            <h2>Kết quả tìm kiếm cho: "{{ $query }}"</h2>
            <p class="results-count">Tìm thấy {{ $products->total() }} sản phẩm</p>

            <a href="{{ route('shop.home') }}" class="view-product-btn" style="width: 200px">
                Quay về Homepage
            </a>
        </div>

        @if ($products->count() > 0)
            <div class="products-grid">
                @foreach ($products as $product)
                    <div class="product-card">
                        <div class="product-image">
                            @php
                                $mainImage =
                                    $product->images->where('pivot.image_role', 'main')->first() ??
                                    $product->images->first();
                            @endphp

                            @if ($mainImage)
                                <img src="{{ Storage::url($mainImage->image_url) }}" class="w-100"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.src='{{ asset('images/no-image.png') }}'; this.onerror=null;">
                            @else
                                <div class="no-image">Không có hình ảnh</div>
                            @endif
                        </div>
                        <div class="product-info">
                            <h3 style="min-height: 85px">{{ $product->product_name }}</h3>
                            <p class="product-brand">{{ $product->brand->brand_name ?? 'N/A' }}</p>
                            <p class="product-price">${{ number_format($product->price, 2) }}</p>
                            <a href="{{ route('shop.product.show', $product->product_id) }}" class="view-product-btn">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $products->appends(['query' => $query])->links() }}
            </div>
        @else
            <div class="no-results">
                <h3>Không tìm thấy sản phẩm nào</h3>
                <p>Hãy thử tìm kiếm với từ khóa khác hoặc <a href="{{ route('shop.home') }}">quay về trang chủ</a></p>
            </div>
        @endif
    </div>

    <script src="{{ asset('js/search.js') }}"></script>
    <script>
        // Search functionality with suggestions
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchForm = document.querySelector('.search-form');

            if (searchInput) {
                let searchTimeout;

                // Add suggestions container
                const suggestionsContainer = document.createElement('div');
                suggestionsContainer.className = 'search-suggestions';
                searchInput.parentNode.appendChild(suggestionsContainer);

                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        hideSuggestions();
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetchSuggestions(query);
                    }, 300);
                });

                searchInput.addEventListener('blur', function() {
                    // Delay hiding to allow clicking on suggestions
                    setTimeout(hideSuggestions, 200);
                });

                function fetchSuggestions(query) {
                    fetch(`/search/suggestions?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(suggestions => {
                            showSuggestions(suggestions);
                        })
                        .catch(error => {
                            console.error('Error fetching suggestions:', error);
                        });
                }

                function showSuggestions(suggestions) {
                    suggestionsContainer.innerHTML = '';

                    if (suggestions.length === 0) {
                        hideSuggestions();
                        return;
                    }

                    suggestions.forEach(suggestion => {
                        const item = document.createElement('div');
                        item.className = 'search-suggestion-item';
                        item.textContent = suggestion;
                        item.addEventListener('click', function() {
                            searchInput.value = suggestion;
                            hideSuggestions();
                            searchForm.submit();
                        });
                        suggestionsContainer.appendChild(item);
                    });

                    suggestionsContainer.style.display = 'block';
                }

                function hideSuggestions() {
                    suggestionsContainer.style.display = 'none';
                }

                // Handle form submission
                searchForm.addEventListener('submit', function(e) {
                    const query = searchInput.value.trim();
                    if (query.length === 0) {
                        e.preventDefault();
                        alert('Vui lòng nhập từ khóa tìm kiếm');
                    }
                });
            }
        });
    </script>
</body>

</html>
