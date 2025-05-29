<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Đơn hàng</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/crud.css') }}">
</head>

<body>
    <!-- Include sidebar và header của dashboard -->
    @include('components.admin-header')

    <div class="container mt-3">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
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
                                <h2>Quản lý <b>Đơn hàng</b></h2>
                                <a href="{{ route('admin.order.create') }}" class="btn btn-success mt-2 mb-4">
                                    <i class="size-icons">&#xE147;</i>
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
                    </div>
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>#{{ $order->order_id }}</td>
                                    <td>
                                        <div>{{ $order->receiver_name }}</div>
                                        <small class="text-muted">
                                            SĐT: {{ $order->receiver_phone }}<br>
                                            Địa chỉ: {{ $order->receiver_address }}
                                        </small>
                                    </td>
                                    <td>{{ $order->order_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->getTotalAmount()) }} VNĐ
                                        @if ($order->voucher)
                                            <br>
                                            <small class="text-success">
                                                Đã áp dụng voucher: {{ $order->voucher->code }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge
                                            @switch($order->order_status)
                                                @case('pending')
                                                    bg-warning
                                                    @break
                                                @case('confirmed')
                                                    bg-info
                                                    @break
                                                @case('shipping')
                                                    bg-primary
                                                    @break
                                                @case('completed')
                                                    bg-success
                                                    @break
                                                @case('cancelled')
                                                    bg-danger
                                                    @break
                                            @endswitch">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.order.show', $order->order_id) }}"
                                                class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="material-icons">visibility</i>
                                            </a>

                                            <a href="{{ route('admin.order.edit', $order->order_id) }}"
                                                class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="material-icons">&#xE254;</i>
                                            </a>

                                            @if ($order->order_status == 'pending')
                                                <form
                                                    action="{{ route('admin.order.update-status', $order->order_id) }}"
                                                    method="POST" style="display:inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="order_status" value="confirmed">
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        title="Xác nhận đơn hàng"
                                                        onclick="return confirm('Xác nhận đơn hàng này?')">
                                                        <i class="material-icons">check</i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($order->order_status != 'completed' && $order->order_status != 'cancelled')
                                                <form
                                                    action="{{ route('admin.order.update-status', $order->order_id) }}"
                                                    method="POST" style="display:inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="order_status" value="cancelled">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Hủy đơn hàng"
                                                        onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                                        <i class="material-icons">clear</i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="clearfix">
                        <div class="footer-container">
                            <div class="pagination-info">
                                <span>Tổng số lượng : </span>
                                <span class="total-records">{{ $orders->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $orders->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $orders->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $orders->currentPage() >= $orders->lastPage() ? 'disabled' : '' }}>
                                    <span>Trang tiếp</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
</body>

</html>
