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
                'relatedProducts'  // Add this line
            ));
        } catch (\Exception $e) {
            Log::error("Error showing product {$product_id}: " . $e->getMessage());
            return redirect()
                ->route('shop.home')
                ->with('error', 'Không tìm thấy sản phẩm này.');
        }
    }
}
