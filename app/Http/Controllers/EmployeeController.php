<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function search(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = $request->get('query', '');

                $employees = Employee::where(function ($q) use ($query) {
                    $q->where('employee_name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%")
                        ->orWhere('phone_number', 'LIKE', "%{$query}%");
                })->get();

                return response()->json([
                    'success' => true,
                    'data' => $employees
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tìm kiếm'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request'
        ], 400);
    }

    // Hàm chuyển chuỗi có dấu thành không dấu
    private function stripVnAccent($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    public function showLoginForm()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('management.dashboard');
        }
        return view('management.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('employee')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('employee.dashboard');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput();
    }

    public function dashboard()
    {
        if (!Auth::guard('employee')->check()) {
            return redirect()->route('employee.login');
        }
        return view('employee.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Đăng xuất thành công!');
    }
    // CRUD nhân viên
    public function index()
    {
        try {
            $employees = Employee::select(
                'employee_id',
                'employee_name',
                'email',
                'phone_number',
                'status',
                'owner_id'
            )
                ->where('owner_id', Auth::guard('owner')->id())
                ->orderBy('employee_name', 'asc')
                ->paginate(10);

            return view('Owner.employee_mana.index', compact('employees'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải danh sách nhân viên: ' . $e->getMessage());
        }
    }
    public function create()
    {
        return view('Owner.employee_mana.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_name' => 'required|string|max:100',
                'email' => 'required|email|unique:employee,email',
                'phone_number' => 'nullable|string|max:20',
                'password' => 'required|string|min:6|confirmed',
                'status' => 'required|in:active,inactive',
            ]);

            // Thêm owner_id của chủ cửa hàng đang đăng nhập
            $validated['owner_id'] = Auth::guard('owner')->id();

            // Mã hóa mật khẩu
            $validated['password'] = Hash::make($validated['password']);

            // Tạo nhân viên mới
            Employee::create($validated);

            return redirect()
                ->route('admin.employee');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm nhân viên: ' . $e->getMessage());
        }
    }

    public function edit(Employee $employee)
    {
        // Kiểm tra xem nhân viên có thuộc owner hiện tại không
        if ($employee->owner_id !== Auth::guard('owner')->id()) {
            return redirect()->route('admin.employee')
                ->with('error', 'Bạn không có quyền chỉnh sửa nhân viên này');
        }

        return view('Owner.employee_mana.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'employee_name' => 'required|string|max:100',
                'email' => 'required|email|unique:employee,email,' . $employee->employee_id . ',employee_id',
                'phone_number' => 'nullable|string|max:20',
                'status' => 'required|in:active,inactive',
            ]);

            // Thêm owner_id vào dữ liệu cập nhật
            $validated['owner_id'] = Auth::guard('owner')->id();

            // Cập nhật thông tin nhân viên
            $employee->update($validated);

            return redirect()
                ->route('admin.employee')
                ->with('success', 'Cập nhật thông tin nhân viên thành công');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật thông tin nhân viên');
        }
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employee')->with('success', 'Xóa nhân viên thành công');
    }
}
