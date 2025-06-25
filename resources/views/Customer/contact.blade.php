<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ với chúng tôi</title>
    <link rel="stylesheet" href="{{ asset('css/category_list.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    <!-- Thêm các link CSS khác nếu cần -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('Customer.components.header')

    <div class="contact-page">
        <!-- Hero Section -->
        <section class="contact-hero">
            <div class="hero-overlay">
                <div class="container">
                    <div class="row justify-content-center text-center">
                        <div class="col-lg-8">
                            <h1 class="hero-title">Liên Hệ Với Chúng Tôi</h1>
                            <p class="hero-subtitle">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy để lại tin
                                nhắn và
                                chúng tôi sẽ phản hồi sớm nhất có thể.</p>
                        </div>
                    </div>
                </div>
        </section>

        <!-- Contact Info & Form Section -->
        <section class="contact-main py-5">
            <div class="container">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Contact Information -->
                    <div class="col-lg-4 mb-5">
                        <div class="contact-info">
                            <h3 class="section-title">Thông Tin Liên Hệ</h3>
                            <p class="section-subtitle">Liên hệ trực tiếp với chúng tôi qua các kênh sau:</p>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Địa chỉ</h5>
                                    <p>123 Đường ABC, Quận 1<br>Thành phố Hồ Chí Minh, Việt Nam</p>
                                </div>
                            </div>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Điện thoại</h5>
                                    <p><a href="tel:+84123456789">+84 123 456 789</a></p>
                                    <p><a href="tel:+84987654321">+84 987 654 321</a></p>
                                </div>
                            </div>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Email</h5>
                                    <p><a href="mailto:info@company.com">info@company.com</a></p>
                                    <p><a href="mailto:support@company.com">support@company.com</a></p>
                                </div>
                            </div>

                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Giờ làm việc</h5>
                                    <p>Thứ 2 - Thứ 6: 8:00 - 18:00<br>
                                        Thứ 7: 8:00 - 12:00<br>
                                        Chủ nhật: Nghỉ</p>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="social-media mt-4">
                                <h5>Theo dõi chúng tôi</h5>
                                <div class="social-links">
                                    <a href="#" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="social-link linkedin"><i
                                            class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="social-link youtube"><i class="fab fa-youtube"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="col-lg-8">
                        <div class="contact-form-wrapper">
                            <h3 class="section-title">Gửi Tin Nhắn</h3>
                            <p class="section-subtitle">Điền thông tin vào form bên dưới và chúng tôi sẽ liên hệ lại với
                                bạn
                                sớm nhất.</p>

                            <form action="{{ route('contact.store') }}" method="POST" class="contact-form"
                                id="contactForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Họ và tên <span
                                                class="required">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="required">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel"
                                            class="form-control @error('phone') is-invalid @enderror" id="phone"
                                            name="phone" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subject" class="form-label">Tiêu đề <span
                                                class="required">*</span></label>
                                        <select class="form-select @error('subject') is-invalid @enderror"
                                            id="subject" name="subject" required>
                                            <option value="">Chọn tiêu đề</option>
                                            <option value="Hỗ trợ kỹ thuật"
                                                {{ old('subject') == 'Hỗ trợ kỹ thuật' ? 'selected' : '' }}>Hỗ trợ kỹ
                                                thuật
                                            </option>
                                            <option value="Thông tin sản phẩm"
                                                {{ old('subject') == 'Thông tin sản phẩm' ? 'selected' : '' }}>Thông
                                                tin
                                                sản phẩm</option>
                                            <option value="Hợp tác kinh doanh"
                                                {{ old('subject') == 'Hợp tác kinh doanh' ? 'selected' : '' }}>Hợp tác
                                                kinh
                                                doanh</option>
                                            <option value="Khiếu nại"
                                                {{ old('subject') == 'Khiếu nại' ? 'selected' : '' }}>Khiếu nại
                                            </option>
                                            <option value="Khác" {{ old('subject') == 'Khác' ? 'selected' : '' }}>
                                                Khác
                                            </option>
                                        </select>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Nội dung tin nhắn <span
                                            class="required">*</span></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6"
                                        placeholder="Nhập nội dung tin nhắn của bạn..." required>{{ old('message') }}</textarea>
                                    <div class="form-text">
                                        <span id="charCount">0</span>/2000 ký tự
                                    </div>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacy" required>
                                        <label class="form-check-label" for="privacy">
                                            Tôi đồng ý với <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#privacyModal">chính sách bảo mật</a> <span
                                                class="required">*</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Gửi tin nhắn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="map-section">
            <div class="container-fluid p-0">
                <div class="map-wrapper">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4326002932!2d106.69312631533414!3d10.776530192318146!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4b3332a2bd%3A0x294dcbc7e4e5c8b!2zVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1635123456789!5m2!1svi!2s"
                        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <div class="map-overlay">
                        <div class="map-info">
                            <h4><i class="fas fa-map-marker-alt me-2"></i>Vị trí của chúng tôi</h4>
                            <p>123 Đường ABC, Quận 1, TP.HCM</p>
                            <a href="https://maps.google.com" target="_blank" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-directions me-1"></i>Chỉ đường
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <h2 class="section-title">Câu Hỏi Thường Gặp</h2>
                        <p class="section-subtitle">Những câu hỏi được khách hàng quan tâm nhất</p>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-8 mx-auto">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse1">
                                        Thời gian phản hồi của bạn là bao lâu?
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Chúng tôi cam kết phản hồi tất cả tin nhắn trong vòng 24 giờ làm việc. Đối với
                                        các
                                        vấn đề khẩn cấp, chúng tôi sẽ phản hồi trong vòng 2-4 giờ.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        Tôi có thể liên hệ qua điện thoại không?
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Có, bạn có thể gọi điện trực tiếp cho chúng tôi theo số hotline trong giờ làm
                                        việc.
                                        Ngoài giờ làm việc, vui lòng để lại tin nhắn qua form liên hệ.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        Thông tin cá nhân của tôi có được bảo mật không?
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Chúng tôi cam kết bảo mật tuyệt đối thông tin cá nhân của khách hàng.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq4">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse4">
                                        Tôi có thể đến trực tiếp văn phòng không?
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Có, bạn có thể đến trực tiếp văn phòng của chúng tôi trong giờ làm việc. Tuy
                                        nhiên,
                                        chúng tôi khuyến khích bạn đặt lịch hẹn trước để được phục vụ tốt nhất.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chính Sách Bảo Mật</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    <div class="modal-body">
                        <h6>1. Thu thập thông tin</h6>
                        <p>Chúng tôi chỉ thu thập những thông tin cần thiết để phản hồi yêu cầu của bạn.</p>

                        <h6>2. Sử dụng thông tin</h6>
                        <p>Thông tin được sử dụng để liên hệ và hỗ trợ khách hàng một cách tốt nhất.</p>

                        <h6>3. Bảo mật thông tin</h6>
                        <p>Chúng tôi cam kết bảo mật tuyệt đối thông tin cá nhân của khách hàng.</p>

                        <h6>4. Chia sẻ thông tin</h6>
                        <p>Chúng tôi không chia sẻ thông tin cá nhân với bên thứ ba trừ khi có yêu cầu pháp lý.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>



        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Character counter for message textarea
                const messageTextarea = document.getElementById('message');
                const charCount = document.getElementById('charCount');

                if (messageTextarea && charCount) {
                    function updateCharCount() {
                        const count = messageTextarea.value.length;
                        charCount.textContent = count;

                        if (count > 2000) {
                            charCount.style.color = '#dc3545';
                        } else if (count > 1800) {
                            charCount.style.color = '#ffc107';
                        } else {
                            charCount.style.color = '#6c757d';
                        }
                    }

                    messageTextarea.addEventListener('input', updateCharCount);
                    updateCharCount(); // Initial count
                }

                // Form submission with loading state
                const contactForm = document.getElementById('contactForm');
                const submitBtn = document.getElementById('submitBtn');

                if (contactForm && submitBtn) {
                    contactForm.addEventListener('submit', function(e) {
                        // Add loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('btn-loading');

                        // Remove loading state after 3 seconds (fallback)
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('btn-loading');
                        }, 3000);
                    });
                }

                // Auto-hide alerts after 5 seconds
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });

                // Form validation enhancement
                const form = document.getElementById('contactForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const requiredFields = form.querySelectorAll('[required]');
                        let isValid = true;

                        requiredFields.forEach(field => {
                            if (!field.value.trim()) {
                                field.classList.add('is-invalid');
                                isValid = false;
                            } else {
                                field.classList.remove('is-invalid');
                            }
                        });

                        // Email validation
                        const emailField = document.getElementById('email');
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (emailField.value && !emailRegex.test(emailField.value)) {
                            emailField.classList.add('is-invalid');
                            isValid = false;
                        }

                        // Phone validation (if provided)
                        const phoneField = document.getElementById('phone');
                        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                        if (phoneField.value && !phoneRegex.test(phoneField.value)) {
                            phoneField.classList.add('is-invalid');
                            isValid = false;
                        }

                        if (!isValid) {
                            e.preventDefault();
                            // Scroll to first invalid field
                            const firstInvalid = form.querySelector('.is-invalid');
                            if (firstInvalid) {
                                firstInvalid.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                firstInvalid.focus();
                            }
                        }
                    });

                    // Real-time validation
                    const inputs = form.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        input.addEventListener('blur', function() {
                            if (this.hasAttribute('required') && !this.value.trim()) {
                                this.classList.add('is-invalid');
                            } else {
                                this.classList.remove('is-invalid');
                            }
                        });

                        input.addEventListener('input', function() {
                            if (this.classList.contains('is-invalid') && this.value.trim()) {
                                this.classList.remove('is-invalid');
                            }
                        });
                    });
                }

                // Smooth scrolling for internal links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });
            });
        </script>
</body>
</html>
