<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        return view('Owner.category_management.index', compact('categories'));
    }

    public function create()
    {
        return view('Owner.category_management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:categories'
        ]);

        try {
            Category::create($request->only('category_name'));

            return redirect()
                ->route('admin.category')
                ->with('success', 'Thêm danh mục thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm danh mục');
        }
    }

    public function edit(Category $category)
    {
        return view('Owner.category_management.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:categories,category_name,' . $category->category_id . ',category_id'
        ]);

        try {
            $category->update($request->only('category_name'));

            return redirect()
                ->route('admin.category')
                ->with('success', 'Cập nhật danh mục thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật danh mục');
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return redirect()
                ->route('admin.category')
                ->with('success', 'Xóa danh mục thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa danh mục');
        }
    }
}
