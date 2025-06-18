<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Profile Chủ cửa hàng</title>
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
                <h2><i class="lni lni-user"></i> Thông tin chủ cửa hàng</h2>
            </div>

            <form id="profileForm" action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="profile-content">
                    <div class="info-group">
                        <label>Tên chủ cửa hàng</label>
                        <div class="editable-field">
                            <span class="text-display">{{ $profile->owner_name }}</span>
                            <input type="text" name="owner_name" class="edit-input"
                                value="{{ $profile->owner_name }}">
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
        const saveButton = document.querySelector('.save-profile');
        let isEditing = false;

        // Toggle edit mode for each field
        infoGroups.forEach(group => {
            const editIcon = group.querySelector('.edit-icon');
            if (editIcon) {
                editIcon.addEventListener('click', () => {
                    group.classList.toggle('editing');
                });
            }
        });

        // Save all changes
        saveButton?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('profileForm').submit();
        });
    });
</script>

</html>
