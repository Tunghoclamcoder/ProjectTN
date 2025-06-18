<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

use Illuminate\Support\Facades\Route;

// 1) Trang chủ khách hàng
Route::get('/', function () {
    return redirect('/homepage');
});
Route::get('/homepage', [ShopController::class, 'index'])->name('shop.home');

// 2) Trang đăng ký, đăng nhập, đăng xuất cho Customer
Route::middleware('guest:customer')->group(function () {
    Route::get('/customer/register', [CustomerController::class, 'showRegisterForm'])->name('customer.register');
    Route::post('/customer/register', [CustomerController::class, 'register'])->name('customer.register.submit');
    Route::get('/customer/login', [CustomerController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/customer/login', [CustomerController::class, 'login'])->name('customer.login.submit');
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/customer/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/update-profile', [CustomerController::class, 'updateProfile'])->name('customer.update.profile');
    Route::post('/customer/logout', [CustomerController::class, 'logout'])->name('customer.logout');
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add-to-cart');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
    Route::delete('/cart/delete/{productId}', [CartController::class, 'deleteItem'])->name('cart.delete-item');
    Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/voucher/apply', [OrderController::class, 'applyVoucher'])->name('voucher.apply');
    Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('order.store');
    Route::get('/orders', [OrderController::class, 'ordersHistory'])
        ->name('customer.orders');
    Route::get('/orders/{order}', [OrderController::class, 'orderDetails'])
        ->name('customer.orders.detail');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancelOrder'])
        ->name('customer.orders.cancel');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.delete');
    Route::get('/product/{product}', [ProductController::class, 'show'])->name('shop.product.show');
});

//Search cho trang chủ của Customer
Route::get('/search-suggestions', [ShopController::class, 'searchSuggestions'])
    ->name('products.search.suggestions');
Route::get('/search', [ShopController::class, 'search'])
    ->name('products.search');

//Trang hiển thị danh mục sản phẩm
Route::get('/categories', [CategoryController::class, 'categoryList'])->name('categories.list');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

//Trang hiển thị thương hiệu sản phẩm
Route::get('/brands', [BrandController::class, 'brandList'])->name('brands.list');
Route::get('/brands/{id}', [BrandController::class, 'show'])->name('brands.show');

// Quên mật khẩu
Route::get('customer/forgot-password', [CustomerController::class, 'showForgotPasswordForm'])->name('customer.forgot_password');
Route::post('customer/forgot-password', [CustomerController::class, 'sendResetLinkEmail'])->name('customer.send_reset_link');
Route::get('customer/reset-password/{token}', [CustomerController::class, 'showResetPasswordForm'])->name('customer.reset_password');
Route::post('customer/reset-password', [CustomerController::class, 'resetPassword'])->name('customer.update_password');

// Đổi mật khẩu (cần đăng nhập)
Route::get('customer/change-password', [CustomerController::class, 'showChangePasswordForm'])->middleware('auth:customer')->name('customer.change_password');
Route::post('customer/change-password', [CustomerController::class, 'changePassword'])->middleware('auth:customer')->name('customer.update_change_password');

Route::get('/product/{product_id}/reviews', [FeedbackController::class, 'showProductReviews'])->name('product.reviews');

//Login route mặc định của Admin
Route::get('login', function () {
    // Get current URL and intended URL
    $intended = session()->get('url.intended');

    // Check if trying to access admin routes
    if ($intended && str_contains($intended, '/admin')) {
        return redirect()->route('admin.login');
    }

    // Check if trying to access employee routes
    if ($intended && str_contains($intended, '/employee')) {
        return redirect()->route('employee.login');
    }

    // Default to customer login
    return redirect()->route('customer.login');
})->name('login');


// Routes xử lý cho Owner và đăng nhập đăng xuất Admin
Route::prefix('admin')->group(function () {
    Route::middleware('guest:owner,employee')->group(function () {
        Route::get('/login', [OwnerController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [OwnerController::class, 'login'])->name('admin.login.submit');
    });

    // Routes yêu cầu Owner đã đăng nhập
    Route::middleware(['auth:owner,employee'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard')
            ->withoutMiddleware(['prevent-back-history']);
        Route::get('/search', [DashboardController::class, 'search'])
            ->name('admin.search');

        Route::get('/dashboard/brand-stats', [DashboardController::class, 'getBrandSalesStats'])
            ->name('admin.dashboard.brandStats');

        Route::get('/profile', [AdminController::class, 'show'])
            ->name('admin.profile.show');
        Route::put('/profile/update', [AdminController::class, 'update'])
            ->name('admin.profile.update');

        Route::get('/search/suggestions', [DashboardController::class, 'searchSuggestions'])
            ->name('admin.search.suggestions');
        Route::post('/logout', [AdminController::class, 'logout'])
            ->name('admin.logout');

        // Routes quản lý nhân viên
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('admin.employee');
            Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employee.create');
            Route::post('/', [EmployeeController::class, 'store'])->name('admin.employee.store');
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('admin.employee.update');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employee.delete');
            Route::get('/search', [EmployeeController::class, 'search'])->name('admin.employee.search');
        });

        Route::prefix('customer')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('admin.customer');
            Route::post('/', [CustomerController::class, 'store'])->name(name: 'admin.customer.store');
            Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])
                ->name('admin.customer.toggle-status');
            Route::get('/search', [CustomerController::class, 'search'])->name('admin.customer.search'); // Put search route before other routes
        });

        // Routes quản lý material
        Route::prefix('materials')->group(function () {
            Route::get('/', [MaterialController::class, 'index'])->name('admin.material');
            Route::get('/create', [MaterialController::class, 'create'])->name('admin.material.create');
            Route::post('/', [MaterialController::class, 'store'])->name('admin.material.store');
            Route::get('/{material}/edit', [MaterialController::class, 'edit'])->name('admin.material.edit');
            Route::put('/{material}', [MaterialController::class, 'update'])->name('admin.material.update');
            Route::delete('/{material}', [MaterialController::class, 'destroy'])->name('admin.material.delete');
            Route::get('/search', [MaterialController::class, 'search'])->name('admin.material.search');
        });

        // Routes quản lý brands
        Route::prefix('brands')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('admin.brand');
            Route::get('/create', [BrandController::class, 'create'])->name('admin.brand.create');
            Route::post('/', [BrandController::class, 'store'])->name('admin.brand.store');
            Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('admin.brand.edit');
            Route::put('/{brand}', [BrandController::class, 'update'])->name('admin.brand.update');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('admin.brand.delete');
            Route::get('/search', [BrandController::class, 'search'])->name('admin.brand.search');
        });

        Route::prefix('sizes')->group(function () {
            Route::get('/', [SizeController::class, 'index'])->name('admin.size');
            Route::get('/create', [SizeController::class, 'create'])->name('admin.size.create');
            Route::post('/', [SizeController::class, 'store'])->name('admin.size.store');
            Route::get('/{size}/edit', [SizeController::class, 'edit'])->name('admin.size.edit');
            Route::put('/{size}', [SizeController::class, 'update'])->name('admin.size.update');
            Route::delete('/{size}', [SizeController::class, 'destroy'])->name('admin.size.delete');
            Route::get('search', [SizeController::class, 'search'])
                ->name('admin.sizes.search');
        });

        // Routes quản lý categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category');
            Route::get('/create', [CategoryController::class, 'create'])->name('admin.category.create');
            Route::post('/', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::get('/{category}/edit', action: [CategoryController::class, 'edit'])->name('admin.category.edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('admin.category.delete');
            Route::get('/search', [CategoryController::class, 'search'])
                ->name('admin.category.search');
        });

        // Routes quản lý images
        Route::prefix('images')->group(function () {
            Route::get('/', [ImageController::class, 'index'])->name('admin.image');
            Route::get('/create', [ImageController::class, 'create'])->name('admin.image.create');
            Route::get('/{image}/edit', action: [ImageController::class, 'edit'])->name('admin.image.edit');
            Route::post('/', [ImageController::class, 'store'])->name('admin.image.store');
            Route::put('/{image}', [ImageController::class, 'update'])->name('admin.image.update');
            Route::delete('/{image}', [ImageController::class, 'destroy'])->name('admin.image.delete');
        });

        // Routes quản lý products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('admin.product');
            Route::get('/create', [ProductController::class, 'create'])->name('admin.product.create');
            Route::post('/', [ProductController::class, 'store'])->name('admin.product.store');
            Route::get('/{product}/details', [ProductController::class, 'adminShow'])->name('admin.product.details');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('admin.product.edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('admin.product.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('admin.product.delete');
            Route::get('/search', [ProductController::class, 'search'])
                ->name('admin.product.search');
        });

        // Routes quản lý vouchers
        Route::prefix('vouchers')->group(function () {
            Route::get('/', [VoucherController::class, 'index'])->name('admin.voucher');
            Route::get('/create', [VoucherController::class, 'create'])->name('admin.voucher.create');
            Route::post('/', [VoucherController::class, 'store'])->name('admin.voucher.store');
            Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('admin.voucher.edit');
            Route::put('/{voucher}', [VoucherController::class, 'update'])->name('admin.voucher.update');
            Route::delete('/{voucher}', action: [VoucherController::class, 'destroy'])->name('admin.voucher.delete');
            // Route::post('vouchers/apply', action: [VoucherController::class, 'apply'])->name('admin.voucher.apply');
            Route::post('voucher/{voucher}/toggle', [VoucherController::class, 'toggleStatus'])
                ->name('admin.voucher.toggle');
            Route::get('/search', [VoucherController::class, 'search'])
                ->name('admin.voucher.search');
        });

        // Routes quản lý payment-methods
        Route::prefix('payment-methods')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('admin.payment');
            Route::get('/create', [PaymentController::class, 'create'])->name('admin.payment.create');
            Route::post('/', [PaymentController::class, 'store'])->name('admin.payment.store');
            Route::get('/{paymentMethod}/edit', [PaymentController::class, 'edit'])->name('admin.payment.edit');
            Route::put('/{paymentMethod}', [PaymentController::class, 'update'])->name('admin.payment.update');
            Route::delete('/{paymentMethod}', [PaymentController::class, 'destroy'])->name('admin.payment.delete');
            Route::get('/search', [PaymentController::class, 'search'])
                ->name('admin.payment.search');
        });

        // Routes quản lý shipping-methods
        Route::prefix('shipping-methods')->group(function () {
            Route::get('/', [ShippingController::class, 'index'])->name('admin.shipping');
            Route::get('/create', [ShippingController::class, 'create'])->name('admin.shipping.create');
            Route::post('/', [ShippingController::class, 'store'])->name('admin.shipping.store');
            Route::get('/{shippingMethod}/edit', [ShippingController::class, 'edit'])->name('admin.shipping.edit');
            Route::put('/{shippingMethod}', [ShippingController::class, 'update'])->name('admin.shipping.update');
            Route::delete('/{shippingMethod}', [ShippingController::class, 'destroy'])->name('admin.shipping.delete');
            Route::get('/search', [ShippingController::class, 'search'])
                ->name('admin.shipping.search');
        });

        // Routes quản lý orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('admin.order');
            Route::get('/create', [OrderController::class, 'create'])->name(name: 'admin.order.create');
            Route::post('/', [OrderController::class, 'store'])->name('admin.order.store');
            Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('admin.order.edit');
            Route::get('/{order}/show', [OrderController::class, 'show'])->name('admin.order.show');
            Route::put('/{order}/update-status', [OrderController::class, 'updateStatus'])
                ->name('admin.order.update-status');
            Route::put('/{order}', [OrderController::class, 'update'])->name('admin.order.update');
            Route::get('/search', [OrderController::class, 'search'])->name('admin.orders.search');
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
