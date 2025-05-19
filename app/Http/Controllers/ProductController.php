<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Material;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use App\Models\ImageProduct;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'material', 'size', 'categories', 'images'])->get();
        return view('management.product_mana.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::all();
        $materials = Material::all();
        $sizes = Size::all();
        $categories = Category::all();
        return view('management.product_mana.create', compact('brands', 'materials', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:150',
            'status' => 'boolean',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,brand_id',
            'material_id' => 'required|exists:materials,material_id',
            'size_id' => 'required|exists:sizes,size_id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,category_id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create product
            $product = Product::create([
                'product_name' => $request->product_name,
                'status' => $request->status,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'discount' => $request->discount,
                'description' => $request->description,
                'brand_id' => $request->brand_id,
                'material_id' => $request->material_id,
                'size_id' => $request->size_id,
                'NumberOfCategory' => count($request->categories),
                'NumberOfImage' => count($request->file('images'))
            ]);

            // Attach categories
            $product->categories()->attach($request->categories);

            // Store images
            if($request->hasFile('images')) {
                foreach($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images/products'), $imageName);

                    ImageProduct::create([
                        'product_id' => $product->product_id,
                        'image_path' => 'images/products/' . $imageName
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.product')
                ->with('success', 'Thêm sản phẩm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm sản phẩm: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        $brands = Brand::all();
        $materials = Material::all();
        $categories = Category::all();
        $product->load(['categories', 'images']);

        return view('Owner.product_management.edit', compact('product', 'brands', 'materials', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required|string|max:150',
            'status' => 'boolean',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,brand_id',
            'material_id' => 'required|exists:materials,material_id',
            'size_id' => 'required|exists:sizes,size_id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,category_id',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $product->update([
                'product_name' => $request->product_name,
                'status' => $request->status,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'discount' => $request->discount,
                'description' => $request->description,
                'brand_id' => $request->brand_id,
                'material_id' => $request->material_id,
                'size_id' => $request->size_id,
                'NumberOfCategory' => count($request->categories),
                'NumberOfImage' => $product->images()->count() + ($request->hasFile('new_images') ? count($request->file('new_images')) : 0)
            ]);

            // Update categories
            $product->categories()->sync($request->categories);

            // Add new images if any
            if($request->hasFile('new_images')) {
                foreach($request->file('new_images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images/products'), $imageName);

                    ImageProduct::create([
                        'product_id' => $product->product_id,
                        'image_path' => 'images/products/' . $imageName
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.product')
                ->with('success', 'Cập nhật sản phẩm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete all related images from storage
            foreach($product->images as $image) {
                if(file_exists(public_path($image->image_path))) {
                    unlink(public_path($image->image_path));
                }
            }

            // Delete the product (will cascade delete categories and images)
            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.product')
                ->with('success', 'Xóa sản phẩm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi xóa sản phẩm');
        }
    }
}
