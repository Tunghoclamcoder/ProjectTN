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
    <script src="{{ asset('js/alert.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>
    @include('management.components.admin-header')

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

    <div class="container">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{{ route('admin.dashboard') }}" class="btn back-btn">
                                <i class="fa fa-arrow-left"></i>
                                <span style="font-size: 12px; font-weight: 500;"> Quay lại</span>
                            </a>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <h2>Quản lý <b>Nhân viên</b></h2>
                                <a href="{{ route(name: 'admin.employee.create') }}" class="btn btn-success mt-2 mb-4">
                                    <i class="material-icons">&#xE147;</i>
                                    <span>Thêm mới</span>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <div class="search-box">
                                    <i class="material-icons">&#xE8B6;</i>
                                    <input type="text" class="form-control"
                                        placeholder="Tìm kiếm theo tên, email hoặc số điện thoại...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover table-bordered" id="employeeTable">
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
                                    <td>
                                        <span style="font-size: 10px; display: flex; justify-content: center"
                                            class="badge status-badge {{ $employee->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $employee->status == 'active' ? 'Đang làm việc' : 'Đã nghỉ làm' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.employee.edit', $employee->employee_id) }}"
                                            class="edit" title="Chỉnh sửa" data-toggle="tooltip">
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
                        <div class="footer-container">
                            <div class="pagination-info">
                                <span>Tổng số lượng : </span>
                                <span class="total-records">{{ $employees->total() }}</span>
                            </div>

                            <div class="page-info">
                                <div class="page-info-text">
                                    Trang <span class="page-number">{{ $employees->currentPage() }}</span>
                                    <span class="all-page-number"> / {{ $employees->lastPage() }} </span>
                                </div>
                                <button class="next-page-btn" onclick="nextPage()"
                                    {{ $employees->currentPage() >= $employees->lastPage() ? 'disabled' : '' }}>
                                    <span>Trang tiếp</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.querySelector('.search-box input');
                const employeeTable = document.querySelector('#employeeTable tbody');

                const debounce = (func, wait) => {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                };

                const handleSearch = debounce(async (e) => {
                    const query = e.target.value.trim();

                    try {
                        // Log để debug
                        console.log('Sending search request for:', query);

                        const response = await fetch(
                            `/admin/employees/search?query=${encodeURIComponent(query)}`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                        // Log response status để debug
                        console.log('Response status:', response.status);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();
                        console.log('Response data:', data); // Log data để debug

                        if (!data.success) {
                            throw new Error(data.message || 'Search failed');
                        }

                        if (data.data.length === 0) {
                            employeeTable.innerHTML =
                                '<tr><td colspan="6" class="text-center">Không tìm thấy nhân viên nào</td></tr>';
                            return;
                        }

                        employeeTable.innerHTML = data.data.map(employee => `
                <tr>
                    <td>${employee.employee_id}</td>
                    <td>${employee.employee_name}</td>
                    <td>${employee.email || ''}</td>
                    <td>${employee.phone_number || ''}</td>
                    <td><span style="font-size: 10px; display: flex; justify-content: center"
                                            class="badge status-badge {{ $employee->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $employee->status == 'active' ? 'Đang làm việc' : 'Đã nghỉ làm' }}
                                        </span>
                                        </td>
                    <td>
                        <div class="btn-group" style="display: flex; justify-content: center;">
                            <a href="/admin/employees/${employee.employee_id}/edit"
                               class="btn btn-warning btn-sm">
                                <i class="material-icons">&#xE254;</i>
                            </a>
                        </div>
                    </td>
                </tr>
            `).join('');

                    } catch (error) {
                        console.error('Search error:', error);
                        employeeTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Đã xảy ra lỗi khi tìm kiếm: ${error.message}
                    </td>
                </tr>`;
                    }
                }, 300);

                searchInput.addEventListener('input', handleSearch);
            });
        </script>
</body>

</html>
