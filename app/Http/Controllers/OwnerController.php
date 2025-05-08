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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::guard('owner')->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            Log::info('Login failed for email: ' . $credentials['email']);

            return back()->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ])->withInput();
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Có lỗi xảy ra khi đăng nhập.',
            ])->withInput();
        }
    }

    public function showLoginForm()
    {
        if (Auth::guard('owner')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function dashboard()
    {
        if (!Auth::guard('owner')->check()) {
            return redirect()->route('admin.login');
        }
        return view('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
