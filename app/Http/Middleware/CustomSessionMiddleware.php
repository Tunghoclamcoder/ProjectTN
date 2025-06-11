<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Set session based on URL
        if (str_starts_with($request->path(), 'admin')) {
            config(['session.cookie' => 'admin_session']);
        } else {
            config(['session.cookie' => 'customer_session']);
        }

        return $next($request);
    }
}
