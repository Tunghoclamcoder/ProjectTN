<!DOCTYPE html>Add commentMore actions
<html>
<head>
    <meta charset="utf-8">
    <title>Tin nhắn liên hệ mới</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #667eea; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .info-row { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .message-box { background: white; padding: 15px; border-left: 4px solid #667eea; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tin nhắn liên hệ mới từ website</h2>
        </div>
        <div class="content">
            <div class="info-row">
                <span class="label">Họ tên:</span> {{ $contact->name }}
            </div>
            <div class="info-row">
                <span class="label">Email:</span> {{ $contact->email }}
            </div>
            @if($contact->phone)
            <div class="info-row">
                <span class="label">Điện thoại:</span> {{ $contact->phone }}
            </div>
            @endif
            <div class="info-row">
                <span class="label">Tiêu đề:</span> {{ $contact->subject }}
            </div>
            <div class="info-row">
                <span class="label">Thời gian:</span> {{ $contact->created_at->format('d/m/Y H:i:s') }}
            </div>

            <div class="message-box">
                <div class="label">Nội dung tin nhắn:</div>
                <p>{{ $contact->message }}</p>
            </div>
        </div>
    </div>
</body>
</html>
