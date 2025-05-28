<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Check if trying to access admin routes
            if (str_starts_with($request->path(), 'admin')) {
                // If user is logged in as customer, redirect to homepage
                if (Auth::guard('customer')->check()) {
                    return route('shop.home');
                }
                return route('admin.login');
            }

            // Check if trying to access employee routes
            if (str_starts_with($request->path(), 'employee')) {
                if (Auth::guard('customer')->check()) {
                    return route('shop.home');
                }
                return route('employee.login');
            }

            return route('customer.login');
        }
        return null;
    }
}
