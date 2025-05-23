<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
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
}
