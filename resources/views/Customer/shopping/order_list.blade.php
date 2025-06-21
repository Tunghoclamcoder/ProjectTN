<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="{{ asset('js/alert.js') }}"></script>

    <style>
        .order-status {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .status-delivered {
            color: #198754;
        }

        .status-shipped {
            color: #0d6efd;
        }

        .status-processing {
            color: #fd7e14;
        }

        .status-cancelled {
            color: #dc3545;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .btn-sm {
            font-size: 0.8rem;
        }
    </style>
</head>
<div class="alerts-container" style="display: flex; justify-content: center;">
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

<body class="bg-light">
    <div class="container my-5">
        <div class="mb-4">
            <a href="{{ route('shop.home') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay về trang chủ
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Lịch sử đặt hàng
                    </h2>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>All Orders</option>
                            <option>Last 30 Days</option>
                            <option>Last 3 Months</option>
                            <option>Last Year</option>
                        </select>
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Mã đơn hàng</th>
                                <th scope="col">Ngày đặt</th>
                                <th scope="col">Phí Ship</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $order->order_id }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $order->order_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <span> {{ number_format($order->shipping_method->shipping_fee) }}đ</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($order->getFinalTotal()) }} VNĐ</span>
                                        @if ($order->voucher)
                                            <br>
                                            <small class="text-success">
                                                <i class="bi bi-tag-fill"></i> Đã áp dụng mã giảm giá
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($order->order_status)
                                            @case('pending')
                                                <span class="badge bg-warning" style="padding: 10px">
                                                    <i class="bi bi-clock me-1"></i>Chờ xác nhận
                                                </span>
                                            @break

                                            @case('confirmed')
                                                <span class="badge bg-info" style="padding: 10px">
                                                    <i class="bi bi-check-circle me-1"></i>Đã xác nhận
                                                </span>
                                            @break

                                            @case('shipping')
                                                <span class="badge bg-primary" style="padding: 10px">
                                                    <i class="bi bi-truck me-1"></i>Đang giao hàng
                                                </span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success" style="padding: 10px">
                                                    <i class="bi bi-check-all me-1"></i>Đã giao hàng
                                                </span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Đã hủy
                                                </span>
                                            @break

                                            @case('returned')
                                                <span class="badge bg-secondary">
                                                    <i class=" me-1"></i>Đã hoàn trả
                                                </span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group d-flex flex-column" role="group">
                                            <a href="{{ route('customer.orders.detail', $order->order_id) }}"
                                                class="btn btn-outline-primary btn-sm mb-2" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>

                                            @if ($order->order_status === 'completed' && !$order->isReturned())
                                                <form action="{{ route('customer.orders.return', $order->order_id) }}"
                                                    style="display: flex; justify-content: center;" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn hoàn trả đơn hàng này ?');">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-warning btn-sm"
                                                        title="Hoàn trả đơn hàng">
                                                        <i class="bi bi-arrow-return-left"></i> Hoàn trả đơn hàng
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox h4 mb-3 d-block"></i>
                                                <p class="mb-0">Bạn chưa có đơn hàng nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    @if ($orders->hasPages())
                        <nav aria-label="Order history pagination" class="mt-4">
                            {{ $orders->links('pagination::bootstrap-5') }}
                        </nav>
                    @endif

                    <!-- Thống kê đơn hàng -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Tổng đơn hàng</h5>
                                    <h2 class="text-success">{{ $totalOrders }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Tổng chi tiêu</h5>
                                    <h2 class="text-primary">{{ number_format($totalSpent) }} VNĐ</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-info">Đơn hàng đã giao thành công</h5>
                                    <h2 class="text-info">{{ $completedOrders }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <style>
        .pagination {
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination .page-item .page-link {
            padding: 8px 16px;
            color: #666;
            border-radius: 4px;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #999;
            pointer-events: none;
            background-color: #f8f9fa;
        }
    </style>

    </html>
