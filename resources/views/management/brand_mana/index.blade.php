<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Thương hiệu</title>
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
                        <div class="col-sm-6">
                            <h2>Quản lý <b>Thương hiệu</b></h2>
                            <a href="{{ route(name: 'admin.brand.create') }}" class="btn btn-success mt-2 mb-4">
                                <i class="material-icons">&#xE147;</i>
                                <span>Thêm mới</span>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <div class="search-box">
                                <i class="size-icons">&#xE8B6;</i>
                                <input type="text" class="form-control" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover table-bordered" id="brandTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên thương hiệu</th>
                                <th>Mô tả</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                                <tr>
                                    <td>{{ $brand->brand_id }}</td>
                                    <td style="width: 120px; height: 100px">
                                        @if ($brand->brand_image && Storage::disk('public')->exists($brand->brand_image))
                                            <img src="{{ Storage::url($brand->brand_image) }}"
                                                alt="{{ $brand->brand_name }}"
                                                style="width: 90px; height: 80px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/placeholder.png') }}" alt="Placeholder"
                                                style="width: 90px; height: 80px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td>{{ $brand->brand_name }}</td>
                                    <td>{{ Str::limit($brand->description, 500) }}</td>
                                    <td>
                                        <a href="{{ route('admin.brand.edit', ['brand' => $brand->brand_id]) }}">
                                            <i class="material-icons">&#xE254;</i>
                                        </a>
                                        <form action="{{ route('admin.brand.delete', $brand->brand_id) }}"
                                            method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Xóa" data-toggle="tooltip"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?')">
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
                                <span class="total-records">{{ $brands->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $brands->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $brands->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $brands->currentPage() >= $brands->lastPage() ? 'disabled' : '' }}>
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
        const currentPage = {{ $brands->currentPage() }};
        const totalPages = {{ $brands->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $brands->url($brands->currentPage() + 1) }}";
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
        const brandTable = document.querySelector('#brandTable tbody');

        const debounce = (func, wait) => {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        };

        const handleSearch = debounce(async (e) => {
            const query = e.target.value.trim();

            try {
                const response = await fetch(
                    `/admin/brands/search?query=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Search failed');
                }

                updateBrandTable(data.data);

            } catch (error) {
                console.error('Search error:', error);
                brandTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
            }
        }, 300);

        function updateBrandTable(brands) {
            if (!brands || brands.length === 0) {
                brandTable.innerHTML =
                    '<tr><td colspan="5" class="text-center">Không tìm thấy thương hiệu nào</td></tr>';
                return;
            }

            brandTable.innerHTML = brands.map(brand => `
            <tr>
                <td>${brand.brand_id}</td>
                <td style="width: 120px; height: 100px">
                    ${brand.brand_image
                        ? `<img src="/storage/${brand.brand_image}"
                             alt="${brand.brand_name}"
                             style="width: 90px; height: 80px; object-fit: cover;">`
                        : `<img src="/images/placeholder.png"
                             alt="Placeholder"
                             style="width: 90px; height: 80px; object-fit: cover;">`
                    }
                </td>
                <td>${brand.brand_name}</td>
                <td>${limitText(brand.description, 500)}</td>
                <td>
                    <a href="/admin/brands/${brand.brand_id}/edit">
                        <i class="material-icons">&#xE254;</i>
                    </a>
                    <form action="/admin/brands/${brand.brand_id}"
                          method="POST"
                          style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="delete"
                                title="Xóa"
                                data-toggle="tooltip"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?')">
                            <i class="material-icons">&#xE872;</i>
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
        }

        function limitText(text, limit) {
            if (!text) return '';
            return text.length > limit ? text.substring(0, limit) + '...' : text;
        }

        searchInput.addEventListener('input', handleSearch);
    });
</script>

</html>
