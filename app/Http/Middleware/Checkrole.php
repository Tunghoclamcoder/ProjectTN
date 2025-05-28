<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // If user is not logged in at all
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // If user is logged in but with wrong guard
        if (Auth::guard('customer')->check()) {
            return redirect()->route('shop.home')
                ->with('error', 'Bạn không có quyền truy cập trang này');
        }

        return $next($request);
    }
}
