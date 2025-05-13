<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem người dùng đã đăng nhập với role owner hoặc employee chưa
        if (Auth::guard('owner')->check() || Auth::guard('employee')->check()) {
            return $next($request);
        }

        // Nếu chưa đăng nhập, chuyển hướng về trang login
        return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập để truy cập trang quản trị');
    }
}
