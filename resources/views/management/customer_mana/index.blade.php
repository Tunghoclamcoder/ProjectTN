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
</head>

<body>
    @include('components.admin-header')

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
                                    <input type="text" class="form-control" placeholder="Tìm kiếm...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover table-bordered">
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
                url: `{{ url('admin/customers') }}/${customerId}/toggle-status`,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
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
</script>

</html>
