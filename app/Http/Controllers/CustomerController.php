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
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $authCustomer = Auth::guard('customer')->user();
        $customer = Customer::find($authCustomer->customer_id);

        $customer->update([
            'customer_name' => $request->customer_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

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

        // Tạo token và lưu vào DB (giả sử bạn có cột reset_token và reset_token_expire)
        $token = Str::random(60);
        $customer->reset_token = $token;
        $customer->reset_token_expire = now()->addMinutes(30);
        $customer->save();

        // Gửi email (bạn cần tạo view email.reset_password)
        Mail::send('emails.reset_password', ['token' => $token, 'customer' => $customer], function ($message) use ($customer) {
            $message->to($customer->email);
            $message->subject('Đặt lại mật khẩu');
        });

        return back()->with('success', 'Đã gửi email đặt lại mật khẩu!');
    }

    // Hiển thị form đặt lại mật khẩu
    public function showResetPasswordForm($token)
    {
        return view('Customer.LoginAccount.reset_password', compact('token'));
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

    // Hiển thị form đổi mật khẩu (khi đã đăng nhập)
    public function showChangePasswordForm()
    {
        return view('Customer.LoginAccount.change_password');
    }

    // Xử lý đổi mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $authCustomer = Auth::guard('customer')->user();
        $customer = Customer::find($authCustomer->customer_id);

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        $customer->password = bcrypt($request->new_password);
        $customer->save();

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}
