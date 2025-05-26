<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa ảnh sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('js/alert.js') }}"></script>
</head>

<body>
    @include('components.admin-header')

    {{-- Thông báo  --}}
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

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Chỉnh sửa ảnh sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.image.update', $image->image_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label>Hình ảnh hiện tại</label>
                                <div class="current-image mb-2">
                                    @if (Storage::disk('public')->exists($image->image_url))
                                        <img src="{{ Storage::url($image->image_url) }}" alt="Current Image"
                                            class="img-thumbnail" style="max-height: 200px;">
                                    @else
                                        <p class="text-muted">Không tìm thấy hình ảnh</p>
                                    @endif
                                </div>

                                <label>Chọn hình ảnh mới</label>
                                <input type="file" name="image"
                                    class="form-control @error('image') is-invalid @enderror" accept="image/*"
                                    onchange="previewImage(this)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <div class="preview-container mb-3">
                                <label>Hình ảnh mới</label>
                                <img id="preview" src="#" alt="Preview"
                                    style="max-height: 200px; display: none;" class="img-thumbnail">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.image') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</html>
