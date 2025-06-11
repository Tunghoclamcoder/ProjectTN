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

        if (empty($query)) {
            return redirect()->route('shop.home');
        }

        try {
            $products = Product::with(['brand', 'images', 'categories'])
                ->where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('product_name', 'LIKE', "%{$query}%")
                        ->orWhereHas('brand', function ($q) use ($query) {
                            $q->where('brand_name', 'LIKE', "%{$query}%");
                        });
                })
                ->paginate(12);

            return view('Customer.filter.search_result', compact('products', 'query'));
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra trong quá trình tìm kiếm');
        }
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $suggestions = Product::where('status', 'active')
                ->where('product_name', 'LIKE', "%{$query}%")
                ->limit(5)
                ->pluck('product_name');

            return response()->json($suggestions);
        } catch (\Exception $e) {
            Log::error('Search suggestions error: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
