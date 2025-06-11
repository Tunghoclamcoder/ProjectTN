<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('employee/*')) {
                return route('employee.login');
            }
            return route('customer.login');
        }
        return null;
    }
}
