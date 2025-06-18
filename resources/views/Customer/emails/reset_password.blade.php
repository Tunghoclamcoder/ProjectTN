<!DOCTYPE html>Add commentMore actions
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đặt lại mật khẩu - {{ config('app.name') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <!-- Email Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Main Email Content -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">

                    <!-- Header Section -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <!-- Logo -->
                                        <div style="background-color: rgba(255,255,255,0.2); width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 7C13.1 7 14 7.9 14 9S13.1 11 12 11 10 10.1 10 9 10.9 7 12 7ZM18 15.59C16.5 17.42 14.23 18.5 12 18.5S7.5 17.42 6 15.59V14C6 11.79 9.58 10 12 10S18 11.79 18 14V15.59Z" fill="white"/>
                                            </svg>
                                        </div>

                                        <!-- Title -->
                                        <h1 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; line-height: 1.2;">
                                            Đặt lại mật khẩu
                                        </h1>
                                        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 10px 0 0 0; line-height: 1.4;">
                                            Yêu cầu khôi phục tài khoản của bạn
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td>
                                        <!-- Greeting -->
                                        <h2 style="color: #2d3748; font-size: 24px; font-weight: 600; margin: 0 0 20px 0; line-height: 1.3;">
                                            Xin chào {{ $customer->name ?? 'Khách hàng' }}!
                                        </h2>

                                        <!-- Main Message -->
                                        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                                            Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Để tiếp tục, vui lòng nhấp vào nút bên dưới để tạo mật khẩu mới.
                                        </p>

                                        <!-- Security Notice -->
                                        <div style="background-color: #f7fafc; border-left: 4px solid #4299e1; padding: 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
                                            <p style="color: #2d3748; font-size: 14px; margin: 0; line-height: 1.5;">
                                                <strong>🔒 Lưu ý bảo mật:</strong> Liên kết này chỉ có hiệu lực trong <strong>60 phút</strong> kể từ khi được gửi và chỉ có thể sử dụng một lần.
                                            </p>
                                        </div>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 35px 0;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $resetUrl }}"
                                                       style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-size: 16px; font-weight: 600; text-align: center; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
                                                        Đặt lại mật khẩu ngay
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Alternative Link -->
                                        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;">
                                            <p style="color: #6b7280; font-size: 14px; margin: 0 0 10px 0; line-height: 1.5;">
                                                Nếu nút trên không hoạt động, bạn có thể sao chép và dán liên kết sau vào trình duyệt:
                                            </p>
                                            <p style="word-break: break-all; color: #4299e1; font-size: 14px; margin: 0; padding: 10px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px;">
                                                {{ $resetUrl }}
                                            </p>
                                        </div>

                                        <!-- Help Section -->
                                        <div style="margin: 30px 0;">
                                            <h3 style="color: #2d3748; font-size: 18px; font-weight: 600; margin: 0 0 15px 0;">
                                                Cần hỗ trợ?
                                            </h3>
                                            <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin: 0;">
                                                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn và không có thay đổi nào được thực hiện.
                                            </p>
                                        </div>

                                        <!-- Contact Info -->
                                        <div style="border-top: 1px solid #e2e8f0; padding-top: 25px; margin-top: 30px;">
                                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0;">
                                                Nếu bạn gặp khó khăn, hãy liên hệ với chúng tôi qua:
                                                <br>
                                                📧 Email: <a href="mailto:support@example.com" style="color: #4299e1; text-decoration: none;">support@example.com</a>
                                                <br>
                                                📞 Hotline: <a href="tel:+84123456789" style="color: #4299e1; text-decoration: none;">+84 123 456 789</a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <!-- Company Info -->
                                        <p style="color: #6b7280; font-size: 14px; margin: 0 0 15px 0; line-height: 1.5;">
                                            <strong>{{ config('app.name') }}</strong>
                                            <br>
                                            Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM
                                            <br>
                                            Website: <a href="{{ config('app.url') }}" style="color: #4299e1; text-decoration: none;">{{ config('app.url') }}</a>
                                        </p>

                                        <!-- Social Links -->
                                        <div style="margin: 20px 0;">
                                            <a href="#" style="display: inline-block; margin: 0 10px; text-decoration: none;">
                                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDIuMTYzQzE1LjIwNCAyLjE2MyAxNS41ODQgMi4xNzUgMTYuODUgMi4yMzNDMTguMTAyIDIuMjkgMTguNzk1IDIuNDkzIDE5LjMwNCAyLjY1NEMxOS44NTYgMi44MjcgMjAuMjY5IDMuMDQyIDIwLjcyMSAzLjQ5NEMyMS4xNzMgMy45NDYgMjEuMzg4IDQuMzU5IDIxLjU2MSA0LjkxMUMyMS43MjIgNS40MiAyMS45MjUgNi4xMTMgMjEuOTgyIDcuMzY1QzIyLjA0IDguNjMxIDIyLjA1MiA5LjAxMSAyMi4wNTIgMTIuMjE1QzIyLjA1MiAxNS40MTkgMjIuMDQgMTUuNzk5IDIxLjk4MiAxNy4wNjVDMjEuOTI1IDE4LjMxNyAyMS43MjIgMTkuMDEgMjEuNTYxIDE5LjUxOUMyMS4zODggMjAuMDcxIDIxLjE3MyAyMC40ODQgMjAuNzIxIDIwLjkzNkMyMC4yNjkgMjEuMzg4IDE5Ljg1NiAyMS42MDMgMTkuMzA0IDIxLjc3NkMxOC43OTUgMjEuOTM3IDE4LjEwMiAyMi4xNCAxNi44NSAyMi4xOTdDMTUuNTg0IDIyLjI1NSAxNS4yMDQgMjIuMjY3IDEyIDIyLjI2N0M4Ljc5NiAyMi4yNjcgOC40MTYgMjIuMjU1IDcuMTUgMjIuMTk3QzUuODk4IDIyLjE0IDUuMjA1IDIxLjkzNyA0LjY5NiAyMS43NzZDNC4xNDQgMjEuNjAzIDMuNzMxIDIxLjM4OCAzLjI3OSAyMC45MzZDMi44MjcgMjAuNDg0IDIuNjEyIDIwLjA3MSAyLjQzOSAxOS41MTlDMi4yNzggMTkuMDEgMi4wNzUgMTguMzE3IDIuMDE4IDE3LjA2NUMxLjk2IDE1Ljc5OSAxLjk0OCAxNS40MTkgMS45NDggMTIuMjE1QzEuOTQ4IDkuMDExIDEuOTYgOC42MzEgMi4wMTggNy4zNjVDMi4wNzUgNi4xMTMgMi4yNzggNS40MiAyLjQzOSA0LjkxMUMyLjYxMiA0LjM1OSAyLjgyNyAzLjk0NiAzLjI3OSAzLjQ5NEMzLjczMSAzLjA0MiA0LjE0NCAyLjgyNyA0LjY5NiAyLjY1NEM1LjIwNSAyLjQ5MyA1LjcxOSAyMy45MjYgNi45OTcgMjMuOTg0QzguMjc3IDI0LjA0MiA4LjI3NyAyMy45ODQgNi45OTdDMjMuOTI2IDUuNzE5IDIzLjcyMyA0Ljg0OSAyMy40MjYgNC4wODRDMjMuMTIxIDMuMjk1IDIyLjcwOSAyLjYyNSAyMi4wNDIgMS45ODZDMjEuMzc1IDEuMzQ3IDIwLjcwNSAwLjkzNSAxOS45MTYgMC42M0MxOS4xNTEgMC4zMzMgMTguMjgxIDAuMTMgMTcuMDAzIDAuMDcyQzE1LjcyMyAwLjAxNCAxNS4zMTUgMCAxMiAwWiIgZmlsbD0iIzQyOTlFMSIvPgo8L3N2Zz4=" alt="Facebook" style="width: 24px; height: 24px;">
                                            </a>
                                            <a href="#" style="display: inline-block; margin: 0 10px; text-decoration: none;">
                                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDIuMTYzQzE1LjIwNCAyLjE2MyAxNS41ODQgMi4xNzUgMTYuODUgMi4yMzNDMTguMTAyIDIuMjkgMTguNzk1IDIuNDkzIDE5LjMwNCAyLjY1NEMxOS44NTYgMi44MjcgMjAuMjY5IDMuMDQyIDIwLjcyMSAzLjQ5NEMyMS4xNzMgMy45NDYgMjEuMzg4IDQuMzU5IDIxLjU2MSA0LjkxMUMyMS43MjIgNS40MiAyMS45MjUgNi4xMTMgMjEuOTgyIDcuMzY1QzIyLjA0IDguNjMxIDIyLjA1MiA5LjAxMSAyMi4wNTIgMTIuMjE1QzIyLjA1MiAxNS40MTkgMjIuMDQgMTUuNzk5IDIxLjk4MiAxNy4wNjVDMjEuOTI1IDE4LjMxNyAyMS43MjIgMTkuMDEgMjEuNTYxIDE5LjUxOUMyMS4zODggMjAuMDcxIDIxLjE3MyAyMC40ODQgMjAuNzIxIDIwLjkzNkMyMC4yNjkgMjEuMzg4IDE5Ljg1NiAyMS42MDMgMTkuMzA0IDIxLjc3NkMxOC43OTUgMjEuOTM3IDE4LjEwMiAyMi4xNCAxNi44NSAyMi4xOTdDMTUuNTg0IDIyLjI1NSAxNS4yMDQgMjIuMjY3IDEyIDIyLjI2N0M4Ljc5NiAyMi4yNjcgOC40MTYgMjIuMjU1IDcuMTUgMjIuMTk3QzUuODk4IDIyLjE0IDUuMjA1IDIxLjkzNyA0LjY5NiAyMS43NzZDNC4xNDQgMjEuNjAzIDMuNzMxIDIxLjM4OCAzLjI3OSAyMC45MzZDMi44MjcgMjAuNDg0IDIuNjEyIDIwLjA3MSAyLjQzOSAxOS41MTlDMi4yNzggMTkuMDEgMi4wNzUgMTguMzE3IDIuMDE4IDE3LjA2NUMxLjk2IDE1Ljc5OSAxLjk0OCAxNS40MTkgMS45NDggMTIuMjE1QzEuOTQ4IDkuMDExIDEuOTYgOC42MzEgMi4wMTggNy4zNjVDMi4wNzUgNi4xMTMgMi4yNzggNS40MiAyLjQzOSA0LjkxMUMyLjYxMiA0LjM1OSAyLjgyNyAzLjk0NiAzLjI3OSAzLjQ5NEMzLjczMSAzLjA0MiA0LjE0NCAyLjgyNyA0LjY5NiAyLjY1NEM1LjIwNSAyLjQ5MyA1LjcxOSAyMy45MjYgNi45OTcgMjMuOTg0QzguMjc3IDI0LjA0MiA4LjI3NyAyMy45ODQgNi45OTdDMjMuOTI2IDUuNzE5IDIzLjcyMyA0Ljg0OSAyMy40MjYgNC4wODRDMjMuMTIxIDMuMjk1IDIyLjcwOSAyLjYyNSAyMi4wNDIgMS45ODZDMjEuMzc1IDEuMzQ3IDIwLjcwNSAwLjkzNSAxOS45MTYgMC42M0MxOS4xNTEgMC4zMzMgMTguMjgxIDAuMTMgMTcuMDAzIDAuMDcyQzE1LjcyMyAwLjAxNCAxNS4zMTUgMCAxMiAwWiIgZmlsbD0iIzQyOTlFMSIvPgo8L3N2Zz4=" alt="Instagram" style="width: 24px; height: 24px;">
                                            </a>
                                        </div>

                                        <!-- Copyright -->
                                        <p style="color: #9ca3af; font-size: 12px; margin: 15px 0 0 0; line-height: 1.4;">
                                            © {{ date('Y') }} {{ config('app.name') }}. Tất cả quyền được bảo lưu.
                                            <br>
                                            Email này được gửi tự động, vui lòng không trả lời.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
