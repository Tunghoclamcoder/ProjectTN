<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Phương thức thanh toán</title>
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
                            <h2>Quản lý <b>Phương thức thanh toán</b></h2>
                            <a href="{{ route('admin.payment.create') }}" class="btn btn-success mt-2 mb-4">
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
                    <table class="table table-striped table-hover table-bordered" id="paymentTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Phương thức thanh toán</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->method_id }}</td>
                                    <td>{{ $payment->method_name }}</td>
                                    <td>
                                        <a href="{{ route('admin.payment.edit', $payment->method_id) }}"
                                            class="edit-btn">
                                            <i class="material-icons">&#xE254;</i>
                                        </a>
                                        <form action="{{ route('admin.payment.delete', $payment->method_id) }}"
                                            method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Xóa"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa phương thức thanh toán này?')">
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
                                <span class="total-records">{{ $payments->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $payments->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $payments->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $payments->currentPage() >= $payments->lastPage() ? 'disabled' : '' }}>
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
        const currentPage = {{ $payments->currentPage() }};
        const totalPages = {{ $payments->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $payments->url($payments->currentPage() + 1) }}";
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
        const paymentTable = document.querySelector('table tbody');

        console.log('Elements found:', {
            searchInput: !!searchInput,
            paymentTable: !!paymentTable
        });

        function updatePaymentTable(payments) {
            if (!payments || payments.length === 0) {
                paymentTable.innerHTML =
                    '<tr><td colspan="3" class="text-center">Không tìm thấy phương thức thanh toán nào</td></tr>';
                return;
            }

            paymentTable.innerHTML = payments.map(payment => `
            <tr>
                <td>${payment.method_id}</td>
                <td>${payment.method_name}</td>
                <td>
                    <a href="/admin/payments/${payment.method_id}/edit" class="edit-btn">
                        <i class="material-icons">&#xE254;</i>
                    </a>
                    <form action="/admin/payments/${payment.method_id}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete" title="Xóa"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa phương thức thanh toán này?')">
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
                    `/admin/payment-methods/search?query=${encodeURIComponent(query)}`, {
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

                updatePaymentTable(data.data);

            } catch (error) {
                console.error('Search error:', error);
                paymentTable.innerHTML = `
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
