
$(document).on('submit', '#loginForm', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('login_user', true);

    Swal.fire({
        title: 'Logging In',
        html: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        type: 'POST',
        url: 'processes/login_process.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Redirecting...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "dashboard.php";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: res.message || 'Please try again'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid server response'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to submit the form!'
            });
        }
    });
});