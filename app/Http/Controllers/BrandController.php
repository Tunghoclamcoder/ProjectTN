<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');

            $brands = Brand::where('brand_name', 'LIKE', "%{$query}%")
                ->select('brand_id', 'brand_name', 'brand_image', 'description')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $brands
            ]);
        } catch (\Exception $e) {
            Log::error('Brand search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm'
            ], 500);
        }
    }

    public function index()
    {
        $brands = Brand::paginate(10);
        return view('management.brand_mana.index', compact('brands'));
    }

    public function create()
    {
        return view('management.brand_mana.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($request->hasFile('brand_image')) {
            // Lưu ảnh vào storage/app/public/brands
            $imagePath = $request->file('brand_image')->store('brands', 'public');
            $validated['brand_image'] = $imagePath;

            // Log để debug
            Log::info('Brand Image Stored:', [
                'path' => $imagePath,
                'full_path' => Storage::disk('public')->path($imagePath)
            ]);
        }

        Brand::create($validated);
        return redirect()->route('admin.brand')->with('success', 'Thêm thương hiệu thành công');
    }

    public function edit($brand_id)
    {
        $brand = Brand::findOrFail($brand_id);
        return view('management.brand_mana.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($request->hasFile('brand_image')) {
            // Delete old image
            if ($brand->brand_image) {
                Storage::delete('public/' . $brand->brand_image);
            }

            $imagePath = $request->file('brand_image')->store('public/brands');
            // Remove 'public/' from path when saving to database
            $validated['brand_image'] = str_replace('public/', '', $imagePath);
        }

        $brand->update($validated);
        return redirect()->route('admin.brand')->with('success', 'Cập nhật thương hiệu thành công');
    }
    public function destroy(Brand $brand)
    {
        try {
            $brandId = $brand->brand_id;
            $brandName = $brand->brand_name;

            if ($brand->brand_image && Storage::disk('public')->exists($brand->brand_image)) {
                Storage::disk('public')->delete($brand->brand_image);
            }

            $brand->delete();

            return redirect()
                ->route('admin.brand')
                ->with('success', "Đã xóa thành công thương hiệu '$brandName' (ID: $brandId)");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.brand')
                ->with('error', "Không thể xóa thương hiệu. Lỗi: " . $e->getMessage());
        }
    }

    public function brandList()
    {
        $brands = Brand::withCount('products')
            ->orderBy('brand_name')
            ->get();

        return view('Customer.brand.brand_list', compact(var_name: 'brands'));
    }

    /**
     * Hiển thị sản phẩm trong một thương hiệu cụ thể
     */
    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        $products = Product::where('brand_id', $id)
            ->with(['brand', 'images'])
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->paginate(12);

        return view('Customer.brand.brand_product', compact('brand', 'products'));
    }
}
