<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    public function vn_payment(Request $request, Order $order)
    {
        // $data = $request->all();
        // $code_cart = rand(00, 9999);
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return');
        $vnp_TmnCode = "ZVFYAJCW";
        $vnp_HashSecret = "RNP6Q0DRH55E08RUS5K22URJNKJZ16CF";

        $vnp_TxnRef = $order->order_id;
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->order_id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = ($order->orderDetails->sum(function ($detail) {
            return $detail->sold_price * $detail->sold_quantity;
        }) + $order->shippingMethod->shipping_fee - ($order->voucher ? $order->getDiscountAmount() : 0)) * 100; // VNPay yêu cầu đơn vị là VND * 100
        $vnp_Locale = 'vn';
        // $vnp_BankCode = '';
        $vnp_IpAddr = $request->ip();

        $startTime = date("YmdHis");
        // $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        $inputData = array_filter($inputData, function ($v) {
            return $v !== null && $v !== '';
        });

        ksort($inputData);
        $hashdata = '';
        $i = 0;
        $query = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= '?' . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        return redirect($vnp_Url);
    }
    public function vnpayReturn(Request $request)
    {
        $inputData = $request->all();

        // Hiển thị thông báo cho khách hàng
        if ($request->vnp_ResponseCode == '00') {
            $order = Order::find($request->vnp_TxnRef);
            if ($order) {
                $order->order_status = 'confirmed';
                $order->save();
            }
            return redirect()->route('customer.orders')->with('success', 'Thanh toán VNPay thành công!');
        }
    }
    public function momo_payment(Request $request, Order $order)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán đơn hàng #" . $order->order_id . " qua ATM MoMo";
        $amount = $order->orderDetails->sum(function ($detail) {
            return $detail->sold_price * $detail->sold_quantity;
        }) + $order->shippingMethod->shipping_fee - ($order->voucher ? $order->getDiscountAmount() : 0);

        $orderId = time() . "";
        $redirectUrl = route('momo.return');
        $ipnUrl = 'http://127.0.0.1:8000/homepage';
        $extraData = "";

        $requestId = time() . "";
        $requestType = "payWithATM";

        // Tạo chuỗi rawHash
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        // Gửi request POST đến MoMo
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close($ch);

        $jsonResult = json_decode($result, true);

        // dd($jsonResult);

        // Chuyển hướng sang trang thanh toán MoMo
        if (isset($jsonResult['payUrl'])) {
            return redirect()->to($jsonResult['payUrl']);
        } else {
            return back()->with('error', 'Không thể kết nối tới cổng thanh toán MoMo!');
        }
    }

    public function momoReturn(Request $request)
    {
        // Xử lý kết quả trả về trên trình duyệt (redirectUrl)
        // Ví dụ: kiểm tra $request->resultCode == 0 là thành công
        if ($request->resultCode == 0) {
            // Cập nhật trạng thái đơn hàng tại đây
            return redirect()->route('customer.orders')->with('success', 'Thanh toán MoMo thành công!');
        } else {
            return redirect()->route('customer.orders')->with('error', 'Thanh toán MoMo thất bại!');
        }
    }

    public function showBankPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        $qrCodeUrl = $order->qr_code_url ?? asset('images/qr-tpbank.png');
        $logoUrl = asset('images/logo.png');
        return view('Customer.payment.bank_payment', compact('order', 'qrCodeUrl', 'logoUrl'));
    }

    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');
            Log::info('Payment search:', ['query' => $query]);

            $payments = PaymentMethod::where('method_name', 'LIKE', "%{$query}%")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Payment search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm'
            ], 500);
        }
    }

    public function index()
    {
        $payments = PaymentMethod::paginate(10);
        return view('management.payment_method_mana.index', compact('payments'));
    }

    public function create()
    {
        return view('management.payment_method_mana.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_name' => 'required|string|max:50|unique:payment_methods'
        ]);

        try {
            PaymentMethod::create($request->only('method_name'));

            return redirect()
                ->route('admin.payment')
                ->with('success', 'Thêm phương thức thanh toán thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm phương thức thanh toán');
        }
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('management.payment_method_mana.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'method_name' => 'required|string|max:50|unique:payment_methods,method_name,' . $paymentMethod->method_id . ',method_id'
        ]);

        try {
            $paymentMethod->update($request->only('method_name'));
            return redirect()->route('admin.payment')->with('success', 'Cập nhật phương thức thanh toán thành công');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật phương thức thanh toán');
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();
            return redirect()->route('admin.payment')->with('success', 'Xóa phương thức thanh toán thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa phương thức thanh toán');
        }
    }
}
