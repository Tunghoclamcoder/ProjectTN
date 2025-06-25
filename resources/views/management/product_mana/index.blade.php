<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Sản phẩm</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/crud.css') }}">
    <script src="{{ asset('js/alert.js') }}"></script>
</head>

<body>
    @include('management.components.admin-header')

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

    <div class="container">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('admin.dashboard') }}" class="btn back-btn">
                                <i class="fa fa-arrow-left"></i>
                                <span style="font-size: 12px; font-weight: 500;"> Quay lại</span>
                            </a>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="filter-box d-flex gap-3 align-items-center">
                                <div class="form-group" style="min-width: 250px;">
                                    <select class="form-control" id="categoryFilter">
                                        <option value="">Chọn danh mục</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="min-width: 250px;">
                                    <select class="form-control" id="brandFilter">
                                        <option value="">Chọn thương hiệu</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="min-width: 250px;">
                                    <select class="form-control" id="materialFilter">
                                        <option value="">Chọn chất liệu</option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->material_id }}">
                                                {{ $material->material_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" style="min-width: 250px;">
                                    <select class="form-control" id="statusFilter">
                                        <option value="">Chọn trạng thái</option>
                                        <option value="1">Đang bán</option>
                                        <option value="0">Ngừng kinh doanh</option>
                                    </select>
                                </div>

                                <button class="btn btn-secondary reset-filters"
                                    style="width: 150px; display: flex; align-items: center">
                                    <i class="material-icons">refresh</i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-6">
                        <h2>Quản lý <b>Sản phẩm</b></h2>
                        <a href="{{ route('admin.product.create') }}" class="btn btn-success mt-2 mb-4">
                            <i class="material-icons">&#xE147;</i>
                            <span>Thêm mới</span>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <div class="search-box">
                            <i class="material-icons">&#xE8B6;</i>
                            <input type="text" class="form-control" placeholder="Tìm kiếm...">
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Thương hiệu</th>
                            <th>Chất liệu</th>
                            <th>Danh mục</th>
                            <th>Giá gốc</th>
                            <th>Giảm giá</th>
                            <th>Giá sau giảm</th>
                            <th>Số lượng</th>
                            <th>Size</th>
                            <th>Trạng thái</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->product_id }}</td>
                                <td style="width: 100px; height: 100px">
                                    @php
                                        $mainImage = $product->getMainImage();
                                    @endphp

                                    @if ($mainImage && Storage::disk('public')->exists(str_replace('storage/', '', $mainImage->image_url)))
                                        <img src="{{ asset($mainImage->image_url) }}"
                                            alt="{{ $product->product_name }}"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/placeholder.png') }}" alt="Placeholder"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif

                                    <small class="d-block text-muted">
                                        ({{ $product->NumberOfImage }} ảnh)
                                    </small>
                                </td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                <td>{{ $product->material->material_name ?? 'N/A' }}</td>
                                <td>
                                    @foreach ($product->categories as $category)
                                        <span>{{ $category->category_name }}</span>
                                    @endforeach
                                    <small class="d-block text-muted">
                                        ({{ $product->NumberOfCategory }} danh mục)
                                    </small>
                                </td>
                                <td>{{ number_format($product->price, 0, ',', '.') }}đ</td>
                                <td>{{ floatval($product->discount) }}%</td>
                                <td>{{ number_format($product->getDiscountedPrice(), 0, ',', '.') }}đ</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->size->size_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->status ? 'Đang bán' : 'Ngừng kinh doanh' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.product.details', ['product' => $product->product_id]) }}"
                                        class="view" title="Xem chi tiết" data-toggle="tooltip">
                                        <i class="material-icons">&#xE417;</i>
                                    </a>
                                    <a href="{{ route('admin.product.edit', ['product' => $product->product_id]) }}"
                                        class="edit" title="Sửa" data-toggle="tooltip">
                                        <i class="material-icons">&#xE254;</i>
                                    </a>
                                    <form action="{{ route('admin.product.delete', $product->product_id) }}"
                                        method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete" title="Xóa" data-toggle="tooltip"
                                            style="color: red"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                            <i class="material-icons">&#xE872;</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="clearfix">
                    <div class="footer-container">
                        <div class="pagination-info">
                            <span>Tổng số lượng : </span>
                            <span class="total-records">{{ $products->total() }}</span>
                        </div>

                        <div class="page-info">
                            <div class="page-info-text">
                                Trang <span class="page-number">{{ $products->currentPage() }}</span>
                                <span class="all-page-number"> / {{ $products->lastPage() }} </span>
                            </div>
                            <button class="next-page-btn" onclick="nextPage()"
                                {{ $products->currentPage() >= $products->lastPage() ? 'disabled' : '' }}>
                                <span>Trang tiếp</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

<script>
    function nextPage() {
        const currentPage = {{ $products->currentPage() }};
        const totalPages = {{ $products->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $products->url($products->currentPage() + 1) }}";
        }
    }

    // Tự động ẩn alert sau 5 giây
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").alert('close');
        }, 5000);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-box input');
        const categoryFilter = document.getElementById('categoryFilter');
        const brandFilter = document.getElementById('brandFilter');
        const materialFilter = document.getElementById('materialFilter');
        const statusFilter = document.getElementById('statusFilter');
        const resetButton = document.querySelector('.reset-filters');
        const productTable = document.querySelector('table tbody');

        // Debug check
        console.log('Elements found:', {
            searchInput: !!searchInput,
            categoryFilter: !!categoryFilter,
            brandFilter: !!brandFilter,
            materialFilter: !!materialFilter,
            statusFilter: !!statusFilter,
            productTable: !!productTable
        });

        async function searchProducts() {
            const query = searchInput?.value.trim() || '';
            const category = categoryFilter?.value || '';
            const brand = brandFilter?.value || '';
            const material = materialFilter?.value || '';
            const status = statusFilter?.value || '';

            console.log('Search params:', {
                query,
                category,
                brand,
                material,
                status
            });

            try {
                const response = await fetch(`/admin/products/search?${new URLSearchParams({
                query,
                category,
                brand,
                material,
                status
            })}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Search response:', data);

                if (data.success) {
                    updateProductTable(data.data);
                } else {
                    throw new Error(data.message || 'Search failed');
                }

            } catch (error) {
                console.error('Search error:', error);
                productTable.innerHTML = `
                <tr>
                    <td colspan="13" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
            }
        }

        function updateProductTable(products) {
            if (!products || products.length === 0) {
                productTable.innerHTML =
                    '<tr><td colspan="13" class="text-center">Không tìm thấy sản phẩm nào</td></tr>';
                return;
            }

            productTable.innerHTML = products.map(product => `
            <tr>
                <td>${product.product_id}</td>
                <td style="width: 100px; height: 100px">
                    <img src="${product.main_image}"
                         alt="${product.product_name}"
                         style="width: 80px; height: 80px; object-fit: cover;">
                    <small class="d-block text-muted">(${product.NumberOfImage} ảnh)</small>
                </td>
                <td>${product.product_name}</td>
                <td>${product.brand?.brand_name || 'N/A'}</td>
                <td>${product.material?.material_name || 'N/A'}</td>
                <td>
                    ${product.categories.map(cat => cat.category_name).join(', ')}
                    <small class="d-block text-muted">(${product.categories.length} danh mục)</small>
                </td>
                <td>${new Intl.NumberFormat('vi-VN').format(product.price)}đ</td>
                <td>${parseFloat(product.discount)}%</td>
                <td>${new Intl.NumberFormat('vi-VN').format(product.discounted_price)}đ</td>
                <td>${product.quantity}</td>
                <td>${product.size?.size_name || 'N/A'}</td>
                <td>
                    <span class="badge ${product.status ? 'bg-success' : 'bg-danger'}">
                        ${product.status ? 'Đang bán' : 'Ngừng kinh doanh'}
                    </span>
                </td>
                <td>
                    <a href="/admin/products/${product.product_id}" class="view" title="Xem chi tiết">
                        <i class="material-icons">&#xE417;</i>
                    </a>
                    <a href="/admin/products/${product.product_id}/edit" class="edit" title="Sửa">
                        <i class="material-icons">&#xE254;</i>
                    </a>
                    <form action="/admin/products/${product.product_id}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete" title="Xóa" style="color: red"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                            <i class="material-icons">&#xE872;</i>
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
        }

        // Add debounced search for input
        const debounce = (func, wait) => {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        };

        // Add event listeners
        searchInput?.addEventListener('input', debounce(searchProducts, 300));
        categoryFilter?.addEventListener('change', searchProducts);
        brandFilter?.addEventListener('change', searchProducts);
        materialFilter?.addEventListener('change', searchProducts);
        statusFilter?.addEventListener('change', searchProducts);

        // Reset filters
        resetButton?.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (categoryFilter) categoryFilter.value = '';
            if (brandFilter) brandFilter.value = '';
            if (materialFilter) materialFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            searchProducts();
        });
    });
</script>

</html>
