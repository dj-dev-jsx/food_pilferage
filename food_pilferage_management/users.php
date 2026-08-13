<?php
session_start();
include "include/connect_db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management - Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="images/food_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="style/pilferage.css">
    <link rel="stylesheet" href="style/inventory.css">
    <style>
        .dropdown-menu .dropdown-item {
            color: #000000;
            font-weight: 500;
        }
        .dropdown-menu .dropdown-item i {
            color: #28a745;
        }
        .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-md-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="images/food_logo.png" alt="Logo" height="40">
                <span>Food Pilferage Management</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php 
                        $user_id = $_SESSION['user_id'];
                        $query = "SELECT username FROM users WHERE user_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_assoc();

                        // Determine the role based on role_id
                        $role_id = $_SESSION['role_id'];
                        $role = '';
                        switch ($role_id) {
                            case 1:
                                $role = 'Admin';
                                break;
                            case 2:
                                $role = 'Inventory Staff';
                                break;
                            case 3:
                                $role = 'Kitchen Staff';
                                break;
                            default:
                                $role = 'Unknown Role';
                        }

                        // Display the username and role
                        echo htmlspecialchars($user['username']) . ' | ' . htmlspecialchars($role);
                        ?>
                    </a>


                        <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profile-modal">
                            <i class="bi bi-person me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="processes/logout_process.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="sidebar">
        <?php include "include/sidebar.php"; ?>
    </div>

    <div class="content">
        <div class="container-fluid bg-white text-dark rounded p-3 mt-3 border-top border-5 border-success border-bottom">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2><i class="bi bi-people"></i> User Management</h2>
                </div>
                <div class="col text-center">
                    <h2>
                    <?php
                    date_default_timezone_set('Asia/Manila');
                    echo date('F j, Y h:i:s A');
                    ?>
                    </h2>
                </div>
                <div class="col text-end">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="bi bi-person-plus"></i> Add User
                    </button>
                </div>
            </div>

            <div class="table-container table-responsive">
                <table class="table table-hover align-middle bg-white">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Role</th>
                            <th scope="col">Access Code</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users_record"></tbody>
                </table>
            </div>

            <div class="pagination-container mt-4 d-flex justify-content-between align-items-center">
                <div class="showing-entries">
                    Showing <span id="entries-start">0</span> to <span id="entries-end">0</span> of <span id="total-entries">0</span> entries
                </div>
                <div class="pagination-controls">
                    <button class="btn btn-outline-success" id="prev-page" disabled>Previous</button>
                    <div class="btn-group mx-2" id="page-numbers"></div>
                    <button class="btn btn-outline-success" id="next-page">Next</button>
                </div>
            </div>
        </div>
    </div>

    <?php include "modals/registration_modal.php";?>
    <?php include "modals/alert_modal.php";?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/alert.js"></script>
    <script src="js/login.js"></script>
    <script src="js/swal-alerts.js"></script>
    <script>
        let currentPage = 1;
const itemsPerPage = 10;

$(document).ready(function() {
    loadUsersData();
    

    $('#prev-page, #next-page').on('click', function() {
        if ($(this).attr('id') === 'prev-page' && currentPage > 1) {
            currentPage--;
        } else if ($(this).attr('id') === 'next-page') {
            currentPage++;
        }
        loadUsersData();
    });
});

// View user details
$(document).on('click', '.view-btn', function() {
    const userId = $(this).data('id');
    $.ajax({
        url: 'processes/user_actions.php',
        type: 'POST',
        data: {
            action: 'getUserDetails',
            user_id: userId
        },
        success: function(response) {
            if (response.status === 200) {
                $('#view-name').text(`${response.data.firstname} ${response.data.middlename} ${response.data.lastname}`);
                $('#view-email').text(response.data.email);
                $('#view-contact').text(response.data.contact_number);
                $('#view-role').text(response.data.role_name);
                $('#view-access-code').text(response.data.access_code);
                $('#view-user-modal').modal('show');
            }
        }
    });
});

// Edit user
$(document).on('click', '.edit-btn', function() {
    const userId = $(this).data('id');
    $.ajax({
        url: 'processes/user_actions.php',
        type: 'POST',
        data: {
            action: 'getUserDetails',
            user_id: userId
        },
        success: function(response) {
            if (response.status === 200) {
                $('#edit-user-form input[name="user_id"]').val(response.data.user_id);
                $('#edit-user-form input[name="firstname"]').val(response.data.firstname);
                $('#edit-user-form input[name="lastname"]').val(response.data.lastname);
                $('#edit-user-form input[name="middlename"]').val(response.data.middlename);
                $('#edit-user-form input[name="email"]').val(response.data.email);
                $('#edit-user-form input[name="contact_number"]').val(response.data.contact_number);
                $('#edit-user-form select[name="role_id"]').val(response.data.role_id);
                $('#edit-user-modal').modal('show');
            }
        }
    });
});

// Update user
$('#updateUserBtn').on('click', function() {
    const formData = $('#edit-user-form').serialize();
    $.ajax({
        url: 'processes/user_actions.php',
        type: 'POST',
        data: formData + '&action=updateUser',
        success: function(response) {
            if (response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'User updated successfully'
                });
                $('#edit-user-modal').modal('hide');
                loadUsersData();
            }
        }
    });
});

// Delete user
$(document).on('click', '.delete-btn', function() {
    const userId = $(this).data('id');
    Swal.fire({
        title: 'Delete User?',
        text: "This action cannot be undone",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'processes/user_actions.php',
                type: 'POST',
                data: {
                    action: 'deleteUser',
                    user_id: userId
                },
                success: function(response) {
                    if(response.status === 200) {
                        Swal.fire('Deleted!', 'User has been deleted.', 'success');
                        loadUsersData();
                    }
                }
            });
        }
    });
});

// Reset access code
$(document).on('click', '.reset-code-btn', function() {
    const userId = $(this).data('id');
    Swal.fire({
        title: 'Reset Access Code?',
        text: 'This will generate a new access code for the user',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'processes/user_actions.php',
                type: 'POST',
                data: {
                    action: 'resetAccessCode',
                    user_id: userId
                },
                success: function(response) {
                    if (response.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `New access code: ${response.new_code}`
                        });
                        loadUsersData();
                    }
                }
            });
        }
    });
});
    // Registration form handling
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'addUser',
            username: $('#regUsername').val(),
            email: $('#regEmail').val(),
            firstname: $('#regFirstName').val(),
            middlename: $('#regMidName').val(),
            lastname: $('#regLastName').val(),
            contact_number: $('#regContactNumber').val(),
            role: $('#regRole').val(),
            access_code: $('#access_code').val()
        };

        $.ajax({
            url: 'processes/user_actions.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'User registered successfully!'
                    }).then(() => {
                        $('#registerModal').modal('hide');
                        $('#registerForm')[0].reset();
                        loadUsersData();
                    });
                } else {
                    $('#errorMessage').removeClass('d-none').text(response.message);
                }
            },
            error: function() {
                $('#errorMessage').removeClass('d-none').text('An error occurred during registration');
            }
        });
    });





function loadUsersData() {
    $.ajax({
        url: 'processes/users_table.php',
        type: 'POST',
        data: {
            action: 'fetchUsersData',
            page: currentPage,
            items_per_page: itemsPerPage,
            search: $('#searchUser').val(),
            role: $('#roleFilter').val()
        },
        success: function(response) {
            if (response && typeof response.total_records !== 'undefined') {
                $('#users_record').html(response.records);
                updatePagination(response.total_records);
            } else {
                console.error('Invalid response format');
                updatePagination(0);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            updatePagination(0);
        }
    });
}


function updatePagination(totalRecords) {
    // Handle case when no records exist
    if (totalRecords === 0) {
        $('#entries-start').text(0);
        $('#entries-end').text(0);
        $('#total-entries').text(0);
        $('#prev-page').prop('disabled', true);
        $('#next-page').prop('disabled', true);
        $('#page-numbers').html('');
        return;
    }

    const totalPages = Math.ceil(totalRecords / itemsPerPage);
    const start = ((currentPage - 1) * itemsPerPage) + 1;
    const end = Math.min(currentPage * itemsPerPage, totalRecords);

    // Ensure values are numbers
    $('#entries-start').text(parseInt(start));
    $('#entries-end').text(parseInt(end));
    $('#total-entries').text(parseInt(totalRecords));
    
    $('#prev-page').prop('disabled', currentPage === 1);
    $('#next-page').prop('disabled', currentPage === totalPages);

    let pageNumbers = '';
    for (let i = 1; i <= totalPages; i++) {
        pageNumbers += `<button class="btn ${i === currentPage ? 'btn-success' : 'btn-outline-success'}" 
            onclick="goToPage(${i})">${i}</button>`;
    }
    $('#page-numbers').html(pageNumbers);
}


function goToPage(page) {
    currentPage = page;
    loadUsersData();
}


    </script>
</body>
</html>
