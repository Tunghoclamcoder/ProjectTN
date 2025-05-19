<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::paginate(10);
        return view('management.voucher_mana.index', compact('vouchers'));
    }

    public function create()
    {
        return view('management.voucher_mana.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers'
        ]);

        try {
            Voucher::create($request->only('code'));

            return redirect()
                ->route('admin.voucher')
                ->with('success', 'Thêm voucher thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm voucher');
        }
    }

    public function edit(Voucher $voucher)
    {
        return view('management.voucher_mana.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->voucher_id . ',voucher_id'
        ]);

        try {
            $voucher->update($request->only('code'));

            return redirect()
                ->route('admin.voucher')
                ->with('success', 'Cập nhật voucher thành công');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật voucher');
        }
    }

    public function destroy(Voucher $voucher)
    {
        try {
            $voucher->delete();

            return redirect()
                ->route('admin.voucher')
                ->with('success', 'Xóa voucher thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa voucher');
        }
    }
}
