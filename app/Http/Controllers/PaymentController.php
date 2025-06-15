<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
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
