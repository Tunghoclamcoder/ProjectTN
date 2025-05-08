<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class EmployeeController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }
        return view('employee.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::guard('employee')->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('employee.dashboard'));
            }

            Log::info('Employee login failed for email: ' . $credentials['email']);

            return back()->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ])->withInput();
        } catch (\Exception $e) {
            Log::error('Employee login error: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Có lỗi xảy ra khi đăng nhập.',
            ])->withInput();
        }
    }

    public function dashboard()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect()->route('employee.login');
        }
        return view('employee.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('employee.login');
    }
}
