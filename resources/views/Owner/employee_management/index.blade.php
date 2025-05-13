<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý nhân viên</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/crud.css') }}">
</head>

<body>
    <!-- Include sidebar và header của dashboard -->
    @include('components.admin-header')

    <div class="container">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Danh sách <b>Nhân viên</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('admin.employee.create') }}" class="btn btn-success">
                                <i class="material-icons">&#xE147;</i> <span>Thêm nhân viên mới</span>
                            </a>
                            <div class="search-box">
                                <i class="material-icons">&#xE8B6;</i>
                                <input type="text" class="form-control" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên nhân viên <i class="fa fa-sort"></i></th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone_number }}</td>
                                <td >
                                    <span style="font-size: 10px"
                                        class="badge status-badge {{ $employee->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $employee->status == 'active' ? 'Đang làm việc' : 'Đã nghỉ làm' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.employee.edit', $employee->employee_id) }}" class="edit"
                                        title="Chỉnh sửa" data-toggle="tooltip">
                                        <i class="material-icons">&#xE254;</i>
                                    </a>
                                    <form action="{{ route('admin.employee.delete', $employee->employee_id) }}"
                                        method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete" title="Xóa" data-toggle="tooltip"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')">
                                            <i class="material-icons">&#xE872;</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="clearfix">
                    <div class="hint-text">
                        Hiển thị <b>{{ $employees->count() }}</b> trên tổng số <b>{{ $employees->total() }}</b> nhân
                        viên
                    </div>
                    {{ $employees->links() }}
                    <ul class="pagination">
                        <li class="page-item disabled" style="margin-right: 10px;">
                            <a href="#">Previous</a>
                        </li>
                        <li class="page-item"><a href="#" class="page-link">1</a></li>
                        <li class="page-item"><a href="#" class="page-link">2</a></li>
                        <li class="page-item active"><a href="#" class="page-link">3</a></li>
                        <li class="page-item"><a href="#" class="page-link">4</a></li>
                        <li class="page-item"><a href="#" class="page-link">5</a></li>
                        <li class="page-item"><a href="#" class="page-link">Next</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>

</html>
