<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Voucher mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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

    @if (config('app.debug'))
        <div class="card mt-4">
            <div class="card-header">
                <h5>Debug Information</h5>
            </div>
            <div class="card-body">
                <h6>Form Data:</h6>
                <pre>{{ print_r(old(), true) }}</pre>

                <h6>Validation Errors:</h6>
                <pre>{{ $errors->any() ? print_r($errors->all(), true) : 'No errors' }}</pre>

                <h6>Session Messages:</h6>
                <pre>
                Success: {{ session('success') ?? 'None' }}
                Error: {{ session('error') ?? 'None' }}
            </pre>
            </div>
        </div>
    @endif

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-13">
                <div class="card">
                    <div class="card-header">
                        <h4>Thêm Voucher mới</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.voucher.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Mã Voucher <span class="text-danger">*</span></label>
                                        <input type="text" name="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            value="{{ old('code') }}" required placeholder="VD: SUMMER2025">
                                        <small class="text-muted">Mã voucher phải là duy nhất</small>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Số tiền giảm (VNĐ)</label>
                                        <div class="input-group">
                                            <input type="number" name="discount_amount"
                                                class="form-control @error('discount_amount') is-invalid @enderror"
                                                value="{{ old('discount_amount') }}" min="0" step="1000"
                                                placeholder="Nhập số tiền giảm">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                        <small class="text-muted">Chọn một trong hai: Số tiền giảm hoặc Phần trăm
                                            giảm</small>
                                        @error('discount_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Phần trăm giảm</label>
                                        <div class="input-group">
                                            <input type="number" name="discount_percentage"
                                                class="form-control @error('discount_percentage') is-invalid @enderror"
                                                value="{{ old('discount_percentage') }}" min="0" max="100"
                                                step="0.1" placeholder="Nhập phần trăm giảm">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('discount_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Số lần sử dụng tối đa</label>
                                        <input type="number" name="max_usage_count"
                                            class="form-control @error('max_usage_count') is-invalid @enderror"
                                            value="{{ old('max_usage_count') }}" min="1"
                                            placeholder="Để trống nếu không giới hạn">
                                        <small class="text-muted">Số lần voucher có thể được sử dụng</small>
                                        @error('max_usage_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Ngày bắt đầu <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Ngày hết hạn <span class="text-danger">*</span></label>
                                        <input type="date" name="expiry_date"
                                            class="form-control @error('expiry_date') is-invalid @enderror"
                                            value="{{ old('expiry_date') }}" required>
                                        @error('expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Số tiền đơn hàng tối thiểu</label>
                                        <div class="input-group">
                                            <input type="number" name="minimum_purchase_amount"
                                                class="form-control @error('minimum_purchase_amount') is-invalid @enderror"
                                                value="{{ old('minimum_purchase_amount') }}" min="0"
                                                step="1000" placeholder="Nhập số tiền tối thiểu">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                        <small class="text-muted">Đơn hàng phải đạt giá trị tối thiểu này để áp dụng
                                            voucher</small>
                                        @error('minimum_purchase_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Số tiền giảm tối đa</label>
                                        <div class="input-group">
                                            <input type="number" name="maximum_purchase_amount"
                                                class="form-control @error('maximum_purchase_amount') is-invalid @enderror"
                                                value="{{ old('maximum_purchase_amount') }}" min="0"
                                                step="1000" placeholder="Nhập số tiền giảm tối đa">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                        <small class="text-muted">Giới hạn số tiền giảm tối đa cho mỗi đơn hàng</small>
                                        @error('maximum_purchase_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>
                                        Đang kích hoạt
                                    </option>
                                    <option value="0" {{ old('status', '1') === '0' ? 'selected' : '' }}>
                                        Đã hết hạn
                                    </option>
                                </select>
                                <small class="text-muted">Chỉ những voucher đang kích hoạt mới có thể sử dụng</small>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.voucher') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tạo Voucher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const discountAmount = document.querySelector('[name="discount_amount"]');
                const discountPercentage = document.querySelector('[name="discount_percentage"]');

                function updateDiscountFields() {
                    if (discountAmount.value) {
                        discountPercentage.value = '';
                        discountPercentage.disabled = true;
                    } else if (discountPercentage.value) {
                        discountAmount.value = '';
                        discountAmount.disabled = true;
                    } else {
                        discountAmount.disabled = false;
                        discountPercentage.disabled = false;
                    }
                }

                // Listen for input changes
                discountAmount.addEventListener('input', updateDiscountFields);
                discountPercentage.addEventListener('input', updateDiscountFields);

                // Form validation before submit
                document.querySelector('form').addEventListener('submit', function(e) {
                    if (discountAmount.value && discountPercentage.value) {
                        e.preventDefault();
                        alert('Vui lòng chỉ chọn một loại giảm giá (số tiền hoặc phần trăm)');
                        return false;
                    }

                    if (!discountAmount.value && !discountPercentage.value) {
                        e.preventDefault();
                        alert('Vui lòng chọn một loại giảm giá');
                        return false;
                    }
                });

                // Initialize state
                updateDiscountFields();
            });
        </script>
    @endpush
</body>

</html>
