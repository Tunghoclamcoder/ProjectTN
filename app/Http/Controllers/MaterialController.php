<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::paginate(10);
        return view('management.material_mana.index', compact('materials'));
    }

    public function create()
    {
        return view('management.material_mana.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string|max:100|unique:materials'
        ]);

        try {
            Material::create([
                'material_name' => $request->material_name
            ]);

            return redirect()
                ->route('admin.material')
                ->with('success', 'Thêm chất liệu thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm chất liệu');
        }
    }

    public function edit(Material $material)
    {
        return view('management.material_mana.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'material_name' => 'required|string|max:100|unique:materials,material_name,' . $material->material_id . ',material_id'
        ]);

        try {
            $material->update([
                'material_name' => $request->material_name
            ]);

            return redirect()
                ->route('admin.material')
                ->with('success', 'Cập nhật chất liệu thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật chất liệu');
        }
    }

    public function destroy(Material $material)
    {
        try {
            $material->delete();
            return redirect()
                ->route('admin.material')
                ->with('success', 'Xóa chất liệu thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa chất liệu');
        }
    }
}
