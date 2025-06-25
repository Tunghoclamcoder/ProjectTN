<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý khách hàng</title>
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
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>

    <div class="container">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{{ route('admin.dashboard') }}" class="btn back-btn">
                                <i class="fa fa-arrow-left"></i>
                                <span style="font-size: 12px; font-weight: 500;"> Quay lại</span>
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h2>Danh sách <b>Khách hàng</b></h2>
                            </div>
                            <div class="col-sm-6">
                                <div class="search-box">
                                    <i class="material-icons">&#xE8B6;</i>
                                    <input type="text" id="customerSearch" class="form-control"
                                        placeholder="Tìm kiếm khách hàng...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover table-bordered" id="customerTable">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên khách hàng <i class="fa fa-sort"></i></th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                                <th>Trạng thái</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->customer_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone_number }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>
                                        <span style="font-size: 9px; display: flex; justify-content: center;"
                                            class="badge status-badge {{ $customer->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $customer->status == 'active' ? 'Đang hoạt động' : 'Đã bị cấm' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="toggleStatus({{ $customer->customer_id }})"
                                            class="btn btn-sm {{ $customer->status == 'active' ? 'btn-danger' : 'btn-success' }}">
                                            {{ $customer->status == 'active' ? 'Cấm tài khoản' : 'Mở khóa tài khoản' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="clearfix">
                        <div class="footer-container">
                            <div class="pagination-info">
                                <span>Tổng số lượng : </span>
                                <span class="total-records">{{ $customers->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $customers->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $customers->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $customers->currentPage() >= $customers->lastPage() ? 'disabled' : '' }}>
                                    <span>Trang tiếp</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</body>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const sessionAlert = document.querySelector('.alert-session');
        if (sessionAlert) {
            setTimeout(() => {
                sessionAlert.remove();
            }, 3000);
        }
    });

    function nextPage() {
        const currentPage = {{ $customers->currentPage() }};
        const totalPages = {{ $customers->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $customers->url($customers->currentPage() + 1) }}";
        }
    }

    function toggleStatus(customerId) {
        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái tài khoản này?')) {
            $.ajax({
                url: `{{ url('admin/customer') }}/${customerId}/toggle-status`,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Lưu thông báo vào localStorage
                        localStorage.setItem('statusMessage', response.message);
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra khi thay đổi trạng thái');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi thay đổi trạng thái');
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('#customerSearch');
        const customerTable = document.querySelector('#customerTable tbody');

        const handleSearch = debounce(async (e) => {
            const query = e.target.value.trim();

            try {
                const response = await fetch(
                    `/admin/customer/search?query=${encodeURIComponent(query)}`, {
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
                console.log('Search response:', data); // Debug log

                if (data.success) {
                    updateCustomerTable(data.data);
                } else {
                    throw new Error(data.message || 'Tìm kiếm thất bại');
                }

            } catch (error) {
                console.error('Search error:', error);
                customerTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
            }
        }, 300);

        function updateCustomerTable(customers) {
            if (!customers || customers.length === 0) {
                customerTable.innerHTML =
                    '<tr><td colspan="6" class="text-center">Không tìm thấy khách hàng nào</td></tr>';
                return;
            }

            customerTable.innerHTML = customers.map(customer => `
            <tr>
                <td>${customer.customer_id}</td>
                <td>${customer.customer_name}</td>
                <td>${customer.email || ''}</td>
                <td>${customer.phone_number || ''}</td>
                <td>${customer.address || ''}</td>
                <td>
                    <span class="badge ${customer.status === 'active' ? 'bg-success' : 'bg-danger'}">
                        ${customer.status === 'active' ? 'Đang hoạt động' : 'Ngừng hoạt động'}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="/admin/customer/${customer.customer_id}/edit"
                           class="btn btn-warning btn-sm">
                            <i class="material-icons">&#xE254;</i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
        }

        searchInput.addEventListener('input', handleSearch);
    });
</script>

</html>
