<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Owner;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function show()
    {
        if (Auth::guard('owner')->check()) {
            $profile = Auth::guard('owner')->user();
            return view('management.profile.owner', compact('profile'));
        }

        if (Auth::guard('employee')->check()) {
            $profile = Auth::guard('employee')->user();
            return view('management.profile.employee', compact('profile'));
        }

        return redirect()->route('admin.dashboard')
            ->with('error', 'Không tìm thấy thông tin tài khoản');
    }

    public function update(Request $request)
    {
        try {
            if (Auth::guard('employee')->check()) {
                $employee = Employee::findOrFail(Auth::guard('employee')->id());

                $validated = $request->validate([
                    'employee_name' => 'required|string|max:100',
                    'email' => 'required|email|unique:employee,email,' . $employee->employee_id . ',employee_id',
                    'phone_number' => 'required|string|max:20'
                ]);

                $updated = $employee->update([
                    'employee_name' => $validated['employee_name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone_number']
                ]);

                Log::info('Employee update result:', ['success' => $updated]);

                if ($updated) {
                    return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
                }

                return redirect()->back()
                    ->with('error', 'Không thể cập nhật thông tin nhân viên!')
                    ->withInput();
            } elseif (Auth::guard('owner')->check()) {
                $owner = Owner::findOrFail(Auth::guard('owner')->id());

                $validated = $request->validate([
                    'owner_name' => 'required|string|max:100',
                    'email' => 'required|email|unique:owner,email,' . $owner->owner_id . ',owner_id',
                    'phone_number' => 'required|string|max:20'
                ]);

                $updated = $owner->update([
                    'owner_name' => $validated['owner_name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone_number']
                ]);

                Log::info('Owner update result:', ['success' => $updated]);

                if ($updated) {
                    return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
                }

                return redirect()->back()
                    ->with('error', 'Không thể cập nhật thông tin chủ cửa hàng!')
                    ->withInput();
            }

            return redirect()->back()
                ->with('error', 'Không có quyền cập nhật!')
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Profile update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function logout()
    {
        if (Auth::guard('owner')->check()) {
            Auth::guard('owner')->logout();
        }

        if (Auth::guard('employee')->check()) {
            Auth::guard('employee')->logout();
        }

        return redirect()->route('login')
            ->with('success', 'Đăng xuất thành công!');
    }

    public function showChangePasswordForm()
    {
        return view('management.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (Auth::guard('owner')->check()) {
            $user = Auth::guard('owner')->user();
            $model = Owner::find($user->owner_id);
            $redirectTo = 'owner.login';
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $model = Employee::find($user->employee_id);
            $redirectTo = 'employee.login';
        } else {
            return redirect()->back()->with('error', 'Không xác định được tài khoản.');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        try {
            $model->password = bcrypt($request->new_password);
            $model->save();

            // Đăng xuất admin sau khi đổi mật khẩu
            Auth::guard($user instanceof Owner ? 'owner' : 'employee')->logout();

            // Chuyển hướng đến đăng nhập với thông báo thành công
            return redirect()
                ->route('admin.login')
                ->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại với mật khẩu mới.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi đổi mật khẩu. Vui lòng thử lại.');
        }
    }
}
