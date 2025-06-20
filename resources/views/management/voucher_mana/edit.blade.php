<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="{{ asset('js/alert.js') }}"></script>

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
        {{-- @php
            dd([
                'discount_value' => $product->discount,
                'old_discount' => old('discount'),
                'product' => $product->toArray(),
            ]);
        @endphp --}}
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Chỉnh sửa thông tin Voucher</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.voucher.update', $voucher->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="code" class="form-label">Mã Voucher <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code" name="code" value="{{ old('code', $voucher->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_amount" class="form-label">Số tiền giảm</label>
                                        <input type="number"
                                            class="form-control @error('discount_amount') is-invalid @enderror"
                                            id="discount_amount" name="discount_amount"
                                            value="{{ old('discount_amount', $voucher->discount_amount) }}"
                                            min="0" step="1000">
                                        @error('discount_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_percentage" class="form-label">Phần trăm giảm (%)</label>
                                        <input type="number"
                                            class="form-control @error('discount_percentage') is-invalid @enderror"
                                            id="discount_percentage" name="discount_percentage"
                                            value="{{ old('discount_percentage', $voucher->discount_percentage) }}"
                                            min="0" max="100" step="0.1">
                                        @error('discount_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Ngày bắt đầu <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date"
                                            value="{{ old('start_date', $voucher->start_date->format('Y-m-d')) }}"
                                            required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="expiry_date" class="form-label">Ngày hết hạn <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('expiry_date') is-invalid @enderror"
                                            id="expiry_date" name="expiry_date"
                                            value="{{ old('expiry_date', $voucher->expiry_date->format('Y-m-d')) }}"
                                            required>
                                        @error('expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="minimum_purchase_amount" class="form-label">Số tiền đơn hàng tối
                                            thiểu</label>
                                        <input type="number"
                                            class="form-control @error('minimum_purchase_amount') is-invalid @enderror"
                                            id="minimum_purchase_amount" name="minimum_purchase_amount"
                                            value="{{ old('minimum_purchase_amount', $voucher->minimum_purchase_amount) }}"
                                            min="0" step="1000">
                                        @error('minimum_purchase_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="maximum_purchase_amount" class="form-label">Số tiền giảm tối
                                            đa</label>
                                        <input type="number"
                                            class="form-control @error('maximum_purchase_amount') is-invalid @enderror"
                                            id="maximum_purchase_amount" name="maximum_purchase_amount"
                                            value="{{ old('maximum_purchase_amount', $voucher->maximum_purchase_amount) }}"
                                            min="0" step="1000">
                                        @error('maximum_purchase_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="max_usage_count" class="form-label">Số lần sử dụng tối đa</label>
                                <input type="number"
                                    class="form-control @error('max_usage_count') is-invalid @enderror"
                                    id="max_usage_count" name="max_usage_count"
                                    value="{{ old('max_usage_count', $voucher->max_usage_count) }}" min="1">
                                <small class="text-muted">Để trống nếu không giới hạn số lần sử dụng</small>
                                @error('max_usage_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    <option value="1"
                                        {{ old('status', $voucher->status) == 1 ? 'selected' : '' }}>
                                        Đang kích hoạt
                                    </option>
                                    <option value="0"
                                        {{ old('status', $voucher->status) == 0 ? 'selected' : '' }}>
                                        Đã hết hạn
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.voucher') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">Cập nhật Voucher</button>
                            </div>
                        </form>
                    </div>

                    @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Xử lý discount amount và percentage
                                const discountAmount = document.getElementById('discount_amount');
                                const discountPercentage = document.getElementById('discount_percentage');

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

                                discountAmount.addEventListener('input', updateDiscountFields);
                                discountPercentage.addEventListener('input', updateDiscountFields);

                                // Kiểm tra ngày
                                const startDate = document.getElementById('start_date');
                                const expiryDate = document.getElementById('expiry_date');

                                startDate.addEventListener('change', function() {
                                    expiryDate.min = this.value;
                                });

                                // Format số tiền
                                function formatCurrency(input) {
                                    input.addEventListener('input', function() {
                                        let value = this.value.replace(/\D/g, '');
                                        if (value === '') return;
                                        value = parseInt(value);
                                        this.value = value.toLocaleString('vi-VN');
                                    });
                                }

                                const currencyInputs = [
                                    document.getElementById('discount_amount'),
                                    document.getElementById('minimum_purchase_amount'),
                                    document.getElementById('maximum_purchase_amount')
                                ];

                                currencyInputs.forEach(input => formatCurrency(input));

                                // Khởi tạo trạng thái ban đầu
                                updateDiscountFields();
                            });
                        </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Xử lý discount amount và percentage
                const discountAmount = document.getElementById('discount_amount');
                const discountPercentage = document.getElementById('discount_percentage');

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

                discountAmount.addEventListener('input', updateDiscountFields);
                discountPercentage.addEventListener('input', updateDiscountFields);

                // Kiểm tra ngày
                const startDate = document.getElementById('start_date');
                const expiryDate = document.getElementById('expiry_date');

                startDate.addEventListener('change', function() {
                    expiryDate.min = this.value;
                });

                // Format số tiền
                function formatCurrency(input) {
                    input.addEventListener('input', function() {
                        let value = this.value.replace(/\D/g, '');
                        if (value === '') return;
                        value = parseInt(value);
                        this.value = value.toLocaleString('vi-VN');
                    });
                }

                const currencyInputs = [
                    document.getElementById('discount_amount'),
                    document.getElementById('minimum_purchase_amount'),
                    document.getElementById('maximum_purchase_amount')
                ];

                currencyInputs.forEach(input => formatCurrency(input));

                // Khởi tạo trạng thái ban đầu
                updateDiscountFields();
            });
        </script>
    @endpush
</body>

</html>
