$(document).ready(function() {
    // Load profile data when modal opens
    $('#profile-modal').on('show.bs.modal', function() {
        $.ajax({
            url: 'processes/profile_process.php',
            type: 'POST',
            data: {action: 'fetchProfile'},
            success: function(response) {
                const data = JSON.parse(response);
                $('#profileUsername').val(data.username);
                $('#profileEmail').val(data.email);
                $('#profileFirstName').val(data.firstname);
                $('#profileLastName').val(data.lastname);
                $('#profileMiddleName').val(data.middlename);
                $('#profileContact').val(data.contact_number);
                $('#profileRole').val(data.role_id == 1 ? 'Admin' : 'User');
            }
        });
    });

    // Handle profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'processes/profile_process.php',
            type: 'POST',
            data: $(this).serialize() + '&action=updateProfile',
            success: function(response) {
                const data = JSON.parse(response);
                if(data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        timer: 1500
                    }).then(() => {
                        $('#profile-modal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            }
        });
    });
});
