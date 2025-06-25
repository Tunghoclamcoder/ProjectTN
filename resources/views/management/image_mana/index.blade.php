<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Ảnh sản phẩm</title>
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
                            <h2>Quản lý <b>Ảnh sản phẩm</b></h2>
                            <a href="{{ route(name: 'admin.image.create') }}" class="btn btn-success mt-2 mb-4">
                                <i class="material-icons">&#xE147;</i>
                                <span>Thêm mới</span>
                            </a>
                        </div>

                    </div>
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Đường dẫn</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($images as $image)
                                <tr>
                                    <td>{{ $image->image_id }}</td>
                                    <td style="width: 100px;">
                                        @php
                                            $filePath = str_replace('storage/', '', $image->image_url);
                                        @endphp
                                        @if (Storage::disk('public')->exists($filePath))
                                            <img src="{{ asset($image->image_url) }}" alt="Image Preview"
                                                style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $image->image_url }}</td>
                                    <td>
                                        <a href="{{ route('admin.image.edit', ['image' => $image->image_id]) }}"
                                            class="edit" title="Sửa" data-toggle="tooltip">
                                            <i class="material-icons">&#xE254;</i>
                                        </a>
                                        <form action="{{ route('admin.image.delete', $image->image_id) }}"
                                            method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Xóa" data-toggle="tooltip"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa hình ảnh này không? Hình ảnh sẽ bị xóa khỏi tất cả sản phẩm liên quan.')">
                                                <i class="material-icons">&#xE872;</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($images->isEmpty())
                        <div class="text-center p-3">
                            <p>Chưa có hình ảnh nào được tải lên.</p>
                        </div>
                    @endif

                    <div class="clearfix">
                        <div class="footer-container">
                            <div class="pagination-info">
                                <span>Tổng số lượng : </span>
                                <span class="total-records">{{ $images->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $images->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $images->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $images->currentPage() >= $images->lastPage() ? 'disabled' : '' }}>
                                    <span>Trang tiếp</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>

<script>
    function nextPage() {
        const currentPage = {{ $images->currentPage() }};
        const totalPages = {{ $images->lastPage() }};

        if (currentPage < totalPages) {
            window.location.href = "{{ $images->url($images->currentPage() + 1) }}";
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
