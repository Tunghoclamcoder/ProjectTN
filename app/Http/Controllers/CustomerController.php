<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


class CustomerController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');

            $customers = Customer::where(function ($q) use ($query) {
                $q->where('customer_name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('phone_number', 'LIKE', "%{$query}%")
                    ->orWhere('address', 'LIKE', "%{$query}%");
            })->get();

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Customer search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm'
            ], 500);
        }
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
        return view('Customer.LoginAccount.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Lấy user theo email
        $customer = Customer::where('email', $credentials['email'])->first();

        // Kiểm tra trạng thái tài khoản
        if ($customer && $customer->status === 'inactive') {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ với admin để được hỗ trợ.');
        }

        // Clear any existing intended URL
        session()->forget('url.intended');

        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('shop.home')
                ->with('success', 'Đăng nhập thành công!');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')
            ->with('success', 'Đăng xuất thành công!');
    }

    public function showRegisterForm()
    {
        return view('Customer.LoginAccount.register');
    }

    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create new customer
        $customer = new \App\Models\Customer();
        $customer->customer_name = $validated['customer_name'];
        $customer->email = $validated['email'];
        $customer->phone_number = $validated['phone_number'] ?? null;
        $customer->address = $validated['address'] ?? null;
        $customer->password = bcrypt($validated['password']);
        $customer->save();

        // Auto-login after registration
        $request->session()->put('customer_id', $customer->customer_id);

        return redirect()->route('customer.login');
    }

    public function home()
    {
        return view('shop.home');
    }

    public function index()
    {
        $customers = Customer::orderBy('customer_id', 'asc')->paginate(8);
        return view('management.customer_mana.index', compact('customers'));
    }

    public function toggleStatus(Customer $customer)
    {

        try {
            $newStatus = $customer->status == 'active' ? 'inactive' : 'active';
            $customer->update(['status' => $newStatus]);

            // Nếu tài khoản bị vô hiệu hóa, đăng xuất tất cả các phiên
            if ($newStatus === 'inactive') {
                // Buộc đăng xuất khách hàng nếu họ hiện đang đăng nhập
                Auth::guard('customer')->logout();
            }

            return response()->json([
                'success' => true,
                'message' => 'Thay đổi trạng thái thành công',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái'
            ], 500);
        }
    }

    public function profile()
    {
        $customer = Auth::guard('customer')->user();

        return view('Customer.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $authCustomer = Auth::guard('customer')->user();
        $customer = Customer::find($authCustomer->customer_id);

        $rules = [
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ];

        // Nếu email thay đổi thì check unique
        if ($request->email !== $customer->email) {
            $rules['email'] = 'required|email|unique:customer,email';
        } else {
            $rules['email'] = 'required|email';
        }

        $validated = $request->validate($rules);

        $customer->update($validated);

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function showForgotPasswordForm()
    {
        return view('Customer.LoginAccount.forgot_password');
    }

    // Xử lý gửi email đặt lại mật khẩu
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) {
            return back()->with('error', 'Email không tồn tại.');
        }

        // Tạo token và lưu vào DB
        $token = Str::random(60);
        $customer->reset_token = $token;
        $customer->reset_token_expire = now()->addMinutes(30);
        $customer->save();

        // Gửi email (bạn cần tạo view email.reset_password)
        $resetUrl = route('customer.reset_password', ['token' => $token]);

        // Gửi email
        Mail::send('Customer.emails.reset_password', [
            'token' => $token,
            'customer' => $customer,
            'resetUrl' => $resetUrl
        ], function ($message) use ($customer) {
            $message->to($customer->email);
            $message->subject('Đặt lại mật khẩu');
        });

        return back()->with('success', 'Đã gửi email đặt lại mật khẩu!');
    }

    // Hiển thị form đặt lại mật khẩu
    public function showResetPasswordForm($token, Request $request)
    {
        $customer = Customer::where('reset_token', $token)
            ->where('reset_token_expire', '>', now())
            ->first();

        $email = $customer ? $customer->email : null;

        return view('Customer.LoginAccount.reset_password', compact('token', 'email'));
    }

    // Xử lý đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::where('reset_token', $request->token)
            ->where('reset_token_expire', '>', now())
            ->first();

        if (!$customer) {
            return back()->with('error', 'Token không hợp lệ hoặc đã hết hạn.');
        }

        $customer->password = bcrypt($request->password);
        $customer->reset_token = null;
        $customer->reset_token_expire = null;
        $customer->save();

        return redirect()->route('customer.login')->with('success', 'Đặt lại mật khẩu thành công!');
    }

    // Xử lý đổi mật khẩu
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
                'confirmed'
            ],
            'new_password_confirmation' => 'required'
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự',
            'new_password.regex' => 'Mật khẩu mới phải chứa ít nhất 8 ký tự, 1 ký tự đặc biệt, 1 chữ số',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'new_password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::find(session('customer')->id);

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng!');
        }

        // Check if new password is different from currentAdd commentMore actions
        if (Hash::check($request->new_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu mới phải khác mật khẩu hiện tại!');
        }

        // Update password
        $customer->password = Hash::make($request->new_password);
        $customer->save();
        // Update session dataAdd commentMore actions
        session(['customer' => $customer]);

        return redirect()->route('customer.login')->with('success', 'Đổi mật khẩu thành công!');
    }

    public function submitForgotPasswordForm(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:customer,email',
        ], [
            'email.exists' => 'Email này không tồn tại trong hệ thống.',
        ]);

        // Gửi link đặt lại mật khẩu (sử dụng Laravel Password Broker)
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Đã gửi email đặt lại mật khẩu! Vui lòng kiểm tra hộp thư.');
        } else {
            return back()->withErrors(['email' => 'Gửi email thất bại. Vui lòng thử lại.']);
        }
    }
    public function showChangePasswordForm()
    {
        return view('Customer.LoginAccount.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp với mật khẩu mới.',
        ]);

        $authCustomer = Auth::guard('customer')->user();
        $customer = Customer::find($authCustomer->customer_id);

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        // Kiểm tra mật khẩu mới có trùng mật khẩu cũ không
        if (Hash::check($request->new_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu mới không được trùng với mật khẩu hiện tại.');
        }

        $customer->password = bcrypt($request->new_password);
        $customer->save();

        Auth::guard('customer')->logout();

        return redirect()->route('customer.login')->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
    }
}
