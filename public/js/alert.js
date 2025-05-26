//JS hiển thị thông báo
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {
            // Show alert
            setTimeout(() => {
                alert.classList.add('show');
            }, 100);

            // Hide alert after 5 seconds
            setTimeout(() => {
                alert.classList.add('fade-out');

                // Remove alert from DOM after animation
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
