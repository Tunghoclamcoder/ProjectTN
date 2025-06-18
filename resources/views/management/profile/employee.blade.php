<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Profile nhân viên</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />

    <!-- Or use Font Awesome as alternative -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-profile.css') }}" />
</head>

<body>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-wrapper">
            <div class="profile-header">
                <h2><i class="lni lni-user"></i> Thông tin nhân viên</h2>
            </div>

            <form id="profileForm" action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="profile-content">
                    <div class="info-group">
                        <label>Tên nhân viên</label>
                        <div class="editable-field">
                            <span class="text-display">{{ $profile->employee_name }}</span>
                            <input type="text" name="employee_name" class="edit-input"
                                value="{{ $profile->employee_name }}">
                            <i class="lni lni-pencil edit-icon"></i>
                        </div>
                    </div>

                    <div class="info-group">
                        <label>Email</label>
                        <div class="editable-field">
                            <span class="text-display">{{ $profile->email }}</span>
                            <input type="email" name="email" class="edit-input" value="{{ $profile->email }}">
                            <i class="lni lni-pencil edit-icon"></i>
                        </div>
                    </div>

                    <div class="info-group">
                        <label>Số điện thoại</label>
                        <div class="editable-field">
                            <span class="text-display">{{ $profile->phone_number }}</span>
                            <input type="tel" name="phone_number" class="edit-input"
                                value="{{ $profile->phone_number }}">
                            <i class="lni lni-pencil edit-icon"></i>
                        </div>
                    </div>

                    <div class="info-group">
                        <label>Trạng thái</label>
                        <span class="badge {{ $profile->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ $profile->status === 'active' ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </div>

                    <div class="actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            <i class="lni lni-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary save-changes">
                            <i class="lni lni-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const infoGroups = document.querySelectorAll('.info-group');
        const saveButton = document.querySelector('.save-changes');

        infoGroups.forEach(group => {
            const editIcon = group.querySelector('.edit-icon');
            const textDisplay = group.querySelector('.text-display');
            const inputField = group.querySelector('.edit-input');

            if (editIcon) {
                editIcon.addEventListener('click', () => {
                    group.classList.toggle('editing');
                    if (group.classList.contains('editing')) {
                        textDisplay.style.display = 'none';
                        inputField.style.display = 'block';
                        inputField.focus();
                    } else {
                        textDisplay.style.display = 'block';
                        inputField.style.display = 'none';
                    }

                    // Check if any field is being edited
                    isEditing = document.querySelector('.info-group.editing') !== null;
                    if (saveButton) {
                        saveButton.style.display = isEditing ? 'inline-flex' : 'none';
                    }
                });
            }
        });
    });
</script>

</html>
