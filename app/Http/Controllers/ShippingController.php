<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShippingController extends Controller
{
    public function index()
    {
        $shippings = ShippingMethod::paginate(10);
        return view('management.shipping_method_mana.index', compact('shippings'));
    }

    public function create()
    {
        return view('management.shipping_method_mana.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_name' => 'required|string|max:50|unique:shipping_methods',
            'shipping_fee' => 'required|numeric|min:0'
        ]);

        try {
            ShippingMethod::create([
                'method_name' => $request->method_name,
                'shipping_fee' => $request->shipping_fee
            ]);

            return redirect()
                ->route('admin.shipping')
                ->with('success', 'Thêm phương thức vận chuyển thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm phương thức vận chuyển');
        }
    }

    public function edit(ShippingMethod $shippingMethod)
    {
        return view('management.shipping_method_mana.edit', compact('shippingMethod'));
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $request->validate([
            'method_name' => 'required|string|max:50|unique:shipping_methods,method_name,' . $shippingMethod->method_id . ',method_id',
            'shipping_fee' => 'required|numeric|min:0'
        ]);

        try {
            $shippingMethod->update([
                'method_name' => $request->method_name,
                'shipping_fee' => $request->shipping_fee
            ]);

            return redirect()
                ->route('admin.shipping')
                ->with('success', 'Cập nhật phương thức vận chuyển thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật phương thức vận chuyển');
        }
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        try {
            $shippingMethod->delete();
            return redirect()
                ->route('admin.shipping')
                ->with('success', 'Xóa phương thức vận chuyển thành công');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Có lỗi xảy ra khi xóa phương thức vận chuyển');
        }
    }
}
