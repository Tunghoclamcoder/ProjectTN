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

        // Convert query to lowercase for case-insensitive comparison
        $searchTerm = mb_strtolower(trim($query));

        $products = Product::with(['brand', 'images', 'categories'])
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(product_name) LIKE ?', ["%{$searchTerm}%"]) // Chỉ tìm trong tên sản phẩm
                    ->orWhere(function ($subQ) use ($searchTerm) {
                        // Tìm chính xác từng từ trong tên sản phẩm
                        $words = explode(' ', $searchTerm);
                        foreach ($words as $word) {
                            $subQ->whereRaw('LOWER(product_name) LIKE ?', ["%{$word}%"]);
                        }
                    });
            })
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->orderByRaw('CASE
            WHEN LOWER(product_name) LIKE ? THEN 1
            WHEN LOWER(product_name) LIKE ? THEN 2
            ELSE 3
        END', ["{$searchTerm}%", "%{$searchTerm}%"])
            ->paginate($perPage);

        return view('Customer.filter.search_result', compact('products', 'query'));
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Convert query to lowercase for case-insensitive comparison
        $searchTerm = mb_strtolower(trim($query));

        $suggestions = Product::whereRaw('LOWER(product_name) LIKE ?', ["%{$searchTerm}%"])
            ->where('status', 'active')
            ->orderByRaw('CASE
            WHEN LOWER(product_name) LIKE ? THEN 1
            WHEN LOWER(product_name) LIKE ? THEN 2
            ELSE 3
        END', ["{$searchTerm}%", "%{$searchTerm}%"])
            ->limit(5)
            ->pluck('product_name')
            ->toArray();

        return response()->json($suggestions);
    }
}
