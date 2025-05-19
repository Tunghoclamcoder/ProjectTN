<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class EmployeeController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }
        return view('employee.login');
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
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('employee.login');
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
