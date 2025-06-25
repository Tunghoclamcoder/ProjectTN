<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
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
                        <h4>Chỉnh sửa thông tin Sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.product.update', $product->product_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="product_name" class="form-label">Tên sản phẩm <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                    id="product_name" name="product_name"
                                    value="{{ old('product_name', $product->product_name) }}" required>
                                @error('product_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phần discount --}}
                            <div class="mb-3">
                                <label for="discount" class="form-label">Giảm giá (%)</label>
                                <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                    id="discount" name="discount" value="{{ old('discount', $product->discount) }}"
                                    min="0" max="100" step="0.01">
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($product->discount > 0)
                                    <small class="text-success mt-1 d-block">
                                        Giá sau khi giảm: {{ number_format($product->getDiscountedPrice()) }} VNĐ
                                    </small>
                                @endif
                            </div>

                            {{-- Phần quantity --}}
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Số lượng <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" name="quantity" value="{{ old('quantity', $product->quantity) }}"
                                    required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="brand_id" class="form-label">Thương hiệu <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('brand_id') is-invalid @enderror" id="brand_id"
                                    name="brand_id" required>
                                    <option value="">Chọn thương hiệu</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->brand_id }}"
                                            {{ old('brand_id', $product->brand_id) == $brand->brand_id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="material_id" class="form-label">Chất liệu <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('material_id') is-invalid @enderror" id="material_id"
                                    name="material_id" required>
                                    <option value="">Chọn chất liệu</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->material_id }}"
                                            {{ old('material_id', $product->material_id) == $material->material_id ? 'selected' : '' }}>
                                            {{ $material->material_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('material_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sizeSelect" class="form-label">Kích thước <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('size_ids') is-invalid @enderror"
                                    id="sizeSelect" name="size_ids[]" multiple required>
                                    @foreach ($sizes as $size)
                                        <option value="{{ $size->size_id }}"
                                            {{ in_array($size->size_id, old('size_ids', $product->sizes->pluck('size_id')->toArray())) ? 'selected' : '' }}>
                                            {{ $size->size_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('size_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Categories Selection --}}
                            <div class="mb-3">
                                <label for="categorySelect" class="form-label">Danh mục <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2 @error('category_ids') is-invalid @enderror"
                                    id="categorySelect" name="category_ids[]" multiple required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category_id }}"
                                            {{ in_array($category->category_id, old('category_ids', $product->categories->pluck('category_id')->toArray())) ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Ảnh chính<span class="text-danger">*</span></label>
                                        <select name="main_image_id" id="mainImageSelect"
                                            class="form-control @error('main_image_id') is-invalid @enderror" required>
                                            <option value="">Chọn ảnh chính</option>
                                            @foreach ($images as $image)
                                                <option value="{{ $image->image_id }}"
                                                    data-url="{{ asset($image->image_url) }}"
                                                    {{ old('main_image_id', $product->getMainImage()?->image_id) == $image->image_id ? 'selected' : '' }}>
                                                    {{ $image->image_url }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('main_image_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="main-image-preview mt-2">
                                            @php $mainImage = $product->getMainImage(); @endphp
                                            @if ($mainImage)
                                                <img id="mainImagePreview" src="{{ asset($mainImage->image_url) }}"
                                                    alt="Ảnh chính"
                                                    style="max-width: 150px; max-height: 150px; border: 2px solid #28a745;">
                                                <div id="mainImagePlaceholder" class="text-muted"
                                                    style="display: none;">
                                                    <small>Chưa chọn ảnh chính</small>
                                                </div>
                                            @else
                                                <img id="mainImagePreview" src="" alt="Ảnh chính"
                                                    style="max-width: 150px; max-height: 150px; display: none; border: 2px solid #28a745;">
                                                <div id="mainImagePlaceholder" class="text-muted">
                                                    <small>Chưa chọn ảnh chính</small>
                                                </div>
                                            @endif
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
                                                    data-url="{{ asset($image->image_url) }}"
                                                    {{ in_array($image->image_id, old('sub_image_ids', $product->getSubImages()->pluck('image_id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $image->image_url }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sub_image_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="sub-images-preview mt-2 d-flex gap-2 flex-wrap">
                                            @if ($product->getSubImages()->count() > 0)
                                                @foreach ($product->getSubImages() as $subImage)
                                                    <img src="{{ asset($subImage->image_url) }}" alt="Ảnh phụ"
                                                        class="me-2 mb-2"
                                                        style="max-width: 100px; max-height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                                @endforeach
                                            @else
                                                <div id="subImagesPlaceholder" class="text-muted">
                                                    <small>Chưa chọn ảnh phụ</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1"
                                        {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Đang bán</option>
                                    <option value="0"
                                        {{ old('status', $product->status) == 0 ? 'selected' : '' }}>Ngừng kinh doanh
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.product') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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

        function formatPrice(input) {
            let cursorPosition = input.selectionStart;
            let value = input.value;
            let numericValue = value.replace(/[^\d]/g, '');

            if (numericValue.length >= 4) {
                numericValue = Math.max(parseInt(numericValue), 1000).toString();
            }

            // Định dạng số với dấu phẩy
            const formatted = new Intl.NumberFormat('vi-VN').format(numericValue);

            // Tạo hoặc cập nhật div hiển thị giá đã format
            let formattedDisplay = input.parentElement.querySelector('.formatted-price');
            if (!formattedDisplay) {
                formattedDisplay = document.createElement('div');
                formattedDisplay.className = 'formatted-price text-muted mt-1';
                input.parentElement.appendChild(formattedDisplay);
            }

            formattedDisplay.textContent = numericValue ? formatted + ' VNĐ' : '';

            // Giữ nguyên giá trị số trong input
            input.value = numericValue;
            input.setSelectionRange(cursorPosition, cursorPosition);
        }

        // Update price display when input changes
        document.addEventListener('DOMContentLoaded', function() {
            const discountInput = document.querySelector('input[name="discount"]');
            console.log('Discount input value:', discountInput?.value);
            console.log('Discount input element:', discountInput);
        });

        $(document).ready(function() {


            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Chọn...',
                allowClear: true
            });

            // Khởi tạo riêng cho từng select nếu cần custom
            $('#categorySelect').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Chọn danh mục',
                allowClear: true
            });

            $('#sizeSelect').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Chọn kích thước',
                allowClear: true
            });

            // Main image preview
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

            // Sub images preview
            $('#subImagesSelect').on('change', function() {
                const selectedOptions = $(this).find('option:selected');
                const previewContainer = $('.sub-images-preview');
                const placeholder = $('#subImagesPlaceholder');

                previewContainer.find('img').remove();
                previewContainer.find('#subImagesPlaceholder').remove();

                if (selectedOptions.length > 0) {
                    selectedOptions.each(function() {
                        const imageUrl = $(this).data('url');
                        previewContainer.append(`
                    <img src="${imageUrl}"
                        alt="Ảnh phụ"
                        class="me-2 mb-2"
                        style="max-width: 100px; max-height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                `);
                    });
                } else {
                    previewContainer.append(`
                <div id="subImagesPlaceholder" class="text-muted">
                    <small>Chưa chọn ảnh phụ</small>
                </div>
            `);
                }
            });
        });
    </script>
</body>
<style>
    .sub-image-container {
        position: relative;
        display: inline-block;
    }

    .remove-sub-image {
        position: absolute;
        top: -10px;
        right: -10px;
        padding: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .img-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: contain;
    }
</style>

</html>
