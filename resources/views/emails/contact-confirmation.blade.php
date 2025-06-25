<!DOCTYPE html>Add commentMore actions
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận tin nhắn</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .message-box { background: white; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cảm ơn bạn đã liên hệ!</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $contact->name }}</strong>,</p>

            <p>Chúng tôi đã nhận được tin nhắn của bạn với tiêu đề: <strong>"{{ $contact->subject }}"</strong></p>

            <div class="message-box">
                <p>Nội dung tin nhắn của bạn:</p>
                <p><em>{{ $contact->message }}</em></p>
            </div>

            <p>Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất có thể qua email: <strong>{{ $contact->email }}</strong></p>

            <p>Cảm ơn bạn đã quan tâm đến dịch vụ của chúng tôi!</p>

            <p>Trân trọng,<br>
            Đội ngũ hỗ trợ khách hàng</p>
        </div>
    </div>
</body>
</html>
