<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Voucher;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'voucher', 'orderDetails.product'])
            ->orderBy('order_date', 'desc')
            ->paginate(10);

        return view('management.order_mana.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $employees = Employee::all();
        $vouchers = Voucher::all();
        $paymentMethods = PaymentMethod::all();
        $shippingMethods = ShippingMethod::all();

        return view('management.order_mana.create', compact(
            'customers',
            'employees',
            'vouchers',
            'paymentMethods',
            'shippingMethods'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'order_status' => 'required|string|max:50',
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string|max:255',
            'customer_id' => 'required|exists:customer,customer_id',
            'employee_id' => 'required|exists:employee,employee_id',
            'voucher_id' => 'nullable|exists:vouchers,voucher_id',
            'payment_method_id' => 'required|exists:payment_methods,method_id',
            'shipping_method_id' => 'required|exists:shipping_methods,method_id'
        ]);

        if ($request->voucher_id) {
            $voucher = Voucher::findOrFail($request->voucher_id);
            $voucher->increment('usage_count');
        }

        try {
            Order::create($request->all());

            return redirect()
                ->route('admin.order')
                ->with('success', 'Thêm đơn hàng thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm đơn hàng');
        }
    }

    public function edit(Order $order)
    {
        $order->load(['orderDetails.product', 'customer', 'voucher']);
        $customers = Customer::all();
        $paymentMethods = PaymentMethod::all();
        $shippingMethods = ShippingMethod::all();

        return view('management.order_mana.edit', compact(
            'order',
            'customers',
            'paymentMethods',
            'shippingMethods'
        ));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'order_status' => 'required|in:pending,confirmed,shipping,completed,cancelled',
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string|max:255',
            'customer_id' => 'required|exists:customer,customer_id',
            'payment_method_id' => 'required|exists:payment_methods,method_id',
            'shipping_method_id' => 'required|exists:shipping_methods,method_id',
        ]);

        // Keep the existing employee_id
        $validated['employee_id'] = $order->employee_id;

        try {
            $order->update($validated);
            return redirect()
                ->route('admin.order')
                ->with('success', 'Cập nhật đơn hàng thành công');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng')
                ->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load(['orderDetails.product', 'customer', 'voucher', 'paymentMethod', 'shippingMethod']);
        return view('management.order_mana.detail', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];

            // Validate request
            $request->validate([
                'order_status' => ['required', Rule::in($validStatuses)]
            ], [
                'order_status.required' => 'Trạng thái đơn hàng không được để trống',
                'order_status.in' => 'Trạng thái đơn hàng không hợp lệ'
            ]);

            // Kiểm tra logic chuyển trạng thái
            if ($order->order_status === 'completed' || $order->order_status === 'cancelled') {
                return back()->with('error', 'Không thể thay đổi trạng thái của đơn hàng đã hoàn thành hoặc đã hủy');
            }

            // Update status
            $order->update([
                'order_status' => $request->order_status
            ]);

            // Chuẩn bị thông báo phù hợp
            $message = match ($request->order_status) {
                'confirmed' => 'Đã xác nhận đơn hàng thành công',
                'shipping' => 'Đã chuyển trạng thái sang đang giao hàng',
                'completed' => 'Đã hoàn thành đơn hàng',
                'cancelled' => 'Đã hủy đơn hàng',
                default => 'Cập nhật trạng thái đơn hàng thành công'
            };

            return back()->with('success', $message);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            $order->delete();

            return redirect()
                ->route('admin.order')
                ->with('success', 'Xóa đơn hàng thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa đơn hàng');
        }
    }

    public function showCheckout()
    {
        try {
            $customer = Auth::guard('customer')->user();

            // Get active cart
            $cartOrder = Order::where([
                'customer_id' => $customer->customer_id,
                'order_status' => 'cart'
            ])->with(['orderDetails.product'])->first();

            if (!$cartOrder || $cartOrder->orderDetails->isEmpty()) {
                return redirect()->route('cart.view')
                    ->with('error', 'Giỏ hàng của bạn đang trống');
            }

            // Get cart items and calculate total
            $cartItems = $cartOrder->orderDetails;
            $total = $cartOrder->getTotalAmount();

            // Apply voucher discount if exists
            if (session()->has('voucher')) {
                $voucherData = session('voucher');
                if (isset($voucherData['discount_amount'])) {
                    $total = max(0, $total - $voucherData['discount_amount']);
                } elseif (isset($voucherData['discount_percentage'])) {
                    $discount = ($total * $voucherData['discount_percentage']) / 100;
                    $total = max(0, $total - $discount);
                }
            }

            // Get all payment and shipping methods
            $paymentMethods = PaymentMethod::all();
            $shippingMethods = ShippingMethod::all();

            // Lấy danh sách voucher_id đã được khách hàng sử dụng
            $usedVoucherIds = Order::where('customer_id', $customer->customer_id)
                ->whereNotNull('voucher_id')
                ->pluck('voucher_id')
                ->toArray();

            // Get active vouchers excluding used ones
            $activeVouchers = Voucher::where('status', true)
                ->where('start_date', '<=', now())
                ->where('expiry_date', '>=', now())
                ->where(function ($query) use ($total) {
                    $query->whereNull('minimum_purchase_amount')
                        ->orWhere('minimum_purchase_amount', '<=', $total);
                })
                ->where(function ($query) {
                    $query->whereNull('max_usage_count')
                        ->orWhereRaw('usage_count < max_usage_count');
                })
                ->whereNotIn('id', $usedVoucherIds) // Loại bỏ voucher đã sử dụng
                ->get();

            return view('Customer.shopping.checkout', compact(
                'cartItems',
                'total',
                'paymentMethods',
                'shippingMethods',
                'activeVouchers'
            ));
        } catch (\Exception $e) {
            return redirect()->route('cart.view')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'receiver_address' => 'required|string|max:255',
            'payment_method_id' => 'required|exists:payment_methods,method_id',
            'shipping_method_id' => 'required|exists:shipping_methods,method_id',
        ]);

        try {
            DB::beginTransaction();

            $customer = Auth::guard('customer')->user();

            // Get current cart
            $cartOrder = Order::where([
                'customer_id' => $customer->customer_id,
                'order_status' => 'cart'
            ])->with(['orderDetails.product'])->firstOrFail();

            // Check stock availability
            foreach ($cartOrder->orderDetails as $item) {
                if ($item->sold_quantity > $item->product->quantity) {
                    throw new \Exception("Sản phẩm {$item->product->product_name} chỉ còn {$item->product->quantity} trong kho");
                }
            }

            if (session()->has('voucher')) {
                $voucherData = session('voucher');
                $voucher = Voucher::find($voucherData['id']);

                // Kiểm tra voucher còn hiệu lực và còn lượt sử dụng
                if (
                    !$voucher || !$voucher->status ||
                    $voucher->expiry_date < now() ||
                    ($voucher->max_usage_count && $voucher->usage_count >= $voucher->max_usage_count)
                ) {
                    throw new \Exception('Mã giảm giá không còn hiệu lực hoặc đã hết lượt sử dụng');
                }

                // Tăng usage_count
                $voucher->increment('usage_count');
            }

            // Update cart to order
            $cartOrder->update([
                'order_status' => 'pending',
                'order_date' => now(),
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'payment_method_id' => $request->payment_method_id,
                'shipping_method_id' => $request->shipping_method_id,
                'voucher_id' => session('voucher.id') ?? null
            ]);

            // Update product quantities and record audit
            foreach ($cartOrder->orderDetails as $item) {
                $product = $item->product;
                $product->quantity -= $item->sold_quantity;
                $product->save();
            }

            // Clear voucher session
            session()->forget('voucher');

            DB::commit();

            return redirect()->route('shop.home')
                ->with('success', 'Đặt hàng thành công! Cảm ơn bạn đã mua hàng.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    public function applyVoucher(Request $request)
    {
        try {
            // Validate request
            if (empty($request->voucher_code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn mã giảm giá'
                ], 400);
            }

            $customer = Auth::guard('customer')->user();
            $voucher = Voucher::where('code', $request->voucher_code)->first();

            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không tồn tại'
                ], 404);
            }

            // Kiểm tra voucher có hợp lệ không
            if (!$voucher->status || $voucher->start_date > now() || $voucher->expiry_date < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá đã hết hạn hoặc không còn hiệu lực'
                ], 400);
            }

            // Kiểm tra số lần sử dụng
            if ($voucher->max_usage_count && $voucher->usage_count >= $voucher->max_usage_count) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá đã hết lượt sử dụng'
                ], 400);
            }

            // Lấy giỏ hàng hiện tại
            $cartOrder = Order::where([
                'customer_id' => $customer->customer_id,
                'order_status' => 'cart'
            ])->first();

            $total = $cartOrder->getTotalAmount();

            // Kiểm tra điều kiện áp dụng
            if ($voucher->minimum_purchase_amount && $total < $voucher->minimum_purchase_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để sử dụng mã giảm giá này'
                ], 400);
            }

            // Tính toán giá trị sau khi áp dụng voucher
            $discountAmount = $voucher->discount_amount ?? ($total * $voucher->discount_percentage / 100);
            $newTotal = max(0, $total - $discountAmount);

            // Lưu thông tin voucher vào session
            session([
                'voucher' => [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'discount_amount' => $voucher->discount_amount,
                    'discount_percentage' => $voucher->discount_percentage
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'new_total' => $newTotal,
                'voucher_id' => $voucher->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng mã giảm giá'
            ], 500);
        }
    }

    public function ordersHistory()
    {
        $customer = auth('customer')->user();

        // Lấy danh sách đơn hàng
        $orders = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', '!=', 'cart')
            ->orderBy('order_date', 'desc')
            ->paginate(10);

        // Tính các thông số thống kê
        $totalOrders = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', '!=', 'cart')
            ->count();

        // Sửa phần tính tổng chi tiêu
        $totalSpent = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', '!=', 'cart')
            ->get()
            ->sum(function ($order) {
                return $order->getFinalTotal();
            });

        $completedOrders = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', 'completed')
            ->count();

        return view('Customer.shopping.order_list', compact(
            'orders',
            'totalOrders',
            'totalSpent',
            'completedOrders'
        ));
    }

    public function orderDetails(Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về customer hiện tại không
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('Customer.shopping.order_details', compact('order'));
    }

    public function cancelOrder(Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về customer hiện tại không
        if ($order->customer_id !== auth('customer')->id()) {
            return back()->with('error', 'Bạn không có quyền hủy đơn hàng này');
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->order_status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng ở trạng thái chờ xác nhận');
        }

        try {
            $order->update([
                'order_status' => 'cancelled'
            ]);

            return redirect()->route('customer.orders')
                ->with('success', 'Đã hủy đơn hàng thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng');
        }
    }
}
