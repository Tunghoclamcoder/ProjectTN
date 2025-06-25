<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Auth;
@endphp
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/materialdesignicons.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <script src="{{ asset('js/alert.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <!-- ======== Preloader =========== -->
    <div id="preloader">
        <div class="spinner"></div>
    </div>
    <!-- ======== Preloader =========== -->

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

    <!-- ======== sidebar-nav start =========== -->
    <aside class="sidebar-nav-wrapper">
        <div class="navbar-logo">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    style="width: 200px; height: 70px; display:flex; justify-content: center;" />
            </a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item nav-item-has-children">
                    <a href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_1" aria-controls="ddmenu_1"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.74999 18.3333C12.2376 18.3333 15.1364 15.8128 15.7244 12.4941C15.8448 11.8143 15.2737 11.25 14.5833 11.25H9.99999C9.30966 11.25 8.74999 10.6903 8.74999 10V5.41666C8.74999 4.7263 8.18563 4.15512 7.50586 4.27556C4.18711 4.86357 1.66666 7.76243 1.66666 11.25C1.66666 15.162 4.83797 18.3333 8.74999 18.3333Z" />
                                <path
                                    d="M17.0833 10C17.7737 10 18.3432 9.43708 18.2408 8.75433C17.7005 5.14918 14.8508 2.29947 11.2457 1.75912C10.5629 1.6568 10 2.2263 10 2.91665V9.16666C10 9.62691 10.3731 10 10.8333 10H17.0833Z" />
                            </svg>
                        </span>
                        <span class="text">Dashboard</span>
                    </a>
                    <ul id="ddmenu_1" class="collapse show dropdown-nav">
                        <li>
                            <a href="index.html" class="active"> eCommerce </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item nav-item-has-children">
                    <a href="#0" class="collapsed" data-bs-toggle="collapse" data-bs-target="#ddmenu_2"
                        aria-controls="ddmenu_2" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11.8097 1.66667C11.8315 1.66667 11.8533 1.6671 11.875 1.66796V4.16667C11.875 5.43232 12.901 6.45834 14.1667 6.45834H16.6654C16.6663 6.48007 16.6667 6.50186 16.6667 6.5237V16.6667C16.6667 17.5872 15.9205 18.3333 15 18.3333H5.00001C4.07954 18.3333 3.33334 17.5872 3.33334 16.6667V3.33334C3.33334 2.41286 4.07954 1.66667 5.00001 1.66667H11.8097ZM6.66668 7.70834C6.3215 7.70834 6.04168 7.98816 6.04168 8.33334C6.04168 8.67851 6.3215 8.95834 6.66668 8.95834H10C10.3452 8.95834 10.625 8.67851 10.625 8.33334C10.625 7.98816 10.3452 7.70834 10 7.70834H6.66668ZM6.04168 11.6667C6.04168 12.0118 6.3215 12.2917 6.66668 12.2917H13.3333C13.6785 12.2917 13.9583 12.0118 13.9583 11.6667C13.9583 11.3215 13.6785 11.0417 13.3333 11.0417H6.66668C6.3215 11.0417 6.04168 11.3215 6.04168 11.6667ZM6.66668 14.375C6.3215 14.375 6.04168 14.6548 6.04168 15C6.04168 15.3452 6.3215 15.625 6.66668 15.625H13.3333C13.6785 15.625 13.9583 15.3452 13.9583 15C13.9583 14.6548 13.6785 14.375 13.3333 14.375H6.66668Z" />
                                <path
                                    d="M13.125 2.29167L16.0417 5.20834H14.1667C13.5913 5.20834 13.125 4.74197 13.125 4.16667V2.29167Z" />
                            </svg>
                        </span>
                        <span class="text">Quản lý</span>
                    </a>
                    <ul id="ddmenu_2" class="collapse dropdown-nav">
                        @if (Auth::guard('owner')->check())
                            <li>
                                <a href="{{ route('admin.employee') }}"
                                    class="{{ request()->routeIs('admin.employee') ? 'active' : '' }}">
                                    <i class="lni lni-users"></i> Quản lý Nhân viên
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('admin.customer') }}">
                                <i class="lni lni-user"></i> Quản lý Khách hàng
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.product') }}">
                                <i class="lni lni-package"></i> Quản lý Sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.category') }}">
                                <i class="lni lni-grid-alt"></i> Quản lý Danh mục
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.image') }}">
                                <i class="lni lni-image"></i> Quản lý Hình ảnh Sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.size') }}">
                                <i class="lni lni-ruler-alt"></i> Quản lý Size Sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="{{ route(name: 'admin.brand') }}">
                                <i class="lni lni-flag"></i> Quản lý Thương hiệu
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.material') }}">
                                <i class="lni lni-layers"></i> Quản lý Chất liệu
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.payment') }}">
                                <i class="lni lni-credit-cards"></i> Quản lý Phương thức thanh toán
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.shipping') }}">
                                <i class="lni lni-delivery"></i> Quản lý Phương thức vận chuyển
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.voucher') }}">
                                <i class="lni lni-ticket"></i> Quản lý Voucher
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.order') }}">
                                <i class="lni lni-shopping-basket"></i> Quản lý Đơn hàng
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.charts') }}">
                        <span class="icon">
                            <i class="lni lni-bar-chart" style="font-size: 1.3em;"></i>
                        </span>
                        <span class="text">Quản lý biểu đồ</span>
                    </a>
                </li>

                <span class="divider">
                    <hr />
                </span>

                <li class="nav-title mt-2 mb-1 text-uppercase text-muted d-flex justify-content-center  "
                    style="font-size: 12px; letter-spacing: 1px;">
                    Hỗ trợ & Thông tin
                </li>
                <li class="nav-item">
                    <a href="#!" onclick="alert('Chức năng đang phát triển!')">
                        <span class="icon"><i class="lni lni-question-circle"></i></span>
                        <span class="text">Trợ giúp</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#!" onclick="alert('Chức năng đang phát triển!')">
                        <span class="icon"><i class="lni lni-book"></i></span>
                        <span class="text">Tài liệu hướng dẫn</span>
                    </a>
                </li>

                <span class="divider">
                    <hr />
                </span>

                <li class="nav-item">
                    <a href="#!" onclick="alert('Chức năng đang phát triển!')">
                        <span class="icon"><i class="lni lni-envelope"></i></span>
                        <span class="text">Liên hệ hỗ trợ</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#!" onclick="alert('Chức năng đang phát triển!')">
                        <span class="icon"><i class="lni lni-crown"></i></span>
                        <span class="text">Nâng cấp tài khoản</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#!" onclick="alert('Chức năng đang phát triển!')">
                        <span class="icon"><i class="lni lni-star"></i></span>
                        <span class="text">Đánh giá hệ thống</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="overlay"></div>
    <!-- ======== sidebar-nav end =========== -->

    <!-- ======== main-wrapper start =========== -->
    <main class="main-wrapper">

        @include('management.components.admin-header')

        <!-- ========== section start ========== -->
        <section class="section">
            <div class="container-fluid">
                <!-- ========== title-wrapper start ========== -->
                <div class="title-wrapper pt-30">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="title">
                                <h2>Admin Dashboard</h2>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-md-6">
                            <div class="breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="#0">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Thống kê
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- ========== title-wrapper end ========== -->
                <div class="row">
                    <!-- Tổng số khách hàng -->
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="icon-card mb-30">
                            <div class="icon orange">
                                <i class="lni lni-users"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-10">Tổng khách hàng</h6>
                                <h3 class="text-bold mb-10">{{ $totalCustomers }}</h3>
                                <p class="text-sm">
                                    <span class="text-gray">Khách hàng đang hoạt động</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn hàng thành công -->
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="icon-card mb-30">
                            <div class="icon success">
                                <i class="lni lni-checkmark-circle"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-10">Đơn hàng thành công</h6>
                                <h3 class="text-bold mb-10">{{ $completedOrders }}</h3>
                                <p class="text-sm">
                                    <span class="text-gray">Đã vận chuyển</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tổng doanh thu -->
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="icon-card mb-30">
                            <div class="icon purple">
                                <i class="lni lni-dollar"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-10">Tổng doanh thu</h6>
                                <h3 class="text-bold mb-10">{{ number_format($totalRevenue) }}đ</h3>
                                <p class="text-sm">
                                    <span class="text-gray">Từ đơn hàng thành công</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Đơn hàng chờ xác nhận -->
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="icon-card mb-30">
                            <div class="icon primary">
                                <i class="lni lni-timer"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-10">Đơn hàng chờ xác nhận</h6>
                                <h3 class="text-bold mb-10">{{ $pendingOrders }}</h3>
                                <p class="text-sm">
                                    <span class="text-gray">Đang chờ xử lý</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Row -->
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card-style mb-30" style="max-height: 420px; overflow-y: auto;">
                            <div class="title d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div class="left">
                                    <h6 class="text-medium mb-10">Tất cả đánh giá của khách hàng</h6>
                                </div>
                            </div>
                            <div>
                                @forelse($allFeedbacks as $feedback)
                                    <div
                                        class="d-flex align-items-center justify-content-between border-bottom py-2 px-1">
                                        <div>
                                            <div class="fw-bold" style="color:#1a237e;">
                                                {{ $feedback->customer->customer_name ?? 'Khách hàng' }}
                                            </div>
                                            <div class="small text-muted mb-1">
                                                Đơn hàng #{{ $feedback->order_id }}
                                            </div>
                                            <div>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star{{ $i <= $feedback->rating ? ' text-warning' : ' text-secondary' }}"></i>
                                                @endfor
                                            </div>
                                            <div class="fst-italic mt-1" style="color:#444;">
                                                "{{ $feedback->comment }}"
                                            </div>
                                        </div>
                                        <form action="{{ route('admin.feedback.delete', $feedback->feedback_id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa feedback này?')"
                                            style="margin-left: 10px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link p-0" data-bs-toggle="tooltip"
                                                title="Xóa feedback">
                                                <i class="lni lni-trash-can text-danger"
                                                    style="font-size: 1.3em;"></i>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">Chưa có feedback nào.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- End Col -->
                    <div class="col-lg-5">
                        <div class="card-style mb-30">
                            <div class="title d-flex flex-wrap justify-content-between">
                                <div class="left">
                                    <h6 class="text-medium mb-10">Thống kê doanh thu</h6>
                                    <h3 class="text-bold">{{ number_format($totalRevenue) }}đ</h3>
                                </div>
                                <div class="right">
                                    <div class="select-style-1">
                                        <div class="select-position select-sm">
                                            <select class="light-bg" id="revenueFilter">
                                                <option value="7days" selected>7 ngày qua</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chart">
                                <canvas id="Chart2" style="width: 100%; height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
                <div class="col-lg-12">
                    <div class="card-style mb-30">
                        <div class="title d-flex flex-wrap justify-content-between align-items-center">
                            <div class="left">
                                <h6 class="text-medium mb-30">Sản phẩm bán chạy nhất</h6>
                            </div>
                        </div>
                        <!-- End Title -->
                        <div class="table-responsive">
                            <table class="table top-selling-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <h6 class="text-sm text-medium">Sản phẩm
                                        </th>
                                        <th style="width: 180px">
                                            <h6 class="text-sm text-medium">Danh mục</h6>
                                        </th>
                                        <th class="min-width">
                                            <h6 class="text-sm text-medium">Đơn giá</h6>
                                        </th>
                                        <th class="min-width">
                                            <h6 class="text-sm text-medium">Giá bán</h6>
                                        </th>
                                        <th class="min-width">
                                            <h6 class="text-sm text-medium">Số lượng bán ra</h6>
                                        </th>
                                        <th class="min-width">
                                            <h6 class="text-sm text-medium">Doanh thu</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topSellingProducts as $product)
                                        <tr>
                                            <td>
                                                <div class="product">
                                                    <div class="image">
                                                        @if ($product->image_url)
                                                            <img src="{{ Storage::url($product->image_url) }}"
                                                                alt="{{ $product->product_name }}"
                                                                style="width: 60px; height: 60px; object-fit: cover;" />
                                                        @else
                                                            <img src="{{ asset('images/no-image.png') }}"
                                                                alt="No image"
                                                                style="width: 60px; height: 60px; object-fit: cover;" />
                                                        @endif
                                                    </div>
                                                    <p class="text-sm">{{ $product->product_name }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm">{{ $product->category_name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm">{{ number_format($product->price) }}đ</p>
                                            </td>
                                            <td>
                                                <p class="text-sm">{{ number_format($product->total_revenue) }}đ</p>
                                            </td>
                                            <td>
                                                <p class="text-sm" style="display: flex; justify-content: center;">
                                                    {{ $product->total_sold }}</p>
                                            </td>
                                            <td>
                                                <div class="action justify-content-center">
                                                    <a href="{{ route('admin.product.edit', $product->product_id) }}"
                                                        class="text-gray">
                                                        <i class="lni lni-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Row -->
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card-style mb-30">
                            <h6 class="mb-10">Top thương hiệu bán chạy</h6>
                            <div id="chartContainer" style="position: relative; height: 400px;">
                                <canvas id="brandChart"></canvas>
                                <div id="chartError" class="text-center text-danger mt-3" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End Col -->
                    <div class="col-lg-5">
                        <div class="card-style mb-30">
                            <div class="title d-flex flex-wrap align-items-center justify-content-between">
                                <div class="left">
                                    <h6 class="text-medium mb-2">Thống kê đơn hàng đã hoàn thành và bị hủy</h6>
                                </div>
                                <div class="right">
                                    <div class="select-style-1 mb-2">
                                        <div class="select-position select-sm">
                                            <select class="bg-ligh">
                                                <option value="">Last 7 days</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- end select -->
                                </div>
                            </div>
                            <!-- End Title -->
                            <div class="chart">
                                <div id="legend4">
                                    <ul class="legend3 d-flex flex-wrap gap-3 gap-sm-0 align-items-center mb-30">
                                        <li>
                                            <div class="d-flex">
                                                <span class="bg-color primary-bg"></span>
                                                <div class="text">
                                                    <p
                                                        class="text-sm {{ $completedTrend >= 0 ? 'text-success' : 'text-danger' }}">
                                                        <span class="text-dark">Đơn hoàn thành</span>
                                                        {{ $completedTrend >= 0 ? '+' : '' }}{{ $completedTrend }}%
                                                        <i
                                                            class="lni {{ $completedTrend >= 0 ? 'lni-arrow-up' : 'lni-arrow-down' }}"></i>
                                                    </p>
                                                    <h2>{{ $completedThisWeek }}</h2>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <span class="bg-color danger-bg"></span>
                                                <div class="text">
                                                    <p
                                                        class="text-sm {{ $canceledTrend >= 0 ? 'text-danger' : 'text-success' }}">
                                                        <span class="text-dark">Đơn hủy</span>
                                                        {{ $canceledTrend >= 0 ? '+' : '' }}{{ $canceledTrend }}%
                                                        <i
                                                            class="lni {{ $canceledTrend >= 0 ? 'lni-arrow-up' : 'lni-arrow-down' }}"></i>
                                                    </p>
                                                    <h2>{{ $canceledThisWeek }}</h2>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <canvas id="Chart4"
                                    style="width: 100%; height: 420px; margin-left: -35px;"></canvas>
                            </div>
                            <!-- End Chart -->
                        </div>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card-style calendar-card mb-30">
                            <div id="calendar-mini"></div>
                        </div>
                    </div>
                    <!-- End Col -->
                    <div class="col-lg-7">
                        <div class="card-style mb-30">
                            <div class="title d-flex flex-wrap align-items-center justify-content-between">
                                <div class="left">
                                    <h6 class="text-medium mb-30">Đơn hàng mới nhất</h6>
                                </div>
                                <div class="right">
                                    <div class="select-style-1">
                                        <div class="select-position select-sm">
                                            <select class="light-bg" id="orderTimeFilter">
                                                <option value="today">Hôm nay</option>
                                                <option value="week">Tuần này</option>
                                                <option value="month">Tháng này</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table top-selling-table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <h6 class="text-sm text-medium">Khách hàng</h6>
                                            </th>
                                            <th class="min-width">
                                                <h6 class="text-sm text-medium">Ngày đặt</h6>
                                            </th>
                                            <th class="min-width">
                                                <h6 class="text-sm text-medium">Tổng tiền</h6>
                                            </th>
                                            <th class="min-width">
                                                <h6 class="text-sm text-medium">Trạng thái</h6>
                                            </th>
                                            <th>
                                                <h6 class="text-sm text-medium text-end">Thao tác</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestOrders as $order)
                                            <tr>
                                                <td>
                                                    <div class="customer">
                                                        <div class="info">
                                                            <h6 class="text-sm">
                                                                {{ $order->customer->customer_name ?? 'N/A' }}</h6>
                                                            <p class="text-sm text-muted">
                                                                {{ $order->customer->email ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-sm">{{ $order->order_date->format('d/m/Y') }}
                                                    </p>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="text-sm">
                                                        {{ number_format($order->getTotalAmount()) }} VNĐ
                                                        @if ($order->voucher)
                                                            <br>
                                                            <small class="text-success">
                                                                <i class="bi bi-tag-fill"></i> Đã áp dụng voucher giảm
                                                                giá
                                                            </small>
                                                        @endif
                                                    </p>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClasses = [
                                                            'pending' => 'warning',
                                                            'confirmed' => 'info',
                                                            'shipping' => 'primary',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                            'returned' => 'secondary',
                                                        ];
                                                        $statusLabels = [
                                                            'pending' => 'Chờ xác nhận',
                                                            'confirmed' => 'Đã xác nhận',
                                                            'shipping' => 'Đang giao hàng',
                                                            'completed' => 'Đã hoàn thành',
                                                            'cancelled' => 'Đã hủy',
                                                            'returned' => 'Đã hoàn trả',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $statusClasses[$order->order_status] ?? 'secondary' }}">
                                                        {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action justify-content-end">
                                                        <a href="{{ route('admin.order.show', $order->order_id) }}"
                                                            class="btn btn-sm btn-outline-info"
                                                            data-bs-toggle="tooltip" title="Xem chi tiết">
                                                            <i class="lni lni-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <p class="text-muted">Chưa có đơn hàng nào</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- end container -->
        </section>
        <!-- ========== section end ========== -->


    </main>
    <!-- ======== main-wrapper end =========== -->

    <!-- ========= All Javascript files linkup ======== -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/dynamic-pie-chart.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar.js') }}"></script>
    <script src="{{ asset('js/jvectormap.min.js') }}"></script>
    <script src="{{ asset('js/world-merc.js') }}"></script>
    <script src="{{ asset('js/polyfill.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <style>
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .search-suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .search-suggestion-item:hover {
            background-color: #f5f5f5;
        }

        .search-suggestion-item .product-name {
            display: block;
            font-weight: bold;
        }

        .search-suggestion-item .product-category {
            display: block;
            font-size: 0.9em;
            color: #666;
        }

        .no-results {
            padding: 15px;
            text-align: center;
            color: #666;
        }

        .customer .info {
            padding: 8px 0;
        }

        .customer .info h6 {
            margin-bottom: 2px;
            font-weight: 500;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .badge {
            padding: 6px 12px;
            font-weight: normal;
        }

        .action .btn-info {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .action .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        /* CSS pie-chart */
        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }

        .card-style {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .select-sm {
            min-width: 120px;
        }

        .chart-wrapper {
            isolation: isolate;
            /* Tạo ngữ cảnh stack riêng biệt */
            z-index: 1;
        }
    </style>

    <script>
        // ====== calendar activation
        document.addEventListener("DOMContentLoaded", function() {
            //JS xử lý search sản phẩm Dashboard
            const searchInput = document.getElementById('adminSearchInput');
            const suggestionsBox = document.getElementById('adminSearchSuggestions');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`/admin/search/suggestions?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(suggestions => {
                            suggestionsBox.innerHTML = '';

                            if (suggestions.length > 0) {
                                suggestions.forEach(item => {
                                    const div = document.createElement('div');
                                    div.className = 'search-suggestion-item';
                                    div.innerHTML = `
                                <span class="product-name">${item.name}</span>
                                <span class="product-category">${item.category}</span>
                            `;
                                    div.addEventListener('click', () => {
                                        searchInput.value = item.name;
                                        suggestionsBox.style.display = 'none';
                                        document.getElementById(
                                            'adminSearchForm').submit();
                                    });
                                    suggestionsBox.appendChild(div);
                                });
                                suggestionsBox.style.display = 'block';
                            } else {
                                suggestionsBox.innerHTML =
                                    '<div class="no-results">Không tìm thấy kết quả</div>';
                                suggestionsBox.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });

            var calendarMiniEl = document.getElementById("calendar-mini");
            var calendarMini = new FullCalendar.Calendar(calendarMiniEl, {
                initialView: "dayGridMonth",
                headerToolbar: {
                    end: "today prev,next",
                },
            });
            calendarMini.render();
        });

        // Khởi tạo tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Xử lý thay đổi bộ lọc thời gian
        const orderTimeFilter = document.getElementById('orderTimeFilter');
        if (orderTimeFilter) {
            orderTimeFilter.addEventListener('change', function() {
                // Thêm lệnh gọi AJAX ở đây để làm mới đơn hàng dựa trên khoảng thời gian đã chọn
                fetch(`/admin/dashboard/latest-orders?period=${this.value}`)
                    .then(response => response.json())
                    .then(data => updateOrdersTable(data));
            });
        }
        // =========== chart two start
        const ctx2 = document.getElementById("Chart2").getContext("2d");
        const chart2 = new Chart(ctx2, {
            type: "bar",
            data: {
                labels: @json($last7Days),
                datasets: [{
                    label: "Doanh thu",
                    backgroundColor: "#365CF5",
                    borderRadius: 30,
                    barThickness: 6,
                    maxBarThickness: 8,
                    data: @json($dailyRevenue),
                }],
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(context.raw);
                            }
                        },
                        backgroundColor: "#F3F6F8",
                        titleAlign: "center",
                        bodyAlign: "center",
                        titleFont: {
                            size: 12,
                            weight: "bold",
                            color: "#8F92A1",
                        },
                        bodyFont: {
                            size: 16,
                            weight: "bold",
                            color: "#171717",
                        },
                        displayColors: false,
                        padding: {
                            x: 30,
                            y: 10,
                        },
                    },
                    legend: {
                        display: false,
                    }
                },
                layout: {
                    padding: {
                        top: 15,
                        right: 15,
                        bottom: 15,
                        left: 15,
                    },
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        grid: {
                            display: false,
                            drawTicks: false,
                            drawBorder: false,
                        },
                        ticks: {
                            padding: 35,
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        },
                        min: 0,
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                            color: "rgba(143, 146, 161, .1)",
                            drawTicks: false,
                            zeroLineColor: "rgba(143, 146, 161, .1)",
                        },
                        ticks: {
                            padding: 20,
                        }
                    }
                }
            }
        });
        // =========== chart two end

        // =========== chart three start
        document.addEventListener('DOMContentLoaded', function() {
            const chartContainer = document.getElementById('chartContainer');
            const canvas = document.getElementById('brandChart');
            const errorDiv = document.getElementById('chartError');

            fetch('{{ route('admin.dashboard.brandStats') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }
                    return data;
                })
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.message || 'Không có dữ liệu');
                    }

                    const data = result.data;
                    if (!data || data.length === 0) {
                        throw new Error('Không có dữ liệu thương hiệu');
                    }

                    // Create chart with data
                    new Chart(canvas, {
                        type: 'doughnut',
                        data: {
                            labels: data.map(item => item.brand_name),
                            datasets: [{
                                data: data.map(item => item.percentage),
                                backgroundColor: [
                                    '#8901dc',
                                    '#01dc8c',
                                    '#ebf15b',
                                    '#ADC2FD',
                                    '#0139dc'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const item = data[context.dataIndex];
                                            return `${item.brand_name}: ${item.percentage}% (${item.total_sales} đơn)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorDiv.style.display = 'block';
                    errorDiv.innerHTML = `
            <div class="alert alert-danger">
                <p>Không thể tải dữ liệu biểu đồ</p>
                <small>${error.message}</small>
            </div>
        `;
                });
        });
        // =========== chart three end

        // ================== chart four start
        const ctx4 = document.getElementById("Chart4").getContext("2d");
        const chart4 = new Chart(ctx4, {
            type: "bar",
            data: {
                labels: @json($orderChartLabels),
                datasets: [{
                        label: "Đơn hoàn thành",
                        backgroundColor: "#365CF5",
                        borderColor: "transparent",
                        borderRadius: 20,
                        borderWidth: 5,
                        barThickness: 20,
                        maxBarThickness: 20,
                        data: @json($completedCounts),
                    },
                    {
                        label: "Đơn hủy",
                        backgroundColor: "#d50100",
                        borderColor: "transparent",
                        borderRadius: 20,
                        borderWidth: 5,
                        barThickness: 20,
                        maxBarThickness: 20,
                        data: @json($canceledCounts),
                    },
                ],
            },
            options: {
                plugins: {
                    tooltip: {
                        backgroundColor: "#F3F6F8",
                        titleColor: "#8F92A1",
                        titleFontSize: 12,
                        bodyColor: "#171717",
                        bodyFont: {
                            weight: "bold",
                            size: 16,
                        },
                        multiKeyBackground: "transparent",
                        displayColors: true,
                        padding: {
                            x: 30,
                            y: 10,
                        },
                        bodyAlign: "center",
                        titleAlign: "center",
                        enabled: true,
                    },
                    legend: {
                        display: true,
                    },
                },
                layout: {
                    padding: {
                        top: 0,
                    },
                },
                responsive: true,
                // maintainAspectRatio: false,
                title: {
                    display: false,
                },
                scales: {
                    y: {
                        grid: {
                            display: false,
                            drawTicks: false,
                            drawBorder: false,
                        },
                        ticks: {
                            padding: 35,
                            min: 0,
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                            color: "rgba(143, 146, 161, .1)",
                            zeroLineColor: "rgba(143, 146, 161, .1)",
                        },
                        ticks: {
                            padding: 20,
                        },
                    },
                },
            },
        });
        // =========== chart four end
    </script>
</body>
<style>
    .alert {
        position: fixed;
        top: -100px;
        /* Start off-screen */
        left: 50%;
        transform: translateX(-50%);
        min-width: 300px;
        padding: 15px;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        opacity: 0;
        transition: all 0.5s ease-in-out;
        display: flex;
        justify-content: center;
    }

    .alert.show {
        top: 20px;
        opacity: 1;
    }

    .alert.fade-out {
        opacity: 0;
        top: -100px;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #fa0019;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }
</style>

</html>
