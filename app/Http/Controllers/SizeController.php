<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::withCount('products')->paginate(10);
        return view('management.size_mana.index', compact('sizes'));
    }

    public function create()
    {
        return view('management.size_mana.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:100|unique:sizes'
        ]);

        try {
            size::create($validated);
            return redirect()
                ->route('admin.size')
                ->with('success', 'Danh mục đã được tạo thành công.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo danh mục.');
        }
    }

    public function edit(size $size)
    {
        return view('management.size_mana.edit', compact('size'));
    }

    public function update(Request $request, size $size)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255|unique:sizes,size_name,' . $size->size_id . ',size_id',
        ]);

        try {
            $size->update($validated);
            return redirect()
                ->route('admin.size')
                ->with('success', 'Danh mục đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật danh mục.');
        }
    }

    public function destroy(size $size)
    {
        try {
            if ($size->products()->exists()) {
                return back()->with('error', 'Không thể xóa danh mục này vì đang có sản phẩm liên kết.');
            }

            $size->delete();
            return redirect()
                ->route('admin.size')
                ->with('success', 'Danh mục đã được xóa thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa danh mục.');
        }
    }
}
