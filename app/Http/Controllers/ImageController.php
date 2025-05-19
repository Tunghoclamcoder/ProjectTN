<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);

            $imageModel = Image::create([
                'image_url' => 'images/products/' . $imageName
            ]);

            return response()->json([
                'success' => true,
                'image_id' => $imageModel->image_id,
                'image_url' => $imageModel->image_url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải ảnh lên'
            ], 500);
        }
    }

    public function destroy(Image $image)
    {
        try {
            if (file_exists(public_path($image->image_url))) {
                unlink(public_path($image->image_url));
            }

            $image->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa ảnh'
            ], 500);
        }
    }
}
