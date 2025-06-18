<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://threejs.org/examples/js/libs/stats.min.js"></script>
    <script src="{{ asset('js/alert.js') }}"></script>

</head>

<div id="particles-js"></div>

<body class="login">
    <div class="container">
        <div class="login-container-wrapper clearfix">
            <div class="logo">
                <img src="{{ asset('images/hacker.png') }}">
            </div>
            <div class="welcome"><strong>Xin chào,</strong> Vui lòng đăng nhập tài khoản Admin</div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form class="form-horizontal login-form" method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <!-- Role selection -->
                <div class="form-group relative mb-4">
                    <select name="role" class="form-control input-lg" required>
                        <option value="">-- Chọn vai trò --</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Chủ cửa hàng</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Nhân viên</option>
                    </select>
                    <i class="fa fa-users"></i>
                </div>

                <div class="form-group relative">
                    <input id="email" name="email" class="form-control input-lg" type="email"
                        placeholder="Email" required value="{{ old('email') }}">
                    <i class="fa fa-user"></i>
                </div>

                <div class="form-group relative password">
                    <input id="password" name="password" class="form-control input-lg" type="password"
                        placeholder="Password" required>
                    <i class="fa fa-lock"></i>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-lg w-100">Đăng nhập</button>
                </div>
            </form>
        </div>
    </div>

</body>

<style>
    @import url(https://fonts.googleapis.com/css?family=Varela);

    html {
        height: 100%;
    }

    body {
        background: #f2f2f2;
        font-family: 'Varela', sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #333;
        position: relative;
    }

    label {
        margin-top: 6px;
        line-height: 17px;
    }

    a {
        color: #fff;
    }

    a:focus,
    a:hover {
        color: #008080;
    }

    .logo img {
        width: 52px;
    }

    .checkbox-inline+.checkbox-inline,
    .radio-inline+.radio-inline {
        margin-top: 6px;
    }

    /******* Login Page *******/

    body.login {
        background: rgba(241, 242, 181, 1);
        background: -moz-radial-gradient(center, ellipse cover, rgba(255, 255, 255, 1) 0%, rgba(19, 80, 88, 1) 100%);
        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(255, 255, 255, 1)), color-stop(100%, rgba(19, 80, 88, 1)));
        background: -webkit-radial-gradient(center, ellipse cover, rgba(255, 255, 255, 1) 0%, rgba(19, 80, 88, 1) 100%);
        background: -o-radial-gradient(center, ellipse cover, rgba(255, 255, 255, 1) 0%, rgba(19, 80, 88, 1) 100%);
        background: -ms-radial-gradient(center, ellipse cover, rgba(255, 255, 255, 1) 0%, rgba(19, 80, 88, 1) 100%);
        background: radial-gradient(ellipse at center, rgba(255, 255, 255, 1) 0%, rgba(19, 80, 88, 1) 100%);
        filter: progid: DXImageTransform.Microsoft.gradient(startColorstr='#f1f2b5', endColorstr='#135058', GradientType=1);
    }

    .relative {
        position: relative;
    }

    .login-container-wrapper .logo,
    .login-container-wrapper .welcome {
        margin: 0 0 20px 0;
        font-size: 16px;
        color: #fff;
        text-align: center;
        letter-spacing: 1px;
    }

    .login-container-wrapper .logo {
        text-align: center;
        position: absolute;
        top: -42px;
        margin: 0 auto;
        width: 25%;
        left: 37.5%;
        border-radius: 50%;
        background-color: #344455;
        padding: 25px;
        box-shadow: 0px 0px 9px 2px #344454;
    }

    .login-container-wrapper {
        max-width: 400px;
        margin: 10% auto 8%;
        padding: 40px;
        box-sizing: border-box;
        background: rgba(57, 89, 116, 0.8);
        box-shadow: 1px 1px 10px 1px #000000, 8px 8px 0px 0px #344454, 12px 12px 10px 0px #000000;
        position: relative;
        padding-top: 80px;
    }

    .login input:focus+.fa {
        color: #fff;
    }

    .login-form .form-group {
        margin-right: 0;
        margin-left: 0;
    }

    .login-form i {
        position: absolute;
        top: 18px;
        right: 20px;
        color: #93a5ab;
    }

    .login-form .input-lg {
        font-size: 16px;
        height: 52px;
        padding: 10px 25px;
        border-radius: 0;
        margin-top: 14px;
    }

    .login input[type="email"],
    .login input[type="password"],
    .login input:focus {
        background-color: rgba(40, 52, 67, 0.75);
        border: 1px solid #4a525f;
        color: #eee;
        border-left: 4px solid #93a5ab;
    }

    .login input:focus {
        border-left: 4px solid #ccd8da;
    }

    input:-webkit-autofill,
    textarea:-webkit-autofill,
    select:-webkit-autofill {
        background-color: rgba(40, 52, 67, 0.75) !important;
        background-image: none;
        color: rgb(0, 0, 0);
        border-color: #FAFFBD;
    }

    .login .checkbox label,
    .login .checkbox a {
        color: #ddd;
    }

    .btn-success {
        background-color: transparent;
        background-image: none;
        padding: 8px 50px;
        border-radius: 0;
        border: 2px solid #93a5ab;
        box-shadow: inset 0 0 0 0 #7692A7;
        -webkit-transition: all ease 0.8s;
        -moz-transition: all ease 0.8s;
        transition: all ease 0.8s;
        margin-top: 16px;
    }

    .btn-success:focus,
    .btn-success:hover,
    .btn-success.active,
    .btn-success:active {
        background-color: transparent;
        border-color: #fff;
        box-shadow: inset 0 0 100px 0 #7692A7;
        color: #FFF;
    }

    #particles-js {
        /*   background: cornflowerblue; */
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: -1;
    }

    .forgot-password-container {
        text-align: center;
        margin-top: 20px;
    }

    .forgot-password-container .forget {
        color: #ddd;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .forgot-password-container .forget:hover {
        color: #fff;
    }
</style>

<script>
    /* ---- particles.js config ---- */

    particlesJS("particles-js", {
        "particles": {
            "number": {
                "value": 60,
                "density": {
                    "enable": true,
                    "value_area": 1000
                }
            },
            "color": {
                "value": ["#344455", "#ffffff"]
            },
            "shape": {
                "type": "edge",
                "stroke": {
                    "width": 0,
                    "color": "#000000"
                },
                "polygon": {
                    "nb_sides": 5
                },
                "image": {
                    "src": "img/github.svg",
                    "width": 100,
                    "height": 100
                }
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {
                    "enable": false,
                    "speed": 1,
                    "opacity_min": 0.1,
                    "sync": false
                }
            },
            "size": {
                "value": 4,
                "random": true,
                "anim": {
                    "enable": false,
                    "speed": 40,
                    "size_min": 0.1,
                    "sync": false
                }
            },
            "line_linked": {
                "enable": true,
                "distance": 50,
                "color": "#fff",
                "opacity": 0.5,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 3,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {
                    "enable": false,
                    "rotateX": 600,
                    "rotateY": 1200
                }
            }
        },
        "retina_detect": true
    });


    /* ---- stats.js config ---- */

    var count_particles, stats, update;
    stats = new Stats;
    stats.setMode(0);
    stats.domElement.style.position = 'absolute';
    stats.domElement.style.left = '0px';
    stats.domElement.style.top = '0px';
    document.body.appendChild(stats.domElement);
    count_particles = document.querySelector('.js-count-particles');
    update = function() {
        stats.begin();
        stats.end();
        if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
            count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
        }
        requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
</script>
