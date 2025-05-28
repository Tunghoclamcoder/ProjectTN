<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Voucher</title>
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
                            <h2>Quản lý <b>Voucher</b></h2>
                            <a href="{{ route('admin.voucher.create') }}" class="btn btn-success mt-2 mb-4">
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
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mã Voucher</th>
                                <th>Giảm giá</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Số tiền tối thiểu</th>
                                <th>Số tiền giảm tối đa</th>
                                <th>Số lần sử dụng</th>
                                <th>Đã sử dụng</th>
                                <th>Trạng thái</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vouchers as $voucher)
                                <tr>
                                    <td>{{ $voucher->id }}</td>
                                    <td>{{ $voucher->code }}</td>
                                    <td>
                                        @if ($voucher->discount_amount)
                                            {{ number_format($voucher->discount_amount) }} VNĐ
                                        @else
                                            {{ $voucher->discount_percentage }}%
                                        @endif
                                    </td>
                                    <td>{{ $voucher->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $voucher->expiry_date->format('d/m/Y') }}</td>
                                    <td>{{ number_format($voucher->minimum_purchase_amount) }} VNĐ</td>
                                    <td>{{ number_format($voucher->maximum_purchase_amount) }} VNĐ</td>
                                    <td>{{ $voucher->max_usage_count ?: 'Không giới hạn' }}</td>
                                    <td>{{ $voucher->usage_count }}</td>
                                    <td>
                                        <span class="badge {{ $voucher->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $voucher->status ? 'Đang kích hoạt' : 'Đã hết hạn' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.voucher.edit', $voucher->id) }}" class="edit"
                                                title="Sửa">
                                                <i class="material-icons">&#xE254;</i>
                                            </a>

                                            <form action="{{ route('admin.voucher.toggle', $voucher->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="toggle-btn {{ $voucher->status ? 'active' : 'inactive' }}"
                                                    title="{{ $voucher->status ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                                    <i class="material-icons">
                                                        {{ $voucher->status ? 'toggle_on' : 'toggle_off' }}
                                                    </i>
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.voucher.delete', $voucher->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete" title="Xóa"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa voucher này không?')"
                                                    style="background: none; border: none; padding: 5px;">
                                                    <i class="material-icons">&#xE872;</i>
                                                </button>
                                            </form>
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
                                <span class="total-records">{{ $vouchers->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $vouchers->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $vouchers->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $vouchers->currentPage() >= $vouchers->lastPage() ? 'disabled' : '' }}>
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
        const currentPage = {{ $vouchers->currentPage() }};
        const totalPages = {{ $vouchers->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $vouchers->url($vouchers->currentPage() + 1) }}";
        }
    }

    // Tự động ẩn alert sau 5 giây
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").alert('close');
        }, 5000);
    });
</script>

</html>
