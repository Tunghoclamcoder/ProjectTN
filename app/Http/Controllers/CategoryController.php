<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');
            Log::info('Category search query:', ['query' => $query]);

            $categories = Category::where('category_name', 'LIKE', "%{$query}%")
                ->orWhere('category_id', 'LIKE', "%{$query}%")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Category search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm'
            ], 500);
        }
    }

    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('management.category_mana.index', compact('categories'));
    }

    public function create()
    {
        return view('management.category_mana.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:100|unique:categories'
        ]);

        try {
            Category::create($validated);
            return redirect()
                ->route('admin.category')
                ->with('success', 'Danh mục đã được tạo thành công.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo danh mục.');
        }
    }

    public function edit(Category $category)
    {
        return view('management.category_mana.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->category_id . ',category_id',
        ]);

        try {
            $category->update($validated);
            return redirect()
                ->route('admin.category')
                ->with('success', 'Danh mục đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật danh mục.');
        }
    }

    public function destroy(Category $category)
    {
        try {
            if ($category->products()->exists()) {
                return back()->with('error', 'Không thể xóa danh mục này vì đang có sản phẩm liên kết.');
            }

            $category->delete();
            return redirect()
                ->route('admin.category')
                ->with('success', 'Danh mục đã được xóa thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa danh mục.');
        }
    }

    public function categoryList()
    {
        $categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        return view('Customer.category.category_list', compact('categories'));
    }

    /**
     * Hiển thị sản phẩm trong một danh mục cụ thể
     */
    public function showCategoryProducts(Request $request, $categoryId)
    {
        // Lấy category và eager load products (active, còn hàng, kèm brand, images)
        $category = Category::with(['products' => function ($query) {
            $query->where('status', 'active')
                ->where('quantity', '>', 0)
                ->with(['brand', 'images']);
        }])->findOrFail($categoryId);

        // Query sản phẩm thuộc category này
        $query = Product::whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.category_id', $categoryId);
        })
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->with(['brand', 'images']);

        // Xử lý sort nếu có
        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('product_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('product_name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('product_id', 'asc');
        }

        $products = $query->paginate(12);

        return view('Customer.category.filter_product', compact('category', 'products'));
    }
}
