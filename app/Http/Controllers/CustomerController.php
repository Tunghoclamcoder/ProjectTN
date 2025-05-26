<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

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

        Log::info('Login attempt', ['email' => $credentials['email']]);

        // Kiểm tra xem tài khoản có tồn tại và đang hoạt động không
        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer) {
            Log::info('Customer not found');
            return redirect()
                ->route('customer.login')
                ->with('error', 'Email hoặc mật khẩu không đúng.')
                ->withInput();
        }

        if ($customer->status === 'inactive') {
            Log::info('Customer account is inactive');
            return redirect()
                ->route('customer.login')
                ->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa.')
                ->withInput();
        }

        if (Auth::guard('customer')->attempt($credentials)) {
            Log::info('Login successful');
            $request->session()->regenerate();

            return redirect()->intended(route('shop.home'))
                ->with('success', 'Đăng nhập thành công! Chào mừng bạn trở lại.');
        }

        Log::info('Login failed - invalid credentials');
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
        return redirect()->route('shop.home');
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

    public function profile()
    {
        $customer = Auth::guard('customer')->user();

        return view('Customer.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $authCustomer = Auth::guard('customer')->user();
        $customer = Customer::find($authCustomer->customer_id);

        $customer->update([
            'customer_name' => $request->customer_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }
}
