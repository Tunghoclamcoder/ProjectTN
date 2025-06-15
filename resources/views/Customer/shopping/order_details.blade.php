<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/order_details.css') }}">
</head>

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
                @endswitch
            </span>
        </div>
    </div>

    <div class="order-info-grid">
        <div class="order-summary">
            <h3>Thông tin đơn hàng</h3>
            <div class="summary-item">
                <span class="label">Ngày đặt hàng:</span>
                <span class="value">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y - H:i') }}</span>
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
                <span class="value">{{ $order->shippingMethod->method_name }}</span>
            </div>
        </div>

        <div class="order-timeline">
            <h3>Trạng thái đơn hàng</h3>
            <div class="timeline">
                <div class="timeline-item {{ $order->order_status == 'pending' ? 'active' : 'completed' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Đặt hàng</h4>
                        <p>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div
                    class="timeline-item {{ in_array($order->order_status, ['confirmed', 'shipping', 'completed']) ? 'active' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Xác nhận</h4>
                        <p>{{ $order->order_status == 'confirmed' ? 'Đang xử lý' : ($order->order_status != 'pending' ? 'Đã xác nhận' : 'Chờ xác nhận') }}
                        </p>
                    </div>
                </div>
                <div
                    class="timeline-item {{ in_array($order->order_status, ['shipping', 'completed']) ? 'active' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Vận chuyển</h4>
                        <p>{{ $order->order_status == 'shipping' ? 'Đang giao hàng' : ($order->order_status == 'completed' ? 'Đã giao hàng' : 'Chưa giao hàng') }}
                        </p>
                    </div>
                </div>
                <div class="timeline-item {{ $order->order_status == 'completed' ? 'active' : '' }}">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h4>Hoàn thành</h4>
                        <p>{{ $order->order_status == 'completed' ? 'Đã giao hàng thành công' : 'Chưa hoàn thành' }}
                        </p>
                    </div>
                </div>
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
                <span
                    class="value">{{ number_format(
                        $order->orderDetails->sum(function ($detail) {
                            return $detail->sold_price * $detail->sold_quantity;
                        }),
                    ) }}
                    VNĐ</span>
            </div>
            @if ($order->voucher)
                <div class="total-item discount">
                    <span class="label">Giảm giá ({{ $order->voucher->code }}):</span>
                    <span class="value">-{{ number_format($order->getDiscountAmount()) }} VNĐ</span>
                </div>
            @endif
            <div class="total-item grand-total">
                <span class="label">Tổng cộng:</span>
                <span class="value">{{ number_format($order->getFinalTotal()) }} VNĐ</span>
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
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Write Order Review</h3>
            <span class="close" onclick="closeReviewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="reviewForm" action="{{-- {{ route('order.review') }} --}} #" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

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
                    <label for="review_text">Review:</label>
                    <textarea name="review_text" id="review_text" rows="4" placeholder="Share your experience with this order..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeReviewModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/order_details.js') }}"></script>

</html>
