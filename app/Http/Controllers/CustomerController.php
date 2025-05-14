<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CustomerController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(Request $request)
{
    // 1. Validate input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 2. Kiểm tra thông tin đăng nhập (giả sử bạn có model Customer)
    $customer = \App\Models\Customer::where('email', $credentials['email'])->first();

    if ($customer && \Illuminate\Support\Facades\Hash::check($credentials['password'], $customer->password)) {
        // 3. Lưu thông tin đăng nhập vào session
        $request->session()->put('customer_id', $customer->id);

        // 4. Chuyển hướng về trang home
        return redirect()->route('customer.home');
    }

    // Nếu đăng nhập thất bại
    return back()->withErrors([
        'email' => 'Email hoặc mật khẩu không đúng.',
    ])->withInput();
}

   public function logout(Request $request)
{
    // Xóa session đăng nhập
    $request->session()->forget('customer_id');
    $request->session()->flush();

    // Chuyển về trang đăng nhập
    return redirect()->route('customer.login');
}

public function showRegisterForm()
{
    return view('customer.register');
}

public function register(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'customer_name' => 'required|string|max:100',
        'email' => 'required|email|unique:customers,email',
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

    return redirect()->route('customer.home');
}
          
    public function home()
    {
        return view('customer.home');
    }
}

