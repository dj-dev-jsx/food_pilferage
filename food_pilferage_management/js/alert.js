document.addEventListener('DOMContentLoaded', function() {
    // Handle session messages
    const sessionMessages = document.getElementById('sessionMessages');
    if (sessionMessages) {
        const alertMessage = sessionMessages.getAttribute('data-message');
        const alertType = sessionMessages.getAttribute('data-type');

        if (alertMessage) {
            Swal.fire({
                title: getAlertTitle(alertType),
                text: alertMessage,
                icon: getAlertIcon(alertType),
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            });
        }
    }

    // Handle logout confirmation
    $(document).on('click', 'a[href="processes/logout_process.php"]', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Logout Confirmation',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'processes/logout_process.php';
            }
        });
    });
});

function getAlertTitle(type) {
    const titles = {
        success: 'Success!',
        error: 'Error!',
        warning: 'Warning!',
        info: 'Information'
    };
    return titles[type] || 'Notice';
}

function getAlertIcon(type) {
    const icons = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };
    return icons[type] || 'info';
}

function triggerModal(message, type) {
    Swal.fire({
        title: getAlertTitle(type),
        text: message,
        icon: getAlertIcon(type),
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true
    });
}
