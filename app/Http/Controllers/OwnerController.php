<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use App\Traits\PreventBackHistory;

class OwnerController extends Controller
{
    use PreventBackHistory;
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

            Log::info('Login attempt', [
                'email' => $credentials['email'],
                'guard' => $guard
            ]);

            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials.'
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Login failed.']);
        }
    }

    public function showLoginForm()
    {
        Log::info('Accessing admin login form');

        if (Auth::guard('owner')->check() || Auth::guard('employee')->check()) {
            return redirect()->route('admin.dashboard');
        }
        $response = view('management.login');
        return $this->preventBackHistory($response);
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Đăng xuất thành công!');
    }

    // public function dashboard()
    // {
    //     try {
    //         // Clear intended URL trước khi kiểm tra auth
    //         session()->forget('url.intended');

    //         // Kiểm tra auth cho cả owner và employee
    //         if (!Auth::guard('owner')->check() && !Auth::guard('employee')->check()) {
    //             // Log để debug
    //             Log::info('User not authenticated, redirecting to admin login');

    //             return redirect()->route('admin.login')
    //                 ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
    //         }

    //         // Lấy thông tin user đang đăng nhập
    //         $user = Auth::guard('owner')->check()
    //             ? Auth::guard('owner')->user()
    //             : Auth::guard('employee')->user();

    //         $data = [
    //             'user' => $user
    //         ];

    //         // Thêm header để ngăn cache
    //         return response()
    //             ->view('management.dashboard', compact('data'))
    //             ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
    //             ->header('Pragma', 'no-cache')
    //             ->header('Expires', '0');
    //     } catch (\Exception $e) {
    //         Log::error('Dashboard error: ' . $e->getMessage());

    //         return redirect()
    //             ->route('admin.login')
    //             ->with('error', 'Có lỗi xảy ra khi truy cập dashboard');
    //     }
    // }
}
