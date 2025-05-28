<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderBy('id', 'desc')->paginate(10);
        return view('management.voucher_mana.index', compact('vouchers'));
    }

    public function create()
    {
        return view('management.voucher_mana.create');
    }

    public function store(Request $request)
    {
        Log::info('Starting voucher creation process', ['input' => $request->all()]);

        try {
            DB::beginTransaction();

            // Validate basic rules first
            $validated = $request->validate([
                'code' => 'required|unique:vouchers,code',
                'discount_amount' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|between:0,100',
                'start_date' => 'required|date',
                'expiry_date' => 'required|date|after:start_date',
                'minimum_purchase_amount' => 'nullable|numeric|min:0',
                'maximum_purchase_amount' => 'nullable|numeric|min:0|gt:minimum_purchase_amount',
                'max_usage_count' => 'nullable|integer|min:1',
                'status' => 'required|boolean'
            ]);

            // Validate discount logic
            if ($request->filled('discount_amount') && $request->filled('discount_percentage')) {
                throw new \Illuminate\Validation\ValidationException(validator([], [], [
                    'discount' => 'Vui lòng chỉ chọn một loại giảm giá (số tiền hoặc phần trăm)'
                ]));
            }

            if (!$request->filled('discount_amount') && !$request->filled('discount_percentage')) {
                throw new \Illuminate\Validation\ValidationException(validator([], [], [
                    'discount' => 'Vui lòng chọn một loại giảm giá'
                ]));
            }

            // Create voucher data
            $voucherData = array_merge($validated, ['usage_count' => 0]);

            // Remove empty discount field
            if ($request->filled('discount_amount')) {
                unset($voucherData['discount_percentage']);
            } else {
                unset($voucherData['discount_amount']);
            }

            Log::info('Creating voucher with data', ['voucher_data' => $voucherData]);

            $voucher = Voucher::create($voucherData);

            DB::commit();

            return redirect()
                ->route('admin.voucher')
                ->with('success', 'Voucher đã được tạo thành công.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error', ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating voucher', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->with('error', 'Có lỗi xảy ra khi tạo voucher: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Voucher $voucher)
    {
        return view('management.voucher_mana.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|unique:vouchers,code,' . $voucher->id,
            'discount_amount' => 'required_without:discount_percentage|nullable|numeric|min:0',
            'discount_percentage' => 'required_without:discount_amount|nullable|numeric|between:0,100',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'minimum_purchase_amount' => 'nullable|numeric|min:0',
            'maximum_purchase_amount' => 'nullable|numeric|min:0|gt:minimum_purchase_amount',
            'max_usage_count' => 'nullable|integer|min:1',
            'status' => 'required|boolean'
        ]);

        try {
            $voucher->update($request->all());
            return redirect()->route('admin.voucher')
                ->with('success', 'Voucher đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật voucher.');
        }
    }

    public function destroy(Voucher $voucher)
    {
        try {
            $voucher->delete();
            return redirect()->route('admin.vouchers.index')
                ->with('success', 'Voucher đã được xóa thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xóa voucher.');
        }
    }

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:vouchers,code',
            'purchase_amount' => 'required|numeric|min:0'
        ]);

        try {
            $voucher = Voucher::where('code', $request->code)->first();

            if (!$voucher->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher không hợp lệ hoặc đã hết hạn'
                ], 400);
            }

            $discount = $voucher->calculateDiscount($request->purchase_amount);

            if ($discount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng voucher'
                ], 400);
            }

            // Increment usage count if voucher is valid and applicable
            $voucher->incrementUsage();

            return response()->json([
                'success' => true,
                'discount' => $discount,
                'message' => 'Voucher đã được áp dụng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng voucher'
            ], 500);
        }
    }

    public function toggleStatus(Voucher $voucher)
    {
        try {
            DB::beginTransaction();

            Log::info('Toggling voucher status', [
                'voucher_id' => $voucher->id,
                'old_status' => $voucher->status,
                'new_status' => !$voucher->status
            ]);

            $voucher->status = !$voucher->status;
            $voucher->save();

            DB::commit();

            $status = $voucher->status ? 'kích hoạt' : 'vô hiệu hóa';
            return redirect()->route('admin.voucher')
                ->with('success', "Voucher đã được $status thành công.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling voucher status', [
                'voucher_id' => $voucher->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Có lỗi xảy ra khi thay đổi trạng thái voucher.');
        }
    }
}
