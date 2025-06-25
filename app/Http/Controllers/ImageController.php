<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
                        $filename = time() . '_' . $imageFile->getClientOriginalName();

                        // 1️⃣ Lưu vào storage/app/public/images
                        $imageFile->storeAs('images', $filename, 'public');

                        // 2️⃣ Copy vào public/storage/images
                        $sourcePath = storage_path("app/public/images/{$filename}");
                        $destinationPath = public_path("storage/images/{$filename}");

                        if (!File::exists(public_path('storage/images'))) {
                            File::makeDirectory(public_path('storage/images'), 0755, true);
                        }

                        File::copy($sourcePath, $destinationPath);

                        // 3️⃣ Lưu đường dẫn chuẩn vào DB
                        Image::create([
                            'image_url' => "storage/images/{$filename}"
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
                $filename = time() . '_' . $request->file('image')->getClientOriginalName();

                // 1️⃣ Xóa ảnh cũ
                if ($image->image_url) {
                    $oldPathStorage = storage_path('app/public/' . str_replace('storage/', '', $image->image_url));
                    $oldPathPublic = public_path($image->image_url);

                    if (File::exists($oldPathStorage)) File::delete($oldPathStorage);
                    if (File::exists($oldPathPublic)) File::delete($oldPathPublic);
                }

                // 2️⃣ Lưu ảnh mới
                $request->file('image')->storeAs('images', $filename, 'public');

                // 3️⃣ Copy ảnh thật ra public
                $sourcePath = storage_path("app/public/images/{$filename}");
                $destinationPath = public_path("storage/images/{$filename}");

                if (!File::exists(public_path('storage/images'))) {
                    File::makeDirectory(public_path('storage/images'), 0755, true);
                }

                File::copy($sourcePath, $destinationPath);

                // 4️⃣ Cập nhật DB
                $image->update([
                    'image_url' => "storage/images/{$filename}"
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
                ->with('error', 'Có lỗi xảy ra khi cập nhật hình ảnh: ' . $e->getMessage());
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
