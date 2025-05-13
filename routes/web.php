<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// 1) Trang chủ khách hàng
Route::get('/', function () {
    return redirect('/homepage');
});
Route::get('/homepage', [ShopController::class, 'index'])->name('shop.home');

// 2) Trang login/registration cho Customer
Route::middleware('guest:customer')->group(function () {
    Route::get('/login', [CustomerController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/login', [CustomerController::class, 'login'])->name('customer.login.submit');
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/home', [CustomerController::class, 'home'])->name('customer.home');
    Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
});

//Login route mặc định của Admin
Route::get('login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Routes xử lý cho Owner và đăng nhập đăng xuất Admin
Route::prefix('admin')->group(function () {
    // Routes cho owner chưa đăng nhập
    Route::middleware('guest:owner,employee')->group(function () {
        Route::get('/login', [OwnerController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [OwnerController::class, 'login'])->name('admin.login.submit');
    });

    // Routes yêu cầu Owner đã đăng nhập
    Route::middleware('auth:owner,employee')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/logout', [OwnerController::class, 'logout'])->name('admin.logout');

        // Routes quản lý nhân viên
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('admin.employee');
            Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employee.create');
            Route::post('/', [EmployeeController::class, 'store'])->name('admin.employee.store');
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('admin.employee.update');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employee.delete');
        });
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
        Route::post('/logout', [EmployeeController::class, 'logout'])->name('employee.logout');
    });
});
