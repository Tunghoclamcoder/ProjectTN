<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with(['images' => function ($query) {
            $query->wherePivot('image_role', 'main');
        }])
            ->where('status', true)
            ->orderBy('product_id', 'desc')
            ->paginate(12);

        return view('Customer.home', compact('products'));
    }

    public function show($product_id)
    {
        try {
            $product = Product::with([
                'brand',
                'material',
                'categories',
                'size',
                'images' => function ($query) {
                    $query->orderByPivot('image_order');
                }
            ])->findOrFail($product_id);

            // Get main image and sub images
            $mainImage = $product->images
                ->where('pivot.image_role', 'main')
                ->first();

            $subImages = $product->images
                ->where('pivot.image_role', 'sub')
                ->sortBy('pivot.image_order');

                // Get related products (same brand, different product)
                $relatedProducts = Product::where('brand_id', $product->brand_id)
                ->where('product_id', '!=', $product->product_id)
                ->where('status', true)
                ->limit(4)
                ->get();

            return view('Customer.product-details', compact(
                'product',
                'mainImage',
                'subImages',
                'relatedProducts'
            ));
        } catch (\Exception $e) {
            Log::error("Error showing product {$product_id}: " . $e->getMessage());
            return redirect()
                ->route('shop.home')
                ->with('error', 'Không tìm thấy sản phẩm này.');
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $perPage = 12;

        if (empty($query)) {
            return redirect()->route('shop.home');
        }

        $products = Product::with(['brand', 'images', 'categories'])
            ->where(function ($q) use ($query) {
                $q->where('product_name', 'LIKE', "%{$query}%")  // Thay đổi từ 'name' thành 'product_name'
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhereHas('brand', function ($brandQuery) use ($query) {
                        $brandQuery->where('brand_name', 'LIKE', "%{$query}%");  // Thay đổi từ 'name' thành 'brand_name'
                    })
                    ->orWhereHas('categories', function ($categoryQuery) use ($query) {
                        $categoryQuery->where('category_name', 'LIKE', "%{$query}%");  // Thay đổi từ 'name' thành 'category_name'
                    });
            })
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->paginate($perPage);

        return view('Customer.filter.search_result', compact('products', 'query'));
    }

    /**
     * Get search suggestions (AJAX)
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Product::where('product_name', 'LIKE', "%{$query}%")  // Thay đổi từ 'name' thành 'product_name'
            ->where('status', 'active')
            ->limit(5)
            ->pluck('product_name')
            ->toArray();

        return response()->json($suggestions);
    }
}
