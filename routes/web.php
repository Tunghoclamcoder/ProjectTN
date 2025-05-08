<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// 1) Trang chủ khách hàng
Route::get('/homepage', [ShopController::class, 'index'])->name('shop.home');

// 2) Trang login/registration cho Customer
Route::get('/login', [CustomerController::class, 'showLoginForm'])
     ->name('customer.login');
Route::post('/login', [CustomerController::class, 'login'])
     ->name('customer.login.submit');
Route::post('/logout', [CustomerController::class, 'logout'])
     ->name('customer.logout');

// 3) Các route chỉ dành cho Customer đã auth
Route::middleware('auth:customer')->group(function() {
    Route::get('/home', [CustomerController::class, 'home'])
         ->name('customer.home');
});

//Login route mặc định của Admin
Route::get('login', function() {
    return redirect()->route('admin.login');
})->name('login');

// Trang quản trị Admin
Route::prefix('admin')->group(function () {
    Route::middleware('guest:owner')->group(function () {
        Route::get('/login', [OwnerController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [OwnerController::class, 'login'])->name('admin.login.submit');
    });

    Route::middleware('auth:owner')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/logout', [OwnerController::class, 'logout'])->name('admin.logout');
    });
});

// Employee Routes
Route::prefix('employee')->group(function () {
    // Guest routes for employees
    Route::middleware('guest:employee')->group(function () {
        Route::get('/login', [EmployeeController::class, 'showLoginForm'])
            ->name('employee.login');
        Route::post('/login', [EmployeeController::class, 'login'])
            ->name('employee.login.submit');
    });

    // Protected routes for employees
    Route::middleware('auth:employee')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])
            ->name('employee.dashboard');
    });
});
