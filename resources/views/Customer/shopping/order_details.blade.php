<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/order_details.css') }}">
    <script src="{{ asset('js/alert.js') }}"></script>

</head>
<div class="alerts-container" style="display: flex; justify-content: center;">
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

<div class="order-details-container">
    <div class="order-header">
        <div class="order-title">
            <h2>Chi tiết đơn hàng</h2>
            <div class="order-id">Mã đơn hàng: #{{ $order->order_id }}</div>
        </div>
        <div class="order-status">
            <span class="status-badge status-{{ strtolower($order->order_status) }}">
                @switch($order->order_status)
                    @case('pending')
                        Chờ xác nhận
                    @break

                    @case('confirmed')
                        Đã xác nhận
                    @break

                    @case('shipping')
                        Đang giao hàng
                    @break

                    @case('completed')
                        Đã giao hàng
                    @break

                    @case('cancelled')
                        Đã hủy
                    @break

                    @case('returned')
                        Đã hoàn trả
                    @break
                @endswitch
            </span>
        </div>
    </div>

    <div class="order-info-grid">
        <div class="order-summary">
            <h3>Thông tin đơn hàng</h3>
            <div class="summary-item">
                <span class="label">Ngày đặt hàng:</span>
                <span class="value">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Người nhận:</span>
                <span class="value">{{ $order->receiver_name }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Số điện thoại:</span>
                <span class="value">{{ $order->receiver_phone }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Địa chỉ:</span>
                <span class="value">{{ $order->receiver_address }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Phương thức thanh toán:</span>
                <span class="value">{{ $order->paymentMethod->method_name }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Phương thức vận chuyển:</span>
                <span class="value">
                    {{ $order->shippingMethod ? $order->shippingMethod->method_name : 'Không có thông tin' }}
                </span>
            </div>
        </div>

        <div class="order-timeline">
            <h3>Trạng thái đơn hàng</h3>
            <div class="timeline">
                <div
                    class="timeline-item {{ $order->order_status == 'pending' ? 'active' : ($order->order_status == 'cancelled' ? '' : 'completed') }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Đặt hàng</h4>
                        <p>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div
                    class="timeline-item {{ in_array($order->order_status, ['confirmed', 'shipping', 'completed', 'cancelled', 'returned']) ? 'active' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Xác nhận</h4>
                        <p>
                            @if ($order->order_status == 'confirmed')
                                Đang xử lý
                            @elseif(in_array($order->order_status, ['shipping', 'completed', 'returned']))
                                Đã xác nhận
                            @elseif($order->order_status == 'cancelled')
                                Đã xác nhận trước khi hủy
                            @else
                                Chờ xác nhận
                            @endif
                        </p>
                    </div>
                </div>
                <div
                    class="timeline-item {{ in_array($order->order_status, ['shipping', 'completed', 'returned']) ? 'active' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Vận chuyển</h4>
                        <p>
                            @if ($order->order_status == 'shipping')
                                Đang giao hàng
                            @elseif(in_array($order->order_status, ['completed', 'returned']))
                                Đã giao hàng
                            @elseif($order->order_status == 'cancelled')
                                Không giao hàng
                            @else
                                Chưa giao hàng
                            @endif
                        </p>
                    </div>
                </div>
                <div
                    class="timeline-item {{ in_array($order->order_status, ['completed', 'returned']) ? 'active' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Hoàn thành</h4>
                        <p>
                            @if ($order->order_status == 'completed')
                                Đã giao hàng thành công
                            @elseif($order->order_status == 'returned')
                                Đã giao hàng trước khi hoàn trả
                            @else
                                Chưa hoàn thành
                            @endif
                        </p>
                    </div>
                </div>
                {{-- Đơn hàng đã bị hủy --}}
                @if ($order->order_status == 'cancelled')
                    <div class="timeline-item active cancelled">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>Đơn hàng đã bị hủy</h4>
                            <p>Đơn hàng đã bị hủy bởi khách hàng hoặc hệ thống.</p>
                        </div>
                    </div>
                @endif
                {{-- Đơn hàng đã được hoàn trả --}}
                @if ($order->order_status == 'returned')
                    <div class="timeline-item active returned">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>Đơn hàng đã được hoàn trả</h4>
                            <p>Đơn hàng đã được hoàn trả thành công.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="order-products">
        <h3>Sản phẩm đã đặt</h3>
        <div class="products-list">
            @foreach ($order->orderDetails as $detail)
                <div class="product-item">
                    <div class="product-image">
                        @if ($detail->product->getMainImage())
                            <img src="{{ Storage::url($detail->product->getMainImage()->image_url) }}"
                                alt="{{ $detail->product->product_name }}" class="img-fluid rounded">
                        @else
                            <div class="no-image">Không có ảnh</div>
                        @endif
                    </div>

                    <div class="product-info">
                        <h4>{{ $detail->product->product_name }}</h4>
                        <p class="product-description">{{ $detail->product->description }}</p>
                        <div class="product-details">
                            <span class="category">
                                Danh mục:
                                {{ $detail->product->getPrimaryCategory() ? $detail->product->getPrimaryCategory()->category_name : 'N/A' }}
                            </span>
                            <br>
                            <span class="brand">
                                Thương hiệu:
                                {{ $detail->product->brand ? $detail->product->brand->brand_name : 'N/A' }}
                            </span>
                            <br>
                            <span class="size">
                                Kích thước:
                                {{ $detail->product->getPrimarySize() ? $detail->product->getPrimarySize()->size_name : 'N/A' }}
                            </span>
                            <br>
                            <span class="quantity">Số lượng: {{ $detail->sold_quantity }}</span>
                        </div>

                    </div>
                    <div class="product-pricing">
                        <div class="unit-price">{{ number_format($detail->sold_price) }} VNĐ</div>
                        <div class="total-price">{{ number_format($detail->sold_price * $detail->sold_quantity) }} VNĐ
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="order-totals">
        <div class="totals-section">
            <div class="total-item">
                <span class="label">Tạm tính:</span>
                <span class="value">
                    {{ number_format(
                        $order->orderDetails->sum(function ($detail) {
                            return $detail->sold_price * $detail->sold_quantity;
                        }),
                    ) }}
                    VNĐ
                </span>
            </div>

            <!-- Add shipping fee display -->
            <div class="total-item">
                <span class="label">Phí vận chuyển ({{ $order->shippingMethod->method_name }}):</span>
                <span class="value">{{ number_format($order->shippingMethod->shipping_fee) }} VNĐ</span>
            </div>

            <!-- Show discount if voucher exists -->
            @if ($order->voucher)
                <div class="total-item discount">
                    <span class="label">Giảm giá ({{ $order->voucher->code }}):</span>
                    <span class="value">-{{ number_format($order->getDiscountAmount()) }} VNĐ</span>
                </div>
            @endif

            <!-- Update grand total to include shipping fee -->
            <div class="total-item grand-total">
                <span class="label">Tổng cộng:</span>
                <span class="value">
                    {{ number_format(
                        $order->orderDetails->sum(function ($detail) {
                            return $detail->sold_price * $detail->sold_quantity;
                        }) +
                            $order->shippingMethod->shipping_fee -
                            ($order->voucher ? $order->getDiscountAmount() : 0),
                    ) }}
                    VNĐ
                </span>
            </div>
        </div>
    </div>

    <div class="order-actions">
        <a href="{{ route('customer.orders') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>

        @if ($order->order_status == 'pending')
            <form action="{{ route('customer.orders.cancel', $order->order_id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                    <i class="bi bi-x-circle"></i> Hủy đơn hàng
                </button>
            </form>
        @endif

        @if ($order->order_status == 'completed' && !$order->isReturned())
            <form action="{{ route('customer.orders.return', $order->order_id) }}" method="POST" class="d-inline">
                @csrf
                @method('POST')
                <button type="submit" class="btn btn-warning btn-return-order"
                    onclick="return confirm('Bạn có chắc chắn muốn hoàn trả đơn hàng này?')">
                    <i class="bi bi-arrow-counterclockwise"></i> Hoàn trả đơn hàng
                </button>
            </form>
            <button type="button" class="btn btn-success" onclick="openReviewModal()">
                <i class="bi bi-star"></i> Đánh giá đơn hàng
            </button>
        @endif

        @if ($order->payment_method_id == 7 && $order->order_status == 'confirmed')
            <form action="{{ route('vnpay.payment', $order->order_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-credit-card"></i> Thanh toán <span style="color: #020046;">VNPay</span>
                </button>
            </form>
        @endif

        @if ($order->payment_method_id == 5 && $order->order_status == 'confirmed')
            <form action="{{ route('momo.payment', $order->order_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-wallet2"></i> Thanh toán <span style="color: #940059;">Momo</span>
                </button>
            </form>
        @endif

        @if ($order->payment_method_id == 4 && $order->order_status == 'confirmed')
            <a href="{{ route('bank-qr.payment', $order->order_id) }}" class="btn btn-primary">
                <i class="bi bi-wallet2"></i> Thanh toán qua <span style="color: #dfdfdf;">Bank</span>
            </a>
        @endif
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Viết đánh giá đơn hàng</h3>
            <span class="close" onclick="closeReviewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="reviewForm" action="{{ route('order.review', $order->order_id) }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <div class="star-rating">
                        <input type="radio" name="rating" value="5" id="star5">
                        <label for="star5">★</label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4">★</label>
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3">★</label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2">★</label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1">★</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="review_text">Viết đánh giá:</label>
                    <textarea name="review_text" id="review_text" rows="4"
                        placeholder="Chia sẻ trải nghiệm của bạn với đơn hàng này..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeReviewModal()">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/order_details.js') }}"></script>

</html>
