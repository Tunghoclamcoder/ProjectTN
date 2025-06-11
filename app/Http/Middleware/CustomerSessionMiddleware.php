<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Sử dụng session riêng cho customer
        config(['session.cookie' => 'customer_session']);

        // Thiết lập guard là 'customer'
        auth()->shouldUse('customer');

        return $next($request);
    }
}
