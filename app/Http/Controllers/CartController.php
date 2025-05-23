<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;


class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $customer = Auth::guard('customer')->user();

            // Check if product already exists in cart
            $cartItem = Order::where('customer_id', $customer->customer_id)
                          ->where('product_id', $product->product_id)
                          ->first();

            if ($cartItem) {
                // Update quantity if product exists
                $cartItem->quantity += 1;
                $cartItem->save();
            } else {
                // Create new cart item if product doesn't exist
                Order::create([
                    'customer_id' => $customer->customer_id,
                    'product_id' => $product->product_id,
                    'quantity' => 1
                ]);
            }

            return back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    }
}
