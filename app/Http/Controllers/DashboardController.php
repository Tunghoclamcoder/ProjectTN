<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\PreventBackHistory;

class DashboardController extends Controller
{
    use PreventBackHistory;

    public function index()
    {
        try {
            // Kiểm tra auth
            if (!Auth::guard('owner')->check() && !Auth::guard('employee')->check()) {
                return redirect()->route('admin.login')
                    ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }

            Log::info('Starting dashboard data collection');

            // 1. Số lượng khách hàng
            $totalCustomers = Customer::where('status', 'active')->count();

            // 2. Số đơn hàng đã vận chuyển thành công
            $completedOrders = Order::where('order_status', 'completed')->count();

            // 3. Tổng doanh thu từ các đơn hàng đã hoàn thành
            $totalRevenue = DB::table('order_details')
                ->join('orders', 'orders.order_id', '=', 'order_details.order_id')
                ->where('orders.order_status', 'completed')
                ->sum(DB::raw('order_details.sold_price * order_details.sold_quantity'));

            // 4. Số đơn hàng đang chờ xác nhận
            $pendingOrders = Order::where('order_status', 'pending')->count();

            // 5. Doanh thu 7 ngày gần nhất
            $revenueData = DB::table('order_details')
                ->join('orders', 'orders.order_id', '=', 'order_details.order_id')
                ->where('orders.order_status', 'completed')
                ->whereBetween('orders.order_date', [
                    Carbon::now()->subDays(6)->startOfDay(),
                    Carbon::now()->endOfDay()
                ])
                ->select(
                    DB::raw('DATE(orders.order_date) as date'),
                    DB::raw('SUM(order_details.sold_price * order_details.sold_quantity) as daily_revenue')
                )
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get()
                ->keyBy('date')
                ->map(function ($item) {
                    return $item->daily_revenue;
                })
                ->toArray();

            // Tạo mảng đủ 7 ngày với revenue = 0 nếu không có doanh thu
            $last7Days = [];
            $dailyRevenue = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $last7Days[] = $date->format('d/m');

                // Tính doanh thu theo ngày
                $revenue = DB::table('order_details')
                    ->join('orders', 'orders.order_id', '=', 'order_details.order_id')
                    ->whereDate('orders.order_date', $date->format('Y-m-d'))
                    ->where('orders.order_status', 'completed')
                    ->sum(DB::raw('order_details.sold_price * order_details.sold_quantity'));

                $dailyRevenue[] = (int)$revenue; // Convert to integer
            }

            // 6. Lấy top sản phẩm bán chạy
            $topSellingProducts = DB::table('order_details')
                ->join('products', 'products.product_id', '=', 'order_details.product_id')
                ->join('category_product', 'products.product_id', '=', 'category_product.product_id')
                ->join('categories', 'categories.category_id', '=', 'category_product.category_id')
                ->join('orders', 'orders.order_id', '=', 'order_details.order_id')
                ->join('image_product', function ($join) {
                    $join->on('products.product_id', '=', 'image_product.product_id')
                        ->where('image_product.image_role', '=', 'main');
                })
                ->join('images', 'images.image_id', '=', 'image_product.image_id')
                ->where('orders.order_status', 'completed')
                ->where('products.status', 'active')
                ->select(
                    'products.product_id',
                    'products.product_name',
                    'images.image_url',
                    'categories.category_name',
                    'products.price',
                    DB::raw('SUM(order_details.sold_quantity) as total_sold'),
                    DB::raw('SUM(order_details.sold_quantity * order_details.sold_price) as total_revenue')
                )
                ->groupBy(
                    'products.product_id',
                    'products.product_name',
                    'images.image_url',
                    'categories.category_name',
                    'products.price'
                )
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get();

            // Debug log
            Log::info('Top Selling Products Query Result:', [
                'count' => $topSellingProducts->count(),
                'products' => $topSellingProducts->toArray()
            ]);

            return view('management.dashboard', compact(
                'totalCustomers',
                'completedOrders',
                'totalRevenue',
                'pendingOrders',
                'last7Days',
                'dailyRevenue',
                'topSellingProducts'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu dashboard');
        }
    }
}
