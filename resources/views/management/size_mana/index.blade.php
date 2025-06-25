<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Size sản phẩm</title>
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
                            <h2>Quản lý <b>Size sản phẩm</b></h2>
                            <a href="{{ route('admin.size.create') }}" class="btn btn-success mt-2 mb-4">
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
                    <table class="table table-striped table-hover table-bordered" id='sizeTable'>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Loại size sản phẩm</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sizes as $size)
                                <tr>
                                    <td>{{ $size->size_id }}</td>
                                    <td>{{ $size->size_name }}</td>
                                    <td>
                                        <a href="{{ route('admin.size.edit', ['size' => $size->size_id]) }}"
                                            class="edit" title="Sửa" data-toggle="tooltip">
                                            <i class="material-icons">&#xE254;</i>
                                        </a>
                                        <form action="{{ route('admin.size.delete', $size->size_id) }}" method="POST"
                                            style="display:inline; color: #e34724">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Xóa" data-toggle="tooltip"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa Size sản phẩm này không?')">
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
                                <span class="total-records">{{ $sizes->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $sizes->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $sizes->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $sizes->currentPage() >= $sizes->lastPage() ? 'disabled' : '' }}>
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
        const currentPage = {{ $sizes->currentPage() }};
        const totalPages = {{ $sizes->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $sizes->url($sizes->currentPage() + 1) }}";
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
        const sizeTable = document.querySelector('#sizeTable tbody');

        // Debug check
        console.log('Elements found:', {
            searchInput: !!searchInput,
            sizeTable: !!sizeTable
        });

        // Define helper functions first
        const debounce = (func, wait) => {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        };

        function updateSizeTable(sizes) {
            if (!sizes || sizes.length === 0) {
                sizeTable.innerHTML =
                    '<tr><td colspan="3" class="text-center">Không tìm thấy size nào</td></tr>';
                return;
            }

            sizeTable.innerHTML = sizes.map(size => `
            <tr>
                <td>${size.size_id}</td>
                <td>${size.size_name}</td>
                <td>
                    <a href="/admin/sizes/${size.size_id}/edit" class="edit" title="Sửa">
                        <i class="material-icons">&#xE254;</i>
                    </a>
                    <form action="/admin/sizes/${size.size_id}" method="POST" style="display:inline; color: #e34724">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete" title="Xóa"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa Size sản phẩm này không?')">
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
                const response = await fetch(`/admin/sizes/search?query=${encodeURIComponent(query)}`, {
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

                updateSizeTable(data.data);

            } catch (error) {
                console.error('Search error:', error);
                sizeTable.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
            }
        };

        // Add event listener
        if (searchInput) {
            searchInput.addEventListener('input', debounce(handleSearch, 300));
            console.log('Search listener attached');
        } else {
            console.error('Search input not found');
        }
    });
</script>

</html>
