<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Phương thức vận chuyển mới</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/alert.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <h4>Thêm Phương thức vận chuyển mới</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shipping.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label>Tên Phương thức vận chuyển mới</label>
                                <input type="text" name="method_name"
                                    class="form-control @error('method_name') is-invalid @enderror" required>
                                @error('method_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="shipping_fee">Phí vận chuyển (VNĐ)</label>
                                <input type="number" class="form-control" id="shipping_fee" name="shipping_fee"
                                    value="{{ old('shipping_fee') }}" min="0" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.shipping') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Thêm Phương thức vận chuyển</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script>

</html>
