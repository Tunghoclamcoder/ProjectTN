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
    @include('management.components.admin-header')

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
                            <h2>Quản lý <b>Đơn hàng</b></h2>

                        </div>
                        <div class="row">

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <div class="search-box">
                                        <i class="material-icons">&#xE8B6;</i>
                                        <input type="text" class="form-control" id="orderSearch"
                                            placeholder="Tìm kiếm theo tên, SĐT, địa chỉ...">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" id="orderDate"
                                        placeholder="Ngày đặt hàng">
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" id="orderStatus">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="pending">Chờ xử lý</option>
                                        <option value="processing">Đang xử lý</option>
                                        <option value="completed">Hoàn thành</option>
                                        <option value="cancelled">Đã hủy</option>
                                        <option value="returned">Đã hoàn trả</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover table-bordered" id="orderTable">
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
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
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
                                                @case('refunded')
                                                    bg-secondary
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
                                                {{-- Show confirm and cancel buttons for pending orders --}}
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
                                            @elseif ($order->order_status == 'confirmed')
                                                {{-- Show block button for confirmed orders --}}
                                                <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                    title="Không thể thay đổi trạng thái đơn hàng đã được xác nhận">
                                                    <button class="btn btn-sm btn-secondary"
                                                        style="pointer-events: none" disabled>
                                                        <i class="material-icons">block</i>
                                                    </button>
                                                </span>
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
    </div>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        function nextPage() {
            const currentPage = {{ $orders->currentPage() }};
            const totalPages = {{ $orders->lastPage() }};

            if (currentPage < totalPages) {
                window.location.href = "{{ $orders->url($orders->currentPage() + 1) }}";
            }
        }

        //Khởi tạo tooltip thông báo
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('#orderSearch');
            const dateInput = document.querySelector('#orderDate');
            const statusSelect = document.querySelector('#orderStatus');
            const orderTable = document.querySelector('#orderTable tbody');

            const debounce = (func, wait) => {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            };

            const handleSearch = debounce(async () => {
                const query = searchInput.value.trim();
                const date = dateInput.value;
                const status = statusSelect.value;

                try {
                    const params = new URLSearchParams({
                        query: query,
                        date: date,
                        status: status
                    });

                    const response = await fetch(`/admin/orders/search?${params}`, {
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
                    updateOrderTable(data.data);

                } catch (error) {
                    console.error('Search error:', error);
                    orderTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
                }
            }, 300);

            function updateOrderTable(orders) {
                if (!orders || orders.length === 0) {
                    orderTable.innerHTML =
                        '<tr><td colspan="6" class="text-center">Không tìm thấy đơn hàng nào</td></tr>';
                    return;
                }

                orderTable.innerHTML = orders.map(order => `
        <tr>
            <td>#${order.order_id}</td>
            <td>
                <div>${order.receiver_name}</div>
                <small class="text-muted">
                    SĐT: ${order.receiver_phone}<br>
                    Địa chỉ: ${order.receiver_address}
                </small>
            </td>
            <td>${formatDate(order.order_date)}</td>
            <td>
                ${formatCurrency(order.total_amount)}
                ${order.voucher ? `
                                                    <br>
                                                    <small class="text-success">
                                                        Đã áp dụng voucher: ${order.voucher.code}
                                                    </small>
                                                ` : ''}
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(order.order_status)}">
                    ${getStatusLabel(order.order_status)}
                </span>
            </td>
            <td>
                <div class="btn-group">
                    <a href="/admin/orders/${order.order_id}" class="btn btn-sm btn-info" title="Xem chi tiết">
                        <i class="material-icons">visibility</i>
                    </a>
                    <a href="/admin/orders/${order.order_id}/edit" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                        <i class="material-icons">&#xE254;</i>
                    </a>
                </div>
            </td>
        </tr>
    `).join('');
            }

            function formatCurrency(amount) {
                // Convert to number and ensure it's valid
                const number = parseFloat(amount);
                if (isNaN(number)) return '0 VNĐ';

                return new Intl.NumberFormat('vi-VN').format(number) + ' VNĐ';
            }

            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';

                return date.toLocaleString('vi-VN', {
                    hour: '2-digit',
                    minute: '2-digit',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            function getStatusBadgeClass(status) {
                const classes = {
                    'pending': 'bg-warning',
                    'confirmed': 'bg-info',
                    'shipping': 'bg-primary',
                    'completed': 'bg-success',
                    'cancelled': 'bg-danger'
                };
                return classes[status] || 'bg-secondary';
            }

            function getStatusLabel(status) {
                const labels = {
                    'pending': 'Chờ xác nhận',
                    'confirmed': 'Đã xác nhận',
                    'shipping': 'Đang giao hàng',
                    'completed': 'Hoàn thành',
                    'cancelled': 'Đã hủy'
                };
                return labels[status] || 'Không xác định';
            }

            function getActionButtons(order) {
                if (order.order_status !== 'pending') return '';

                return `
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                  title="Không thể thay đổi trạng thái đơn hàng đã được xác nhận">
                <button class="btn btn-sm btn-secondary" style="pointer-events: none" disabled>
                    <i class="material-icons">block</i>
                </button>
            </span>
            <form action="/admin/order/${order.order_id}/update-status" method="POST" style="display:inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_status" value="confirmed">
                <button type="submit" class="btn btn-sm btn-success" title="Xác nhận đơn hàng"
                        onclick="return confirm('Xác nhận đơn hàng này?')">
                    <i class="material-icons">check</i>
                </button>
            </form>
            <form action="/admin/order/${order.order_id}/update-status" method="POST" style="display:inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_status" value="cancelled">
                <button type="submit" class="btn btn-sm btn-danger" title="Hủy đơn hàng"
                        onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                    <i class="material-icons">clear</i>
                </button>
            </form>
        `;
            }

            // Add event listeners
            searchInput.addEventListener('input', handleSearch);
            dateInput.addEventListener('change', handleSearch);
            statusSelect.addEventListener('change', handleSearch);
        });
    </script>
</body>

</html>
