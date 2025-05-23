<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::withCount('products')->paginate(10);
        return view('management.image_mana.index', compact('images'));
    }

    public function create()
    {
        return view('management.image_mana.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'array|max:4',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:2048'
        ]);

        try {
            $successCount = 0;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    if ($imageFile) {
                        $path = $imageFile->store('images', 'public');

                        Image::create([
                            'image_url' => $path
                        ]);

                        $successCount++;
                    }
                }
            }

            if ($successCount > 0) {
                return redirect()
                    ->route('admin.image')
                    ->with('success', "Đã tải lên thành công $successCount ảnh.");
            }

            return back()
                ->withInput()
                ->with('error', 'Vui lòng chọn ít nhất một ảnh để tải lên.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tải lên ảnh: ' . $e->getMessage());
        }
    }

    public function edit(Image $image)
    {
        return view('management.image_mana.edit', compact('image'));
    }

    public function update(Request $request, Image $image)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048'
        ]);

        try {
            if ($request->hasFile('image')) {
                // Delete old image
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }

                // Store new image
                $path = $request->file('image')->store('images', 'public');

                $image->update([
                    'image_url' => $path
                ]);

                return redirect()
                    ->route('admin.image')
                    ->with('success', 'Hình ảnh đã được cập nhật thành công.');
            }

            return back()
                ->withInput()
                ->with('error', 'Vui lòng chọn file hình ảnh.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật hình ảnh.');
        }
    }

    public function destroy(Image $image)
    {
        try {
            // Check if image is being used by any products
            if ($image->products()->exists()) {
                return back()->with('error', 'Không thể xóa hình ảnh này vì đang được sử dụng bởi sản phẩm.');
            }

            // Delete image file from storage
            if (Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }

            // Delete database record
            $image->delete();

            return redirect()
                ->route('admin.image')
                ->with('success', 'Hình ảnh đã được xóa thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa hình ảnh.');
        }
    }
}
