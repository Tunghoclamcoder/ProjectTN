<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('components.admin-header')
    {{-- Thông báo  --}}
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

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4>Chỉnh sửa thông tin đơn hàng</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.order.update', $order->order_id) }}" method="POST">
                            @csrf
                            @method('PUT')



                            <!-- Thông tin người nhận -->
                            <h5 class="mb-3">Thông tin người nhận</h5>
                            <div class="mb-3">
                                <label for="receiver_name" class="form-label">Tên người nhận <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('receiver_name') is-invalid @enderror"
                                    id="receiver_name" name="receiver_name"
                                    value="{{ old('receiver_name', $order->receiver_name) }}" required>
                                @error('receiver_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="receiver_phone" class="form-label">Số điện thoại <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('receiver_phone') is-invalid @enderror"
                                    id="receiver_phone" name="receiver_phone"
                                    value="{{ old('receiver_phone', $order->receiver_phone) }}" required>
                                @error('receiver_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="receiver_address" class="form-label">Địa chỉ <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('receiver_address') is-invalid @enderror" id="receiver_address"
                                    name="receiver_address" rows="3" required>{{ old('receiver_address', $order->receiver_address) }}</textarea>
                                @error('receiver_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Thông tin đơn hàng -->
                            <h5 class="mb-3">Thông tin đơn hàng</h5>
                            <div class="mb-3">
                                <label for="order_status" class="form-label">Trạng thái đơn hàng</label>
                                <select class="form-select @error('order_status') is-invalid @enderror"
                                    id="order_status" name="order_status">
                                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>
                                        Chờ xác nhận
                                    </option>
                                    <option value="confirmed"
                                        {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>
                                        Đã xác nhận
                                    </option>
                                    <option value="shipping"
                                        {{ $order->order_status == 'shipping' ? 'selected' : '' }}>
                                        Đang giao hàng
                                    </option>
                                    <option value="completed"
                                        {{ $order->order_status == 'completed' ? 'selected' : '' }}>
                                        Đã hoàn thành
                                    </option>
                                    <option value="cancelled"
                                        {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>
                                        Đã hủy
                                    </option>
                                </select>
                                @error('order_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_method_id" class="form-label">Phương thức thanh toán</label>
                                <select class="form-select @error('payment_method_id') is-invalid @enderror"
                                    id="payment_method_id" name="payment_method_id">
                                    @foreach ($paymentMethods as $method)
                                        <option value="{{ $method->method_id }}"
                                            {{ $order->payment_method_id == $method->method_id ? 'selected' : '' }}>
                                            {{ $method->method_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shipping_method_id" class="form-label">Phương thức vận chuyển</label>
                                <select class="form-select @error('shipping_method_id') is-invalid @enderror"
                                    id="shipping_method_id" name="shipping_method_id">
                                    @foreach ($shippingMethods as $method)
                                        <option value="{{ $method->method_id }}"
                                            {{ $order->shipping_method_id == $method->method_id ? 'selected' : '' }}>
                                            {{ $method->method_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('shipping_method_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="mb-3">
                                    <label for="order_date" class="form-label">Ngày đặt hàng <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                        class="form-control @error('order_date') is-invalid @enderror" id="order_date"
                                        name="order_date"
                                        value="{{ old('order_date', $order->order_date->format('Y-m-d\TH:i')) }}"
                                        required>
                                    @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Khách hàng <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id" required>
                                        <option value="">Chọn khách hàng</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->customer_id }}"
                                                {{ $order->customer_id == $customer->customer_id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} - {{ $customer->phone_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Chi tiết sản phẩm -->
                                <h5 class="mb-3">Chi tiết sản phẩm</h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Giá bán</th>
                                                <th>Số lượng</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->orderDetails as $detail)
                                                <tr>
                                                    <td>{{ $detail->product->product_name }}</td>
                                                    <td>{{ number_format($detail->sold_price) }} VNĐ</td>
                                                    <td>{{ $detail->sold_quantity }}</td>
                                                    <td>{{ number_format($detail->getSubtotal()) }} VNĐ</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Tổng tiền:</strong></td>
                                                <td>{{ number_format($order->getTotalAmount()) }} VNĐ</td>
                                            </tr>
                                            @if ($order->voucher)
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Mã giảm giá:</strong>
                                                    </td>
                                                    <td>{{ $order->voucher->code }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Thành tiền sau
                                                            giảm:</strong></td>
                                                    <td>{{ number_format($order->getTotalAmount() - $order->voucher->discount_amount) }}
                                                        VNĐ</td>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.order') }}" class="btn btn-secondary">
                                        <i class="material-icons">arrow_back</i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons">save</i> Cập nhật đơn hàng
                                    </button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }

        .card-header h4 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        h5 {
            color: #2c3e50;
            font-weight: 600;
            margin-top: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .table {
            margin-top: 15px;
        }

        .table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }

        .table td,
        .table th {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #0d6efd;
            border: none;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background: #5c636a;
            transform: translateY(-1px);
        }

        .material-icons {
            font-size: 20px;
        }

        .text-danger {
            font-weight: bold;
        }

        .invalid-feedback {
            font-size: 0.875em;
            margin-top: 5px;
        }

        /* Responsive tables */
        .table-responsive {
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        /* Status colors */
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-confirmed {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-shipping {
            background-color: #007bff;
            color: #fff;
        }

        .badge-completed {
            background-color: #28a745;
            color: #fff;
        }

        .badge-cancelled {
            background-color: #dc3545;
            color: #fff;
        }

        /* Scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Container padding for mobile */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .card-body {
                padding: 15px;
            }

            .btn {
                padding: 8px 15px;
            }

            .table-responsive {
                margin: 0 -15px;
            }
        }

        select option:disabled {
            color: #999;
            font-style: italic;
        }
    </style>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderStatusSelect = document.getElementById('order_status');
        const currentStatus = orderStatusSelect.value;

        // Định nghĩa thứ tự các trạng thái
        const statusOrder = {
            'pending': 0,
            'confirmed': 1,
            'shipping': 2,
            'completed': 3,
            'cancelled': 4
        };

        // Disable các option có thứ tự nhỏ hơn trạng thái hiện tại
        const options = orderStatusSelect.options;
        for (let i = 0; i < options.length; i++) {
            if (statusOrder[options[i].value] < statusOrder[currentStatus]) {
                options[i].disabled = true;
            }
        }

        // Nếu đơn hàng đã hoàn thành hoặc đã hủy, disable tất cả các option khác
        if (currentStatus === 'completed' || currentStatus === 'cancelled') {
            for (let i = 0; i < options.length; i++) {
                if (options[i].value !== currentStatus) {
                    options[i].disabled = true;
                }
            }
        }
    });

    orderStatusSelect.addEventListener('change', function(e) {
        const newStatus = e.target.value;

        if (statusOrder[newStatus] < statusOrder[currentStatus]) {
            alert('Không thể chuyển về trạng thái trước đó!');
            e.target.value = currentStatus;
            return false;
        }

        if ((currentStatus === 'completed' || currentStatus === 'cancelled') && newStatus !== currentStatus) {
            alert('Không thể thay đổi trạng thái của đơn hàng đã hoàn thành hoặc đã hủy!');
            e.target.value = currentStatus;
            return false;
        }
    });
</script>

</html>
