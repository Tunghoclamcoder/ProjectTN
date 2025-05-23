<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản phẩm mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
            <div class="col-md-13">
                <div class="card">
                    <div class="card-header">
                        <h4>Thêm Sản phẩm mới</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Tên sản phẩm<span class="text-danger">*</span></label>
                                        <input type="text" name="product_name"
                                            class="form-control @error('product_name') is-invalid @enderror"
                                            value="{{ old('product_name') }}" required>
                                        @error('product_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Giá (VNĐ)<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" name="price"
                                                class="form-control @error('price') is-invalid @enderror"
                                                value="{{ old('price') }}" required pattern="[0-9]*"
                                                inputmode="numeric" placeholder="Nhập giá bán (VNĐ)"
                                                oninput="formatPrice(this)">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                        <div class="formatted-price text-muted mt-1"></div>
                                        <small class="text-muted">(Giá ít nhất phải từ 1,000 VNĐ)</small>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Giảm giá (%)</label>
                                        <input type="number" name="discount"
                                            class="form-control @error('discount') is-invalid @enderror"
                                            value="{{ old('discount', 0) }}" min="0" max="100">
                                        @error('discount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Số lượng<span class="text-danger">*</span></label>
                                        <input type="number" name="quantity"
                                            class="form-control @error('quantity') is-invalid @enderror"
                                            value="{{ old('quantity', 0) }}" required min="0">
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Thương hiệu<span class="text-danger">*</span></label>
                                        <select name="brand_id"
                                            class="form-control @error('brand_id') is-invalid @enderror" required>
                                            <option value="">Chọn thương hiệu</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->brand_id }}"
                                                    {{ old('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                                                    {{ $brand->brand_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Size sản phẩm<span class="text-danger">*</span></label>
                                        <select name="size_ids[]"
                                            class="form-control select2 @error('size_ids') is-invalid @enderror"
                                            multiple required>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->size_id }}"
                                                    {{ in_array($size->size_id, old('size_ids', [])) ? 'selected' : '' }}>
                                                    {{ $size->size_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('size_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Chất liệu<span class="text-danger">*</span></label>
                                        <select name="material_id"
                                            class="form-control @error('material_id') is-invalid @enderror" required>
                                            <option value="">Chọn chất liệu</option>
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->material_id }}"
                                                    {{ old('material_id') == $material->material_id ? 'selected' : '' }}>
                                                    {{ $material->material_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('material_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Danh mục<span class="text-danger">*</span></label>
                                        <select name="category_ids[]"
                                            class="form-control select2 @error('category_ids') is-invalid @enderror"
                                            multiple required>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->category_id }}"
                                                    {{ in_array($category->category_id, old('category_ids', [])) ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Trạng thái</label>
                                        <select name="status"
                                            class="form-control @error('status') is-invalid @enderror">
                                            <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>
                                                Đang bán</option>
                                            <option value="0" {{ old('status', '1') === '0' ? 'selected' : '' }}>
                                                Ngừng kinh doanh</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Ảnh chính<span class="text-danger">*</span></label>
                                        <select name="main_image_id" id="mainImageSelect"
                                            class="form-control @error('main_image_id') is-invalid @enderror"
                                            required>
                                            <option value="">Chọn ảnh chính</option>
                                            @foreach ($images as $image)
                                                <option value="{{ $image->image_id }}"
                                                    data-url="{{ Storage::url($image->image_url) }}"
                                                    {{ old('main_image_id') == $image->image_id ? 'selected' : '' }}>
                                                    {{ $image->image_url }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('main_image_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="main-image-preview mt-2">
                                            <img id="mainImagePreview" src="" alt="Ảnh chính"
                                                style="max-width: 150px; max-height: 150px; display: none; border: 2px solid #28a745;">
                                            <div id="mainImagePlaceholder" class="text-muted">
                                                <small>Chưa chọn ảnh chính</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Ảnh phụ (tối đa 3 ảnh)</label>
                                        <select name="sub_image_ids[]" id="subImagesSelect"
                                            class="form-control select2 @error('sub_image_ids') is-invalid @enderror"
                                            multiple>
                                            @foreach ($images as $image)
                                                <option value="{{ $image->image_id }}"
                                                    data-url="{{ Storage::url($image->image_url) }}"
                                                    {{ in_array($image->image_id, old('sub_image_ids', [])) ? 'selected' : '' }}>
                                                    {{ $image->image_url }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sub_image_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="sub-images-preview mt-2 d-flex gap-2 flex-wrap">
                                            <div id="subImagesPlaceholder" class="text-muted">
                                                <small>Chưa chọn ảnh phụ</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.product') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tạo sản phẩm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function formatPrice(input) {
            // Cho phép nhập số và giữ con trỏ ở đúng vị trí
            let cursorPosition = input.selectionStart;
            let value = input.value;

            // Chỉ lấy số từ input
            let numericValue = value.replace(/[^\d]/g, '');

            // Đảm bảo giá trị tối thiểu là 1000 nếu đã nhập đủ 4 số
            if (numericValue.length >= 4) {
                numericValue = Math.max(parseInt(numericValue), 1000).toString();
            }

            // Định dạng số với dấu phẩy
            const formatted = new Intl.NumberFormat('vi-VN').format(numericValue);

            // Hiển thị giá đã format
            const formattedDisplay = input.parentElement.nextElementSibling;
            if (numericValue) {
                formattedDisplay.textContent = formatted + ' VNĐ';
            } else {
                formattedDisplay.textContent = '';
            }

            // Giữ nguyên giá trị số trong input
            input.value = numericValue;

            // Đặt lại vị trí con trỏ
            input.setSelectionRange(cursorPosition, cursorPosition);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.querySelector('input[name="price"]');
            if (priceInput.value) {
                formatPrice(priceInput);
            }
        });

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

        // Update price display when input changes
        document.querySelector('input[name="price"]').addEventListener('input', function(e) {
            let value = this.value;
            if (value >= 1000) {
                document.querySelector('.price-display').textContent = formatNumber(value) + ' VNĐ';
            }
        });

        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Chọn...',
                allowClear: true
            });

            $('#mainImageSelect').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const imageUrl = selectedOption.data('url');
                const preview = $('#mainImagePreview');
                const placeholder = $('#mainImagePlaceholder');

                if (imageUrl) {
                    preview.attr('src', imageUrl).show();
                    placeholder.hide();
                } else {
                    preview.hide();
                    placeholder.show();
                }
            });

            // Xử lý ảnh phụ
            $('#subImagesSelect').on('change', function() {
                const selectedOptions = $(this).find('option:selected');
                const previewContainer = $('.sub-images-preview');
                const placeholder = $('#subImagesPlaceholder');

                previewContainer.find('img').remove();

                if (selectedOptions.length > 0) {
                    placeholder.hide();
                    selectedOptions.each(function() {
                        const imageUrl = $(this).data('url');
                        previewContainer.append(`
                    <img src="${imageUrl}" alt="Ảnh phụ" class="me-2 mb-2">
                `);
                    });
                } else {
                    placeholder.show();
                }
            });

            // Trigger change events to show initial selected images
            $('#mainImageSelect').trigger('change');
            $('#subImagesSelect').trigger('change');
        });
    </script>
</body>
<style>
    .main-image-preview,
    .sub-images-preview {
        min-height: 100px;
        border: 1px dashed #ddd;
        border-radius: 4px;
        padding: 10px;
        background-color: #f8f9fa;
    }

    .sub-images-preview img {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
    }

    ::after
</style>

</html>
