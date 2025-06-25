<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán QR Ngân hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 40px 32px 32px 32px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .qr-logo {
            width: 160px;
            height: 80px;
            margin-bottom: 18px;
        }
        .qr-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #222;
        }
        .qr-amount {
            font-size: 1.1rem;
            color: #04a03b;
            margin-bottom: 18px;
        }
        .qr-img {
            width: 220px;
            height: 220px;
            object-fit: contain;
            margin: 0 auto 18px auto;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .qr-note {
            font-size: 0.98rem;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <img src="{{ $logoUrl }}" alt="Logo Website" class="qr-logo">
        <div class="qr-title">Quét mã QR để thanh toán qua ngân hàng</div>
        <div class="qr-amount">
            Số tiền: <b>{{ number_format($order->orderDetails->sum(fn($d) => $d->sold_price * $d->sold_quantity) + $order->shippingMethod->shipping_fee - ($order->voucher ? $order->getDiscountAmount() : 0)) }} VNĐ</b>
        </div>
        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="qr-img">
        <div class="qr-note">
            Vui lòng sử dụng ứng dụng ngân hàng hoặc ví điện tử để quét mã.<br>
            <span style="color:#04a03b;">Thanh toán sẽ được xác nhận tự động sau khi chuyển khoản thành công.</span>
        </div>
        <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary mt-4">Quay lại đơn hàng</a>
    </div>
</body>
</html>
