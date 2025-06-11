<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('owner')->check() || Auth::guard('employee')->check()) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'redirect' => route('admin.login')
            ], 401);
        }

        return redirect()->route('admin.login')
            ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
    }
}
