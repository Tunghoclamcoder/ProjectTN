<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Phương thức vận chuyển</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('js/alert.js') }}"></script>
</head>

<body>
    @include('management.components.admin-header')

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
                        <h4>Chỉnh sửa Phương thức vận chuyển</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shipping.update', $shippingMethod->method_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label>Tên phương thức vận chuyển</label>
                                <input type="text" name="method_name"
                                    class="form-control @error('method_name') is-invalid @enderror"
                                    value="{{ $shippingMethod->method_name }}" required>
                                @error('method_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="shipping_fee">Phí vận chuyển (VNĐ)</label>
                                <input type="number" class="form-control" id="shipping_fee" name="shipping_fee"
                                    value="{{ old('shipping_fee', $shippingMethod->shipping_fee) }}" min="0"
                                    required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.shipping') }}" class="btn btn-secondary">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
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
        }
    }
</script>

</html>
