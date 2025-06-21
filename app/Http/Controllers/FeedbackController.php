<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Order;


class FeedbackController extends Controller
{
    public function submitReview(Request $request, $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('feedback')->findOrFail($orderId);

        // Đảm bảo chỉ customer của đơn hàng mới được đánh giá
        if ($order->customer_id !== auth('customer')->id()) {
            abort(403, 'Bạn không có quyền đánh giá đơn hàng này.');
        }

        // Kiểm tra đã đánh giá chưa
        if (Feedback::where('order_id', $orderId)->where('customer_id', auth('customer')->id())->exists()) {
            return back()->with('error', 'Bạn đã đánh giá đơn hàng này rồi.');
        }

        Feedback::create([
            'customer_id' => auth('customer')->id(),
            'order_id' => $orderId,
            'comment' => $request->review_text,
            'rating' => $request->rating,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá đơn hàng!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'comment' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'customer_id' => Auth::guard('customer')->id(),
            'product_id' => $validated['product_id'],
            'comment' => $validated['comment'],
            'rating' => $validated['rating'],
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }

    public function update(Request $request, Feedback $feedback)
    {
        // Ensure the feedback belongs to the logged-in customer
        if ($feedback->customer_id !== Auth::guard('customer')->id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa đánh giá này!');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật đánh giá thành công!');
    }

    public function destroy($id)
    {
        Feedback::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa feedback!');
    }

    // Method to show product reviews
    public function showProductReviews($product_id)
    {
        $feedbacks = Feedback::with('customer')
            ->where('product_id', $product_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('products.reviews', compact('feedbacks'));
    }
}
