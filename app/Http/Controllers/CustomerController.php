<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function showLoginForm()
    {
        return view('Customer.LoginAccount.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if account is active before login
        $customer = Customer::where('email', $credentials['email'])->first();
        if ($customer && $customer->status === 'inactive') {
            return redirect()
                ->route('customer.login')
                ->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa.')
                ->withInput();
        }

        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()
                ->route('shop.home')
                ->with('success', 'Đăng nhập thành công! Chào mừng bạn trở lại.');
        }

        return redirect()
            ->route('customer.login')
            ->with('error', 'Email hoặc mật khẩu không đúng.')
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('customer.login');
    }
    public function showRegisterForm()
    {
        return view('Customer.LoginAccount.register');
    }

    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create new customer
        $customer = new \App\Models\Customer();
        $customer->customer_name = $validated['customer_name'];
        $customer->email = $validated['email'];
        $customer->phone_number = $validated['phone_number'] ?? null;
        $customer->address = $validated['address'] ?? null;
        $customer->password = bcrypt($validated['password']);
        $customer->save();

        // Auto-login after registration
        $request->session()->put('customer_id', $customer->customer_id);

        return redirect()->route('customer.login');
    }

    public function home()
    {
        return view('shop.home');
    }

    public function index()
    {
        $customers = Customer::orderBy('customer_id', 'asc')->paginate(8);
        return view('management.customer_mana.index', compact('customers'));
    }

    public function toggleStatus(Customer $customer)
    {
        try {
            $newStatus = $customer->status == 'active' ? 'inactive' : 'active';
            $customer->update(['status' => $newStatus]);

            // Nếu tài khoản bị vô hiệu hóa, đăng xuất tất cả các phiên
            if ($newStatus === 'inactive') {
                // Buộc đăng xuất khách hàng nếu họ hiện đang đăng nhập
                Auth::guard('customer')->logout();
            }

            return response()->json([
                'success' => true,
                'message' => 'Thay đổi trạng thái thành công',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái'
            ], 500);
        }
    }
}
