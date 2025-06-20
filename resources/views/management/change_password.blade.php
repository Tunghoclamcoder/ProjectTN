<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin_chnage_password.css') }}" />
</head>

<body>
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 w-100 animate__animated animate__fadeIn"
            style="max-width: 500px;">
            <div class="card-body p-4 p-sm-5">
                <h2 class="card-title text-center mb-4 animate__animated animate__bounceIn">Đổi mật khẩu Admin</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form class="needs-validation" method="POST" action="{{ route('admin.change-password.update') }}"
                    novalidate>
                    @csrf
                    <div class="mb-4">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePassword('current_password')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback">Vui lòng nhập mật khẩu hiện tại</div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required
                                pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePassword('new_password')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback">Mật khẩu phải ít nhất 8 ký tự, gồm chữ và số</div>
                        </div>

                        <small class="text-muted">Mật khẩu phải ít nhất 8 ký tự, gồm chữ và số</small>
                    </div>
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePassword('new_password_confirmation')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback">Mật khẩu xác nhận không khớp</div>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-3 btn-hover-effect">Đổi mật
                        khẩu</button>

                    <a href="{{ url()->previous() }}" class="btn btn-secondary w-100 py-2 mt-2">
                        <i class="bi bi-arrow-left"></i> Quay về
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <script>
        particlesJS("particles-bg", {
            particles: {
                number: {
                    value: 80
                },
                color: {
                    value: "#ffffff"
                },
                shape: {
                    type: "circle"
                },
                opacity: {
                    value: 0.5
                },
                size: {
                    value: 3
                },
                move: {
                    enable: true,
                    speed: 2
                }
            }
        });

        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        document.getElementById('new_password').addEventListener('input', function() {
            const strength = this.value.length;
            const progressBar = document.getElementById('passwordStrength');
            progressBar.style.width = Math.min(strength * 10, 100) + '%';
            if (strength < 8) {
                progressBar.className = 'progress-bar bg-danger';
            } else if (strength < 10) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-success';
            }
        });


        // Client-side validation
        const form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            // Check password match
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('new_password_confirmation').value;
            if (newPass !== confirmPass) {
                event.preventDefault();
                document.getElementById('new_password_confirmation').setCustomValidity(
                    'Mật khẩu xác nhận không khớp');
            } else {
                document.getElementById('new_password_confirmation').setCustomValidity('');
            }
            form.classList.add('was-validated');
        });
    </script>
</body>

</html>
