<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Sản phẩm</title>
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
                            <h2>Quản lý <b>Sản phẩm</b></h2>
                            <a href="{{ route('admin.product.create') }}" class="btn btn-success mt-2 mb-4">
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
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Thương hiệu</th>
                                <th>Chất liệu</th>
                                <th>Danh mục</th>
                                <th>Giá gốc</th>
                                <th>Giảm giá</th>
                                <th>Giá sau giảm</th>
                                <th>Số lượng</th>
                                <th>Size</th>
                                <th>Trạng thái</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->product_id }}</td>
                                    <td style="width: 100px; height: 100px">
                                        @if ($product->getMainImage() && Storage::disk('public')->exists($product->getMainImage()->image_url))
                                            <img src="{{ Storage::url($product->getMainImage()->image_url) }}"
                                                alt="{{ $product->product_name }}"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/placeholder.png') }}" alt="Placeholder"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        @endif
                                        <small class="d-block text-muted">
                                            ({{ $product->NumberOfImage }} ảnh)
                                        </small>
                                    </td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                    <td>{{ $product->material->material_name ?? 'N/A' }}</td>
                                    <td>
                                        @foreach ($product->categories as $category)
                                            <span>{{ $category->category_name }}</span>
                                        @endforeach
                                        <small class="d-block text-muted">
                                            ({{ $product->NumberOfCategory }} danh mục)
                                        </small>
                                    </td>
                                    <td>{{ number_format($product->price, 0, ',', '.') }}đ</td>
                                    <td>{{ floatval($product->discount) }}%</td>
                                    <td>{{ number_format($product->getDiscountedPrice(), 0, ',', '.') }}đ</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ $product->size->size_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->status ? 'Đang bán' : 'Ngừng kinh doanh' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.product.show', ['product' => $product->product_id]) }}"
                                            class="view" title="Xem chi tiết" data-toggle="tooltip">
                                            <i class="material-icons">&#xE417;</i>
                                        </a>
                                        <a href="{{ route('admin.product.edit', ['product' => $product->product_id]) }}"
                                            class="edit" title="Sửa" data-toggle="tooltip">
                                            <i class="material-icons">&#xE254;</i>
                                        </a>
                                        <form action="{{ route('admin.product.delete', $product->product_id) }}"
                                            method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Xóa" data-toggle="tooltip"
                                                style="color: red"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
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
                                <span class="total-records">{{ $products->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $products->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $products->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $products->currentPage() >= $products->lastPage() ? 'disabled' : '' }}>
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
        const currentPage = {{ $products->currentPage() }};
        const totalPages = {{ $products->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $products->url($products->currentPage() + 1) }}";
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
