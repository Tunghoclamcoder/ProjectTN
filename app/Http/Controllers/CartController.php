<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng',
                'redirect' => route('customer.login')
            ], 401);
        }

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $customer = Auth::guard('customer')->user();
            $requestedQuantity = $request->quantity ?? 1;

            // Check stock
            if ($product->quantity <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm đã hết hàng'
                ], 400);
            }

            // Get or create cart
            $cartOrder = Order::firstOrCreate(
                [
                    'customer_id' => $customer->customer_id,
                    'order_status' => 'cart'
                ],
                [
                    'order_date' => now()
                ]
            );

            // Find existing cart item
            $orderDetail = OrderDetail::where([
                'order_id' => $cartOrder->order_id,
                'product_id' => $product->product_id
            ])->first();

            if ($orderDetail) {
                // Update existing item
                $newQuantity = $orderDetail->sold_quantity + $requestedQuantity;

                if ($newQuantity > $product->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Tổng số lượng vượt quá số lượng tồn kho'
                    ], 400);
                }

                DB::table('order_details')
                    ->where([
                        'order_id' => $cartOrder->order_id,
                        'product_id' => $product->product_id
                    ])
                    ->update([
                        'sold_quantity' => $newQuantity,
                        'sold_price' => $product->getDiscountedPrice()
                    ]);
            } else {
                // Create new item
                OrderDetail::create([
                    'order_id' => $cartOrder->order_id,
                    'product_id' => $product->product_id,
                    'sold_quantity' => $requestedQuantity,
                    'sold_price' => $product->getDiscountedPrice()
                ]);
            }

            // Refresh order relations to get updated total
            $cartOrder->load('orderDetails');
            $cartCount = $cartOrder->orderDetails->sum('sold_quantity');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã thêm $requestedQuantity sản phẩm vào giỏ hàng",
                'cartCount' => $cartCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng'
            ], 500);
        }
    }

    // Thêm method mới để xử lý cập nhật số lượng
    public function updateQuantity(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,product_id',
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($request->product_id);
            $customer = Auth::guard('customer')->user();

            if ($request->quantity > $product->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng đặt vào giỏ vượt quá số lượng sản phẩm có sẵn'
                ], 400);
            }

            $cartOrder = Order::where('customer_id', $customer->customer_id)
                ->where('order_status', 'cart')
                ->first();

            if ($cartOrder) {
                // Thay đổi cách cập nhật số lượng
                $updated = DB::table('order_details')
                    ->where('order_id', $cartOrder->order_id)
                    ->where('product_id', $request->product_id)
                    ->update(['sold_quantity' => $request->quantity]);

                if ($updated) {
                    // Tải lại order details để tính tổng
                    $cartOrder->load('orderDetails');
                    $cartTotal = $cartOrder->orderDetails->sum(function ($detail) {
                        return $detail->sold_quantity * $detail->sold_price;
                    });

                    return response()->json([
                        'success' => true,
                        'cartTotal' => $cartTotal
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    public function viewCart()
    {
        $customer = Auth::guard('customer')->user();

        $cartOrder = Order::where('customer_id', $customer->customer_id)
            ->where('order_status', 'cart')
            ->with(['orderDetails.product'])
            ->first();

        $cartItems = $cartOrder ? $cartOrder->orderDetails : collect();

        return view('Customer.shopping.cart', compact('cartOrder', 'cartItems'));
    }

    public function deleteItem($productId)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $cartOrder = Order::where('customer_id', $customer->customer_id)
                ->where('order_status', 'cart')
                ->first();

            if ($cartOrder) {
                $deleted = OrderDetail::where([
                    ['order_id', '=', $cartOrder->order_id],
                    ['product_id', '=', $productId]
                ])->delete();

                if ($deleted) {
                    session()->flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
                    return response()->json([
                        'success' => true,
                        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage()
            ], 500);
        }
    }
    public function clearCart()
    {
        try {
            $customer = Auth::guard('customer')->user();
            $cartOrder = Order::where('customer_id', $customer->customer_id)
                ->where('order_status', 'cart')
                ->first();

            if ($cartOrder) {
                OrderDetail::where('order_id', $cartOrder->order_id)->delete();

                session()->flash('success', 'Đã xóa tất cả sản phẩm khỏi giỏ hàng');
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false], 404);
        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi xóa giỏ hàng');
            return response()->json(['success' => false], 500);
        }
    }
}
