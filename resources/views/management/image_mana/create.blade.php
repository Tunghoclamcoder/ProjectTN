<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm ảnh mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('components.admin-header')

    {{-- Thông báo  --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4>Thêm ảnh mới (Tối đa 4 ảnh)</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.image.store') }}" method="POST" enctype="multipart/form-data"
                            id="uploadForm">
                            @csrf

                            @for ($i = 0; $i < 4; $i++)
                                <div class="preview-container">
                                    <div class="form-group">
                                        <label>Ảnh {{ $i + 1 }}</label>
                                        <input type="file" name="images[]"
                                            class="form-control image-input @error('images.' . $i) is-invalid @enderror"
                                            accept="image/*" onchange="previewImage(this, {{ $i }})">
                                        @error('images.' . $i)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <img id="preview-{{ $i }}" src="#"
                                        alt="Preview {{ $i + 1 }}" class="image-preview">
                                </div>
                            @endfor

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('admin.image') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Tải lên</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    function previewImage(input, index) {
        const preview = document.getElementById('preview-' + index);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    // Automatically hide alerts after 3 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 3000);
</script>
<style>
    .preview-image-container {
        position: relative;
        margin-bottom: 15px;
    }

    .image-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        display: none;
    }

    .preview-container {
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        padding: 5px 10px;
        cursor: pointer;
    }

    .invalid-feedback {
        display: block;
    }
</style>

</html>
