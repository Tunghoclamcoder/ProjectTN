<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class VoucherController extends Controller
{
    public function search(Request $request)
{
    try {
        $query = $request->get('query', '');
        $status = $request->get('status');

        Log::info('Search params:', ['query' => $query, 'status' => $status]);

        $vouchers = Voucher::query();

        // Search by voucher code
        if (!empty($query)) {
            $vouchers->where('code', 'LIKE', "%{$query}%");
        }

        // Filter by status
        if ($status !== '' && $status !== null) {
            $vouchers->where('status', $status === '1');
        }

        $results = $vouchers->get();

        return response()->json([
            'success' => true,
            'data' => $results
        ]);

    } catch (\Exception $e) {
        Log::error('Voucher search error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi tìm kiếm'
        ], 500);
    }
}

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
                'code' => 'required|unique:vouchers,code|regex:/^[A-Z0-9]+$/',
                'discount_amount' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|between:0,100',
                'start_date' => [
                    'required',
                    'date',
                    'after_or_equal:today'
                ],
                'expiry_date' => [
                    'required',
                    'date',
                    'after:start_date'
                ],
                'minimum_purchase_amount' => 'nullable|numeric|min:0',
                'maximum_purchase_amount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) use ($request) {
                        $minAmount = $request->input('minimum_purchase_amount');
                        if ($value && $minAmount && $value <= $minAmount) {
                            $fail('Số tiền giảm tối đa phải lớn hơn số tiền tối thiểu.');
                        }
                    }
                ],
                'max_usage_count' => 'nullable|integer|min:1',
                'status' => 'required|boolean'
            ]);

            // Validate discount type logic
            if (!$request->filled('discount_amount') && !$request->filled('discount_percentage')) {
                throw ValidationException::withMessages([
                    'discount' => 'Vui lòng chọn một loại giảm giá (số tiền hoặc phần trăm)'
                ]);
            }

            if ($request->filled('discount_amount') && $request->filled('discount_percentage')) {
                throw ValidationException::withMessages([
                    'discount' => 'Chỉ được chọn một loại giảm giá (số tiền hoặc phần trăm)'
                ]);
            }

            // Validate discount amount logic
            if ($request->filled('discount_amount') && $request->filled('minimum_purchase_amount')) {
                if ($request->discount_amount >= $request->minimum_purchase_amount) {
                    throw ValidationException::withMessages([
                        'discount_amount' => 'Số tiền giảm không được lớn hơn hoặc bằng số tiền tối thiểu của đơn hàng'
                    ]);
                }
            }

            // Create voucher data with default usage count
            $voucherData = array_merge($validated, [
                'usage_count' => 0,
                'code' => strtoupper($request->code)
            ]);

            // Remove empty discount field
            if ($request->filled('discount_amount')) {
                unset($voucherData['discount_percentage']);
            } else {
                unset($voucherData['discount_amount']);
            }

            Log::info('Creating voucher with data', ['voucher_data' => $voucherData]);

            $voucher = Voucher::create($voucherData);

            // Auto disable voucher if expiry date is passed
            if ($voucher->expiry_date < now()) {
                $voucher->update(['status' => false]);
            }

            DB::commit();

            return redirect()
                ->route('admin.voucher')
                ->with('success', 'Voucher đã được tạo thành công.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error', ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
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
            'voucher_code' => 'required|exists:vouchers,code'
        ]);

        try {
            $voucher = Voucher::where('code', $request->voucher_code)
                ->where('status', 1)
                ->first();

            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không hợp lệ'
                ]);
            }

            // Kiểm tra điều kiện voucher
            $total = session('cart_total', 0);
            if ($voucher->minimum_purchase_amount > $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng chưa đạt giá trị tối thiểu'
                ]);
            }

            // Tính toán giảm giá
            $discount = $voucher->discount_percentage
                ? ($total * $voucher->discount_percentage / 100)
                : $voucher->discount_amount;

            $newTotal = $total - $discount;

            // Lưu thông tin vào session
            session(['voucher_id' => $voucher->id]);
            session(['cart_total' => $newTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'new_total' => $newTotal,
                'voucher_id' => $voucher->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng mã giảm giá'
            ]);
        }
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|exists:vouchers,code'
        ]);

        $voucher = Voucher::where('code', $request->voucher_code)
            ->where('status', true)
            ->first();

        if (!$voucher) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn');
        }

        // Lấy tổng giá trị giỏ hàng
        $cartTotal = session('cart.total', 0);

        // Kiểm tra điều kiện áp dụng
        if ($voucher->minimum_purchase_amount && $cartTotal < $voucher->minimum_purchase_amount) {
            return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu để sử dụng mã giảm giá này');
        }

        // Lưu thông tin voucher vào session
        session([
            'voucher.id' => $voucher->id,
            'voucher.code' => $voucher->code,
            'voucher.discount_amount' => $voucher->discount_amount,
            'voucher.discount_percentage' => $voucher->discount_percentage
        ]);

        return back()->with('success', 'Áp dụng mã giảm giá thành công');
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
