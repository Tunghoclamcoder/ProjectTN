<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::paginate(8);
        return view('management.size_mana.index', compact('sizes'));
    }

    public function create()
    {
        return view('management.size_mana.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255|unique:sizes,size_name'
        ]);

        Size::create($validated);

        return redirect()
            ->route('admin.size')
            ->with('success', 'Thêm size mới thành công!');
    }

    public function edit(Size $size)
    {
        return view('management.size_mana.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255|unique:sizes,size_name,' . $size->size_id . ',size_id'
        ]);

        $size->update($validated);

        return redirect()
            ->route('admin.size')
            ->with('success', 'Cập nhật size thành công!');
    }

    public function destroy(Size $size)
    {
        try {
            $sizeName = $size->size_name;
            $size->delete();

            return redirect()
                ->route('admin.size')
                ->with('success', "Đã xóa thành công size '$sizeName'");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.size')
                ->with('error', 'Không thể xóa size này. Có thể có sản phẩm đang sử dụng!');
        }
    }
}
