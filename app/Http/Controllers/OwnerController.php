<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class OwnerController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
                'role' => ['required', 'in:owner,employee']
            ]);

            $guard = $credentials['role'];
            unset($credentials['role']);

            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();

                // Chuyển hướng về dashboard sau khi đăng nhập thành công
                return redirect()->route('admin.dashboard');
            }

            return back()
                ->withErrors(['email' => 'Thông tin đăng nhập không chính xác.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()
                ->withErrors(['email' => 'Có lỗi xảy ra khi đăng nhập.'])
                ->withInput();
        }
    }

    public function showLoginForm()
    {
        if (Auth::guard('owner')->check() || Auth::guard('employee')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('Owner.login');
    }

    public function logout(Request $request)
{
    if (Auth::guard('owner')->check()) {
        Auth::guard('owner')->logout();
    } elseif (Auth::guard('employee')->check()) {
        Auth::guard('employee')->logout();
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('admin.login');
}

    public function dashboard()
    {
        // Kiểm tra user đang đăng nhập là owner hay employee
        if (Auth::guard('owner')->check()) {
            $user = Auth::guard('owner')->user();
        } else {
            $user = Auth::guard('employee')->user();
        }

        return view('Owner.dashboard', compact('user'));
    }
}
