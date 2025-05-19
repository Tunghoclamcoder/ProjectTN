<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Voucher;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'employee', 'voucher', 'paymentMethod', 'shippingMethod'])->get();
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
        $customers = Customer::all();
        $employees = Employee::all();
        $vouchers = Voucher::all();
        $paymentMethods = PaymentMethod::all();
        $shippingMethods = ShippingMethod::all();

        return view('management.order_mana.edit', compact(
            'order',
            'customers',
            'employees',
            'vouchers',
            'paymentMethods',
            'shippingMethods'
        ));
    }

    public function update(Request $request, Order $order)
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

        try {
            $order->update($request->all());

            return redirect()
                ->route('admin.order')
                ->with('success', 'Cập nhật đơn hàng thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng');
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
}
