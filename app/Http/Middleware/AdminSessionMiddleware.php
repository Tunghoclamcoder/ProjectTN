<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Sử dụng session riêng cho admin
        config(['session.cookie' => 'admin_session']);

        // Thiết lập guard là 'admin'
        auth()->shouldUse('admin');

        return $next($request);
    }
}
