<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Voucher;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $date = $request->get('date');
            $status = $request->get('status');

            $orders = Order::with(['orderDetails', 'voucher'])
                ->where(function ($q) use ($query) {
                    $q->where('receiver_name', 'LIKE', "%{$query}%")
                        ->orWhere('receiver_phone', 'LIKE', "%{$query}%")
                        ->orWhere('receiver_address', 'LIKE', "%{$query}%");
                });

            if ($date) {
                $orders->whereDate('order_date', $date);
            }

            if ($status) {
                $orders->where('order_status', $status);
            }

            $results = $orders->get()->map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'receiver_name' => $order->receiver_name,
                    'receiver_phone' => $order->receiver_phone,
                    'receiver_address' => $order->receiver_address,
                    'order_date' => $order->order_date,
                    'order_status' => $order->order_status,
                    'total_amount' => $order->getTotalAmount(),
                    'voucher' => $order->voucher ? [
                        'code' => $order->voucher->code,
                        'discount_amount' => $order->voucher->discount_amount
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Order search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $orders = Order::with(['orderDetails', 'shippingMethod', 'voucher'])
            ->orderBy('order_date', 'desc')
            ->paginate(10);

        return view('management.order_mana.index', compact('orders'));
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

        $statusOrder = [
            'pending' => 0,
            'confirmed' => 1,
            'shipping' => 2,
            'completed' => 3,
            'cancelled' => 4
        ];

        // Validate request
        $request->validate([
            'order_status' => [
                'required',
                'in:pending,confirmed,shipping,completed,cancelled',
                function ($attribute, $value, $fail) use ($order, $statusOrder) {
                    // Kiểm tra nếu đang cố chuyển về trạng thái trước đó
                    if ($statusOrder[$value] < $statusOrder[$order->order_status]) {
                        $fail('Không thể chuyển về trạng thái trước đó.');
                    }

                    // Kiểm tra nếu đơn hàng đã hoàn thành hoặc đã hủy
                    if (($order->order_status === 'completed' || $order->order_status === 'cancelled')
                        && $value !== $order->order_status
                    ) {
                        $fail('Không thể thay đổi trạng thái của đơn hàng đã hoàn thành hoặc đã hủy.');
                    }
                },
            ],
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

            $request->validate([
                'order_status' => ['required', Rule::in($validStatuses)]
            ], [
                'order_status.required' => 'Trạng thái đơn hàng không được để trống',
                'order_status.in' => 'Trạng thái đơn hàng không hợp lệ'
            ]);

            if ($order->order_status === 'completed' || $order->order_status === 'cancelled') {
                return back()->with('error', 'Không thể thay đổi trạng thái của đơn hàng đã hoàn thành hoặc đã hủy');
            }

            DB::beginTransaction();
            try {
                $oldStatus = $order->order_status;
                $newStatus = $request->order_status;

                // Only deduct quantities when changing from pending to confirmed/shipping/completed
                if ($oldStatus === 'pending' && in_array($newStatus, ['confirmed', 'shipping', 'completed'])) {
                    foreach ($order->orderDetails as $orderDetail) {
                        $product = $orderDetail->product;
                        if ($product->quantity < $orderDetail->sold_quantity) {
                            throw new \Exception("Sản phẩm {$product->product_name} không đủ số lượng trong kho");
                        }
                        $product->quantity -= $orderDetail->sold_quantity;
                        $product->save();
                    }
                }

                // Return quantities when cancelling from confirmed/shipping/completed
                if ($newStatus === 'cancelled' && in_array($oldStatus, ['confirmed', 'shipping', 'completed'])) {
                    foreach ($order->orderDetails as $orderDetail) {
                        $product = $orderDetail->product;
                        $product->quantity += $orderDetail->sold_quantity;
                        $product->save();
                    }
                }

                $order->update([
                    'order_status' => $newStatus
                ]);

                DB::commit();

                $message = match ($newStatus) {
                    'confirmed' => 'Đã xác nhận đơn hàng và cập nhật số lượng sản phẩm',
                    'shipping' => 'Đã chuyển trạng thái sang đang giao hàng',
                    'completed' => 'Đã hoàn thành đơn hàng',
                    'cancelled' => 'Đã hủy đơn hàng' .
                        (in_array($oldStatus, ['confirmed', 'shipping', 'completed']) ? ' và hoàn lại số lượng sản phẩm' : ''),
                    default => 'Cập nhật trạng thái đơn hàng thành công'
                };

                return back()->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
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
            $today = now()->format('Y-m-d'); // Format as date string

            // Debug current date
            Log::info('Current date:', ['today' => $today]);

            // Get cart order and total
            $cartOrder = Order::where([
                'customer_id' => $customer->customer_id,
                'order_status' => 'cart'
            ])->with(['orderDetails.product'])->firstOrFail();

            $total = $cartOrder->getTotalAmount();

            // Get used vouchers
            $usedVoucherIds = Order::where('customer_id', $customer->customer_id)
                ->whereNotNull('voucher_id')
                ->pluck('voucher_id');

            // Query active vouchers using direct SQL comparison
            $activeVouchersQuery = DB::table('vouchers')
                ->select('*')
                ->where('status', true)
                ->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') <= ?", [$today])
                ->whereRaw("STR_TO_DATE(expiry_date, '%Y-%m-%d') >= ?", [$today])
                ->where('minimum_purchase_amount', '<=', $total)
                ->where(function ($query) use ($total) {
                    $query->whereNull('maximum_purchase_amount')
                        ->orWhere('maximum_purchase_amount', '>=', $total);
                })
                ->where(function ($query) {
                    $query->whereNull('max_usage_count')
                        ->orWhere('usage_count', '<', DB::raw('max_usage_count'));
                })
                ->whereNotIn('id', $usedVoucherIds);

            // Debug query
            Log::info('Voucher Query:', [
                'sql' => $activeVouchersQuery->toSql(),
                'bindings' => $activeVouchersQuery->getBindings(),
                'total' => $total
            ]);

            // Convert query results to Voucher models
            $activeVouchers = Voucher::hydrate($activeVouchersQuery->get()->toArray());

            // Debug found vouchers
            Log::info('Found Vouchers:', [
                'count' => $activeVouchers->count(),
                'vouchers' => $activeVouchers->map(function ($v) {
                    return [
                        'code' => $v->code,
                        'start_date' => $v->start_date,
                        'expiry_date' => $v->expiry_date,
                        'min_amount' => $v->minimum_purchase_amount
                    ];
                })->toArray()
            ]);

            // Get other required data
            $paymentMethods = PaymentMethod::all();
            $shippingMethods = ShippingMethod::all();

            return view('Customer.shopping.checkout', compact(
                'cartOrder',
                'total',
                'paymentMethods',
                'shippingMethods',
                'activeVouchers'
            ));
        } catch (\Exception $e) {
            Log::error('Checkout Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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

            // Check stock availability only
            foreach ($cartOrder->orderDetails as $item) {
                if ($item->sold_quantity > $item->product->quantity) {
                    throw new \Exception("Sản phẩm {$item->product->product_name} chỉ còn {$item->product->quantity} trong kho");
                }
            }

            if (session()->has('voucher')) {
                $voucherData = session('voucher');
                $voucher = Voucher::find($voucherData['id']);

                if (
                    !$voucher || !$voucher->status ||
                    $voucher->expiry_date < now() ||
                    ($voucher->max_usage_count && $voucher->usage_count >= $voucher->max_usage_count)
                ) {
                    throw new \Exception('Mã giảm giá không còn hiệu lực hoặc đã hết lượt sử dụng');
                }

                $voucher->increment('usage_count');
            }

            // Update cart to order - without deducting quantities
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

            // Remove the product quantity deduction from here
            // It will now only happen when order status changes to confirmed/shipping/completed

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

        $orders = Order::with([
            'orderDetails',
            'shipping_method',
            'voucher'
        ])
            ->where('customer_id', $customer->customer_id)
            ->where('order_status', '!=', 'cart')
            ->orderBy('order_date', 'desc')
            ->paginate(10);

        $totalOrders = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', '!=', 'cart')
            ->count();

        // Tính tổng chi phí bao gồm cả phí vận chuyển
        $totalSpent = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', '!=', 'cart')
            ->with('shipping_method')
            ->get()
            ->sum(function ($order) {
                return $order->getFinalTotal() + ($order->shipping_method ? $order->shipping_method->shipping_fee : 0);
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
        // Kiểm tra xem đơn hàng có thuộc về khách hàng hiện tại không
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['shippingMethod', 'paymentMethod', 'orderDetails.product']);

        // Tính tổng cộng với phí vận chuyển
        $totalWithShipping = $order->getFinalTotal() + ($order->shipping_method ? $order->shipping_method->shipping_fee : 0);

        return view('Customer.shopping.order_details', compact('order', 'totalWithShipping'));
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
            DB::beginTransaction();

            // Hoàn lại số lượng sản phẩm
            foreach ($order->orderDetails as $orderDetail) {
                $product = $orderDetail->product;
                $product->quantity += $orderDetail->sold_quantity;
                $product->save();
            }

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'order_status' => 'cancelled'
            ]);

            DB::commit();

            return redirect()->route('customer.orders')
                ->with('success', 'Đã hủy đơn hàng và hoàn lại số lượng sản phẩm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
        }
    }

    public function returnOrder(Order $order)
    {
        try {
            if (!$order->canBeReturned()) {
                return redirect()->back()
                    ->with('error', 'Đơn hàng không thể hoàn trả.');
            }

            $order->processReturn();

            return redirect()->back()
                ->with('success', 'Đơn hàng đã được hoàn trả thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý hoàn trả: ' . $e->getMessage());
        }
    }
}
