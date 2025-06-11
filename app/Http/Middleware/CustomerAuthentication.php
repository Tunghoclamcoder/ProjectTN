<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('customer')->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'redirect' => route('customer.login')
                ], 401);
            }
            return redirect()->route('customer.login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $customer = Auth::guard('customer')->user();
        if ($customer->status === 'inactive') {
            Auth::guard('customer')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('customer.login')
                ->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa.');
        }

        return $next($request);
    }
}
