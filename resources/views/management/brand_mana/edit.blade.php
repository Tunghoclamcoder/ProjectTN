<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Thương hiệu</title>
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
                        <h4>Chỉnh sửa thương hiệu</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.brand.update', ['brand' => $brand->brand_id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label>Tên thương hiệu</label>
                                <input type="text" name="brand_name" class="form-control" required
                                    value="{{ $brand->brand_name }}">
                            </div>
                            <div class="form-group mb-3">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control" rows="4">{{ $brand->description }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Logo thương hiệu</label>
                                <input type="file" name="brand_image" id="brand_image"
                                    class="form-control @error('brand_image') is-invalid @enderror" accept="image/*"
                                    onchange="previewImage(this)">
                                @error('brand_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="mt-2">
                                    <img id="preview" src="{{ asset('storage/' . $brand->brand_image) }}"
                                        alt="Preview"
                                        style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.brand') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Cập nhật thương hiệu</button>
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
