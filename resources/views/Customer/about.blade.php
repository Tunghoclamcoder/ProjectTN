<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu</title>
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">

</head>

<body>
    @include('Customer.components.header')

    <div class="about-section">
        <div class="about-container">

            <div class="about-title">Về ProjectTN Sports</div>
            <div class="about-subtitle">
                Cửa hàng thiết bị thể thao hàng đầu – Đam mê thể thao, Sáng tạo vì sức khỏe cộng đồng!
            </div>

            <div class="about-card">
                <div class="card-title">Giới Thiệu</div>
                <p>
                    ProjectTN Sports là cửa hàng chuyên cung cấp thiết bị thể thao chính hãng với đa dạng sản phẩm từ
                    các thương hiệu nổi tiếng toàn cầu. Chúng tôi tin vào sức mạnh của hoạt động thể thao để tạo nên một
                    cộng đồng khỏe mạnh, năng động và gắn kết.
                </p>
            </div>

            <div class="about-card">
                <div class="card-title">Tầm Nhìn & Sứ Mệnh</div>
                <strong>Tầm nhìn:</strong>
                <ul class="about-list">
                    <li>Trở thành hệ thống bán lẻ thiết bị thể thao được yêu thích nhất Việt Nam vào năm 2030.</li>
                    <li>Trở thành lựa chọn hàng đầu của mọi cá nhân, gia đình và tổ chức thể thao.</li>
                </ul>
                <div style="margin-top: 12px"><strong>Sứ mệnh:</strong></div>
                <ul class="about-list">
                    <li>Cung cấp giải pháp toàn diện từ sản phẩm đến dịch vụ, giúp khách hàng xây dựng lối sống lành
                        mạnh.</li>
                    <li>Khơi dậy niềm đam mê vận động, lan tỏa cảm hứng thể thao ra cộng đồng.</li>
                </ul>
            </div>

            <div class="about-card">
                <div class="card-title">Giá Trị Cốt Lõi</div>
                <ul class="about-list">
                    <li><strong>Chất lượng:</strong> 100% sản phẩm chính hãng, kiểm định nghiêm ngặt.</li>
                    <li><strong>Chính trực:</strong> Giao dịch minh bạch, đề cao sự trung thực.</li>
                    <li><strong>Khách hàng là trung tâm:</strong> Luôn lắng nghe và nâng cấp trải nghiệm cho khách hàng.
                    </li>
                    <li><strong>Đổi mới:</strong> Luôn cập nhật xu hướng, không ngừng sáng tạo sản phẩm – dịch vụ mới.
                    </li>
                    <li><strong>Trách nhiệm xã hội:</strong> Tham gia, tài trợ và tổ chức nhiều hoạt động cộng đồng.
                    </li>
                </ul>
            </div>

            <div class="about-card">
                <div class="card-title">Lịch Sử Phát Triển</div>
                <ul class="about-list">
                    <li><strong>2020:</strong> Thành lập cửa hàng đầu tiên tại Hà Nội, tập trung vào bóng đá và cầu
                        lông.</li>
                    <li><strong>2021:</strong> Ký kết độc quyền phân phối sản phẩm Adidas & Yonex.</li>
                    <li><strong>2022:</strong> Ra mắt hệ thống mua hàng trực tuyến, phục vụ trên toàn quốc.</li>
                    <li><strong>2023:</strong> Mở rộng danh mục sang các bộ môn chạy bộ, gym, yoga, bơi lội.</li>
                    <li><strong>2025:</strong> Đạt 50,000 đơn hàng thành công, phục vụ hơn 10,000 khách hàng trung
                        thành.</li>
                    <li><strong>Tương lai:</strong> Tiếp tục nâng tầm chất lượng, đẩy mạnh chuyển đổi số, mở rộng đối
                        tác quốc tế.</li>
                </ul>
            </div>

            <div class="about-card">
                <div class="card-title">Dịch Vụ Khách Hàng</div>
                <ul class="about-list">
                    <li>Tư vấn chọn sản phẩm theo nhu cầu & ngân sách.</li>
                    <li>Chính sách đổi/trả trong 15 ngày, miễn phí nếu sản phẩm lỗi.</li>
                    <li>Ship nhanh trong 2h nội thành và 2-4 ngày toàn quốc.</li>
                    <li>Bảo hành lên tới 12 tháng cho sản phẩm chính hãng.</li>
                    <li>Chương trình tích điểm, quà tặng & voucher khuyến mại thường xuyên.</li>
                    <li>Chăm sóc khách hàng 24/7 qua hotline, Facebook, Zalo.</li>
                </ul>
            </div>

            <div class="about-card">
                <div class="card-title">Đội Ngũ Quản Lý & Chuyên Gia</div>
                <img src="{{ asset('images/face/Team.jpg') }}" alt="ProjectTN Sports Logo" class="about-team-main-img">
                <div class="about-team">
                    <div class="team-member">
                        <img src="{{ asset('images/face/Ninh.jpg') }}" alt="CEO Trần Đăng Ninh">
                        <div class="team-name">Trần Đăng Ninh</div>
                        <div class="team-role">CEO & Co-founder</div>
                        <div>10+ năm kinh nghiệm ngành bán lẻ thể thao</div>
                    </div>
                    <div class="team-member">
                        <img src="{{ asset('images/face/Tung.jpg') }}" alt="COO Hoàng Sơn Tùng">
                        <div class="team-role">COO – Vận hành</div>
                        <div>Chuyên gia quản lý chuỗi cung ứng</div>
                    </div>
                    <div class="team-member">
                        <div class="team-name">Nguyễn Quốc Huy</div>
                        <div class="team-role">Sales – Bán hàng</div>
                        <div>Chuyên gia bán hàng khủng</div>
                    </div>
                    <div class="team-member">
                        <img src="{{ asset('images/face/Phuc.png') }}" alt="COO Đỗ Hồng Phúc">
                        <div class="team-name">Đỗ Hồng Phúc</div>
                        <div class="team-role">HR – Quản lý nhan viên</div>
                        <div>Chuyên gia quản lý nhân viên</div>
                    </div>
                    <div class="team-member">
                        <img src="{{ asset('images/face/Tam.jpg') }}" alt="COO Nguyễn Đức Tâm">
                        <div class="team-name">Nguyễn Đức Tâm</div>
                        <div class="team-role">PM – Phần mềm</div>
                        <div>Chuyên gia sản phẩm</div>
                    </div>
                    <!-- Thêm thành viên khác nếu muốn -->
                </div>
            </div>

            <div class="about-card">
                <div class="card-title">Đối Tác & Thương Hiệu</div>
                <ul class="about-list">
                    <li>Adidas, Yonex, Nike, Asics, Li-Ning, Mizuno, Puma, Speedo, Head, LifeSport, Everfit, ...</li>
                    <li>Đối tác vận chuyển: Giao Hàng Nhanh, Viettel Post, Lalamove, J&T Express.</li>
                    <li>Đối tác thanh toán: MoMo, ZaloPay, VNPAY, ShopeePay, thẻ nội địa và quốc tế.</li>
                </ul>
            </div>

            <div class="about-card">
                <div class="card-title">Cam Kết Của Chúng Tôi</div>
                <ul class="about-list">
                    <li>100% sản phẩm chính hãng, nói KHÔNG với hàng giả, hàng nhái.</li>
                    <li>Giá cạnh tranh - Minh bạch - Không phụ phí ẩn.</li>
                    <li>Bảo mật tuyệt đối thông tin khách hàng.</li>
                    <li>Bảo vệ quyền lợi và trải nghiệm mua sắm cho khách hàng.</li>
                </ul>
            </div>

            <div class="about-card about-faq">
                <div class="card-title">Các Câu Hỏi Thường Gặp (FAQ)</div>
                <div class="question">Q: Làm thế nào để tra cứu trạng thái đơn hàng?</div>
                <div class="answer">A: Vào mục "Đơn hàng của tôi" sau khi đăng nhập hoặc liên hệ hotline/zalo để được
                    hướng dẫn.</div>

                <div class="question">Q: Shop có nhận giao hàng toàn quốc không?</div>
                <div class="answer">A: Chúng tôi giao hàng trên toàn quốc, miễn phí vận chuyển cho đơn hàng từ 1 triệu
                    đồng.</div>

                <div class="question">Q: Sản phẩm khi nào sẽ được bảo hành?</div>
                <div class="answer">A: Tất cả sản phẩm chính hãng đều được bảo hành từ 6-12 tháng, tùy dòng sản phẩm.
                </div>

                <div class="question">Q: Có hỗ trợ thanh toán trả góp không?</div>
                <div class="answer">A: Có, áp dụng với thẻ tín dụng một số ngân hàng lớn và qua các ví điện tử.</div>
            </div>

            <div class="about-card">
                <div class="card-title">Liên Hệ</div>
                <ul class="about-list">
                    <li>Email: <a href="mailto:support@projecttnsports.vn"
                            style="color:#aeea00;">support@projecttnsports.vn</a></li>
                    <li>Hotline: <a href="tel:19001234" style="color:#aeea00;">1900 1234</a></li>
                    <li>Facebook: <a href="https://facebook.com/projecttnsports" target="_blank"
                            style="color:#aeea00;">ProjectTN Sports Fanpage</a></li>
                    <li>Địa chỉ: Tòa nhà 789, đường Mỹ Đình, Hà Nội</li>
                    <li>Giờ mở cửa: 8h00-21h00 (T2-CN)</li>
                </ul>
            </div>

            <div class="about-footer">
                © {{ date('Y') }} ProjectTN Sports – Đam mê thể thao, Kết nối mọi người!
            </div>
        </div>
    </div>

    @include('Customer.components.footer')
</body>

</html>
