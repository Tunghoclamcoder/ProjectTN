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
            return redirect()->route('customer.login')
                ->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng');
        }

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $customer = Auth::guard('customer')->user();
            $requestedQuantity = $request->quantity ?? 1;

            // Debug logging
            Log::info('Starting add to cart', [
                'product_id' => $product->product_id,
                'customer_id' => $customer->customer_id,
                'quantity' => $requestedQuantity
            ]);

            // Check stock
            if ($product->quantity <= 0) {
                DB::rollBack();
                return back()->with('error', 'Sản phẩm đã hết hàng');
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
                    return back()->with('error', 'Tổng số lượng vượt quá số lượng tồn kho');
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

                Log::info('Updated cart item', [
                    'order_id' => $cartOrder->order_id,
                    'product_id' => $product->product_id,
                    'new_quantity' => $newQuantity
                ]);
            } else {
                // Create new item
                OrderDetail::create([
                    'order_id' => $cartOrder->order_id,
                    'product_id' => $product->product_id,
                    'sold_quantity' => $requestedQuantity,
                    'sold_price' => $product->getDiscountedPrice()
                ]);

                Log::info('Created new cart item', [
                    'order_id' => $cartOrder->order_id,
                    'product_id' => $product->product_id,
                    'quantity' => $requestedQuantity
                ]);
            }

            // Refresh order relations to get updated total
            $cartOrder->load('orderDetails');

            DB::commit();

            return back()->with('success', "Đã thêm $requestedQuantity sản phẩm vào giỏ hàng");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
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
