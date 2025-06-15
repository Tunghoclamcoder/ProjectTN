<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Searchable
{
    public function search(Request $request)
    {
        $query = $request->get('query');
        $isAdmin = $request->route()->getName() === 'admin.search';

        if (empty($query)) {
            return $isAdmin
                ? response()->json([])
                : redirect()->route('shop.home');
        }

        try {
            $productsQuery = Product::searchElastic($query)
                ->with(['brand', 'categories', 'images']);

            // Only show active products for customers
            if (!$isAdmin) {
                $productsQuery->where('status', 'active');
            }

            $products = $productsQuery->take($isAdmin ? 5 : 12)->get();

            if ($request->ajax()) {
                return response()->json([
                    'products' => $products->map(function ($product) use ($isAdmin) {
                        return [
                            'id' => $product->product_id,
                            'name' => $product->product_name,
                            'price' => number_format($product->price) . ' VNĐ',
                            'category' => $product->getPrimaryCategory()?->category_name ?? 'N/A',
                            'image' => $product->getMainImage()?->image_url
                                ? asset('storage/' . $product->getMainImage()->image_url)
                                : asset('images/no-image.png'),
                            'url' => $isAdmin
                                ? route('admin.product.edit', $product->product_id)
                                : route('shop.product.detail', $product->product_id)
                        ];
                    })
                ]);
            }

            $viewPath = $isAdmin
                ? 'management.search_results'
                : 'Customer.filter.search_result';

            return view($viewPath, compact('products', 'query'));

        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json(['error' => 'Có lỗi xảy ra trong quá trình tìm kiếm'], 500)
                : back()->with('error', 'Có lỗi xảy ra trong quá trình tìm kiếm');
        }
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');
        $isAdmin = $request->route()->getName() === 'admin.search.suggestions';

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $productsQuery = Product::searchElastic($query);

            if (!$isAdmin) {
                $productsQuery->where('status', 'active');
            }

            $suggestions = $productsQuery
                ->take(5)
                ->get()
                ->map(function ($product) {
                    return [
                        'name' => $product->product_name,
                        'category' => $product->getPrimaryCategory()?->category_name ?? 'N/A'
                    ];
                });

            return response()->json($suggestions);

        } catch (\Exception $e) {
            Log::error('Search suggestions error: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
