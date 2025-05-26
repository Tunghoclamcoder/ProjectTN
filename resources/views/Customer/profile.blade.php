<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="{{ asset('js/alert.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
</head>

<body>
    @include('Customer.components.header')

    <div class="alerts-container">
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
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h5 class="my-3">{{ $customer->customer_name }}</h5>
                        <p class="text-muted mb-4">{{ $customer->address }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('customer.update.profile') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" name="customer_name"
                                    value="{{ $customer->customer_name }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $customer->email }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" name="phone_number"
                                    value="{{ $customer->phone_number }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" name="address"
                                    value="{{ $customer->address }}">
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-5">Cập nhật thông tin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Customer.components.footer')
</body>

</html>
