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
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use Elastic\Elasticsearch\Client;
use App\Traits\Searchable;
use App\Models\Brand;
use App\Models\Category;

class DashboardController extends Controller
{
    use PreventBackHistory, Searchable;
    public function index()
    {
        $statusClasses = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'shipping' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        $latestOrders = Order::with(['customer'])
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

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

            $allFeedbacks = \App\Models\Feedback::with('customer')->orderByDesc('feedback_id')->get();

            // biểu đồ so sánh số lượng đơn hàng hoàn thành và hủy trong 7 ngày qua
            $orderChartLabels = [];
            $completedCounts = [];
            $canceledCounts = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $orderChartLabels[] = Carbon::now()->subDays($i)->format('d/m');
                $completedCounts[] = Order::whereDate('order_date', $date)->where('order_status', 'completed')->count();
                $canceledCounts[] = Order::whereDate('order_date', $date)->where('order_status', 'cancelled')->count();
            }

            $completedThisWeek = Order::where('order_status', 'completed')
                ->whereDate('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->count();
            $canceledThisWeek = Order::where('order_status', 'cancelled')
                ->whereDate('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->count();

            // Tuần trước (7 ngày liền kề trước đó)
            $completedLastWeek = Order::where('order_status', 'completed')
                ->whereBetween('order_date', [
                    Carbon::now()->subDays(13)->startOfDay(),
                    Carbon::now()->subDays(7)->endOfDay()
                ])->count();
            $canceledLastWeek = Order::where('order_status', 'cancelled')
                ->whereBetween('order_date', [
                    Carbon::now()->subDays(13)->startOfDay(),
                    Carbon::now()->subDays(7)->endOfDay()
                ])->count();

            // Tính % khuynh hướng
            $calcTrend = function ($now, $last) {
                if ($last == 0) return $now > 0 ? 100 : 0;
                return round((($now - $last) / $last) * 100, 2);
            };
            $completedTrend = $calcTrend($completedThisWeek, $completedLastWeek);
            $canceledTrend = $calcTrend($canceledThisWeek, $canceledLastWeek);

            return view('management.dashboard', compact(
                'totalCustomers',
                'completedOrders',
                'totalRevenue',
                'pendingOrders',
                'last7Days',
                'dailyRevenue',
                'topSellingProducts',
                'latestOrders',
                'statusClasses',
                'allFeedbacks',
                'orderChartLabels',
                'completedCounts',
                'canceledCounts',
                'completedThisWeek',
                'canceledThisWeek',
                'completedTrend',
                'canceledTrend',
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu dashboard');
        }
    }

    protected Client $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(Request $request)
    {
        try {
            $query = trim($request->get('query'));
            Log::info('Search query: ' . $query);

            if (empty($query)) {
                return redirect()->route('admin.dashboard');
            }

            // Tìm kiếm sản phẩm
            $products = Product::where(function ($q) use ($query) {
                $q->whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereHas('brand', function ($q) use ($query) {
                        $q->whereRaw('LOWER(brand_name) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->orWhereHas('categories', function ($q) use ($query) {
                        $q->whereRaw('LOWER(category_name) LIKE ?', ['%' . strtolower($query) . '%']);
                    });
            })
                ->with(['brand', 'categories', 'images'])
                ->get();

            Log::info('Found products: ' . $products->count());

            if ($request->ajax()) {
                return response()->json([
                    'products' => $products->map(function ($product) {
                        return [
                            'id' => $product->product_id,
                            'name' => $product->product_name,
                            'category' => $product->getPrimaryCategory()?->category_name ?? 'N/A',
                            'price' => number_format($product->price) . ' VNĐ',
                            'image' => $product->getMainImage()?->image_url
                                ? asset('storage/' . $product->getMainImage()->image_url)
                                : asset('images/no-image.png'),
                            'url' => route('admin.product.edit', $product->product_id)
                        ];
                    })
                ]);
            }

            return view('management.search-result-admin', compact('products', 'query'));
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json(['error' => $e->getMessage()], 500)
                : back()->with('error', 'Có lỗi xảy ra trong quá trình tìm kiếm: ' . $e->getMessage());
        }
    }

    public function searchSuggestions(Request $request)
    {
        try {
            $query = trim($request->get('query'));

            if (strlen($query) < 2) {
                return response()->json(['products' => []]);
            }

            $products = Product::where(function ($q) use ($query) {
                $q->whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereHas('brand', function ($q) use ($query) {
                        $q->whereRaw('LOWER(brand_name) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    ->orWhereHas('categories', function ($q) use ($query) {
                        $q->whereRaw('LOWER(category_name) LIKE ?', ['%' . strtolower($query) . '%']);
                    });
            })
                ->with(['brand', 'categories', 'images'])
                ->take(5)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->product_id,
                        'name' => $product->product_name,
                        'category' => $product->getPrimaryCategory()?->category_name ?? 'N/A',
                        'price' => number_format($product->price) . ' VNĐ',
                        'image_url' => $product->getMainImage()?->image_url
                            ? asset( $product->getMainImage()->image_url)
                            : asset('images/no-image.png'),
                        'url' => route('admin.product.edit', $product->product_id)
                    ];
                });

            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            Log::error('Search suggestions error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function charts()
    {
        $latestOrders = Order::with(['customer'])
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

        try {
            // Kiểm tra auth
            if (!Auth::guard('owner')->check() && !Auth::guard('employee')->check()) {
                return redirect()->route('admin.login')
                    ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }

            Log::info('Starting dashboard data collection');

            // 2. Số đơn hàng đã vận chuyển thành công
            $completedOrders = Order::where('order_status', 'completed')->count();

            // 3. Tổng doanh thu từ các đơn hàng đã hoàn thành
            $totalRevenue = DB::table('order_details')
                ->join('orders', 'orders.order_id', '=', 'order_details.order_id')
                ->where('orders.order_status', 'completed')
                ->sum(DB::raw('order_details.sold_price * order_details.sold_quantity'));

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

                $dailyRevenue[] = (int)$revenue;
            }

            // biểu đồ so sánh số lượng đơn hàng hoàn thành và hủy trong 7 ngày qua
            $orderChartLabels = [];
            $completedCounts = [];
            $canceledCounts = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $orderChartLabels[] = Carbon::now()->subDays($i)->format('d/m');
                $completedCounts[] = Order::whereDate('order_date', $date)->where('order_status', 'completed')->count();
                $canceledCounts[] = Order::whereDate('order_date', $date)->where('order_status', 'cancelled')->count();
            }

            $completedThisWeek = Order::where('order_status', 'completed')
                ->whereDate('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->count();
            $canceledThisWeek = Order::where('order_status', 'cancelled')
                ->whereDate('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->count();

            // Tuần trước (7 ngày liền kề trước đó)
            $completedLastWeek = Order::where('order_status', 'completed')
                ->whereBetween('order_date', [
                    Carbon::now()->subDays(13)->startOfDay(),
                    Carbon::now()->subDays(7)->endOfDay()
                ])->count();
            $canceledLastWeek = Order::where('order_status', 'cancelled')
                ->whereBetween('order_date', [
                    Carbon::now()->subDays(13)->startOfDay(),
                    Carbon::now()->subDays(7)->endOfDay()
                ])->count();

            // Tính % khuynh hướng
            $calcTrend = function ($now, $last) {
                if ($last == 0) return $now > 0 ? 100 : 0;
                return round((($now - $last) / $last) * 100, 2);
            };
            $completedTrend = $calcTrend($completedThisWeek, $completedLastWeek);
            $canceledTrend = $calcTrend($canceledThisWeek, $canceledLastWeek);

            // Biểu đồ hình đường đi uốn lượn show lượng voucher đã sử dụng trong 7 ngày qua
            $voucherChartLabels = [];
            $voucherUsageCounts = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $voucherChartLabels[] = Carbon::now()->subDays($i)->format('d/m');
                // Đếm số đơn có sử dụng voucher trong ngày đó
                $voucherUsageCounts[] = Order::whereDate('order_date', $date)
                    ->whereNotNull('voucher_id') // hoặc điều kiện phù hợp với hệ thống của bạn
                    ->count();
            }

            // Biểu đồ hình cột ngang show số lượng sản phẩm bán đc theo danh mục
            $categorySales = Category::with(['products.orderDetails' => function ($q) {
                $q->whereHas('order', function ($query) {
                    $query->where('order_status', 'completed');
                });
            }])->get()->map(function ($cat) {
                $total = 0;
                foreach ($cat->products as $product) {
                    $total += $product->orderDetails->sum('sold_quantity');
                }
                return [
                    'name' => $cat->category_name,
                    'total' => $total
                ];
            });
            $categoryNames = $categorySales->pluck('name')->toArray();
            $categoryTotals = $categorySales->pluck('total')->toArray();

            // Biểu đồ hình cột dọc show số lượng sản phẩm bán đc theo thương hiệu
            $brandSales = Brand::with(['products.orderDetails' => function ($q) {
                $q->whereHas('order', function ($query) {
                    $query->where('order_status', 'completed');
                });
            }])->get()->map(function ($brand) {
                $total = 0;
                foreach ($brand->products as $product) {
                    $total += $product->orderDetails->sum('sold_quantity');
                }
                return [
                    'name' => $brand->brand_name,
                    'total' => $total
                ];
            });
            $brandNames = $brandSales->pluck('name')->toArray();
            $brandTotals = $brandSales->pluck('total')->toArray();

            return view('management.charts', compact(
                'completedOrders',
                'totalRevenue',
                'last7Days',
                'dailyRevenue',
                'latestOrders',
                'orderChartLabels',
                'completedCounts',
                'canceledCounts',
                'completedThisWeek',
                'canceledThisWeek',
                'completedTrend',
                'canceledTrend',
                'voucherChartLabels',
                'voucherUsageCounts',
                'categoryNames',
                'categoryTotals',
                'brandNames',
                'brandTotals',
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải dữ liệu dashboard');
        }
    }

    public function getBrandSalesStats(): JsonResponse
    {
        try {
            Log::info('Starting getBrandSalesStats...');

            // First get total orders
            $totalOrders = DB::table('order_details')->count();
            Log::info('Total orders found: ' . $totalOrders);

            if ($totalOrders === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu đơn hàng'
                ]);
            }

            $brandStats = DB::table('brands as b')
                ->join('products as p', 'b.brand_id', '=', 'p.brand_id')
                ->join('order_details as od', 'p.product_id', '=', 'od.product_id')
                ->select(
                    'b.brand_name',
                    DB::raw('COUNT(od.product_id) as total_sales'),
                    DB::raw("ROUND((COUNT(od.product_id) * 100.0 / $totalOrders), 2) as percentage")
                )
                ->groupBy('b.brand_id', 'b.brand_name')
                ->orderByDesc('total_sales')
                ->limit(5)
                ->get();

            Log::info('Brand stats retrieved:', ['data' => $brandStats]);

            return response()->json([
                'success' => true,
                'data' => $brandStats
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getBrandSalesStats: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}
