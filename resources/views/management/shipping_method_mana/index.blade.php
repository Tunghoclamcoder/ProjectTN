<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Phương thức vận chuyển</title>
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
    @include('components.admin-header')

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
                            <h2>Quản lý <b>Phương thức vận chuyển</b></h2>
                            <a href="{{ route('admin.shipping.create') }}" class="btn btn-success mt-2 mb-4">
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
                    <table class="table table-striped table-hover table-bordered" id="shippingTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Phương thức vận chuyển</th>
                                <th>Phí Ship</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shippings as $shipping)
                                <tr>
                                    <td>{{ $shipping->method_id }}</td>
                                    <td>{{ $shipping->method_name }}</td>
                                    <td>{{ number_format($shipping->shipping_fee) }} VNĐ</td>
                                    <td>
                                        <a href="{{ route('admin.shipping.edit', $shipping->method_id) }}"
                                            class="edit-btn">
                                            <i class="material-icons">&#xE254;</i>
                                        </a>
                                        <form action="{{ route('admin.shipping.delete', $shipping->method_id) }}"
                                            method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Xóa"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa phương thức vận chuyển này?')">
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
                                <span class="total-records">{{ $shippings->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $shippings->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $shippings->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $shippings->currentPage() >= $shippings->lastPage() ? 'disabled' : '' }}>
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
        const currentPage = {{ $shippings->currentPage() }};
        const totalPages = {{ $shippings->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $shippings->url($shippings->currentPage() + 1) }}";
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
        const shippingTable = document.querySelector('table tbody');

        console.log('Elements found:', {
            searchInput: !!searchInput,
            shippingTable: !!shippingTable
        });

        function updateshippingTable(shippings) {
            if (!shippings || shippings.length === 0) {
                shippingTable.innerHTML =
                    '<tr><td colspan="3" class="text-center">Không tìm thấy phương thức vận chuyển nào</td></tr>';
                return;
            }

            shippingTable.innerHTML = shippings.map(shipping => `
            <tr>
                <td>${shipping.method_id}</td>
                <td>${shipping.method_name}</td>
                <td>${new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(shipping.shipping_fee)}</td>
                <td>
                    <a href="/admin/shippings/${shipping.method_id}/edit" class="edit-btn">
                        <i class="material-icons">&#xE254;</i>
                    </a>
                    <form action="/admin/shippings/${shipping.method_id}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete" title="Xóa"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa phương thức vận chuyển này?')">
                            <i class="material-icons">&#xE872;</i>
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
        }

        const handleSearch = async (e) => {
            const query = e.target.value.trim();
            console.log('Searching for:', query);

            try {
                // Updated URL to match route prefix
                const response = await fetch(
                    `/admin/shipping-methods/search?query=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Search response:', data);

                updateshippingTable(data.data);

            } catch (error) {
                console.error('Search error:', error);
                shippingTable.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
            }
        };

        if (searchInput) {
            const debounce = (func, wait) => {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            };

            searchInput.addEventListener('input', debounce(handleSearch, 300));
            console.log('Search listener attached');
        }
    });
</script>

</html>
