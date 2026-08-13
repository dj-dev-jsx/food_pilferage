<?php
session_start();
include "include/connect_db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role_id = $_SESSION['role_id'];
$user_id = $_SESSION['user_id'];

if (isset($_SESSION['message'])) {
    echo '<div id="sessionMessages" 
             data-message="' . htmlspecialchars($_SESSION['message']) . '" 
             data-type="' . $_SESSION['message_type'] . '">
          </div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management - Pilferage Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="images/food_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="style/pilferage.css">
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
        .swal2-popup {
            font-size: 1rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-md-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="images/food_logo.png" alt="Logo" height="40">
                Food Pilferage Management
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

                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
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
            <div class="row d-flex justify-content-center">
                <div class="col">
                    <h2><i class="bi bi-flag"></i> Pilferage Report</h2>
                </div>
                <div class="col text-center">
                    <h2>
                    <?php
                    date_default_timezone_set('Asia/Manila');
                    $date = new DateTime();
                    echo $date->format('F j, Y h:i:s A');
                    ?>
                    </h2>
                </div>
                <div class="col text-end">
                    <div class="btn-group">
                        <!-- <button class="btn btn-outline-success" id="exportCSV">
                            <i class="bi bi-file-earmark-excel"></i> Export CSV
                        </button> -->
                        <button class="btn btn-outline-danger" id="exportPDF">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="filter-section mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input class="form-control" type="date" name="date" id="report_date">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-flag"></i></span>
                            <select class="form-select" name="report_status" id="report_status_id">
                                <option value="">Status Filter</option>
                                <option value="open">Open</option>
                                <option value="investigating">Investigating</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchInv" placeholder="Search Items...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <select class="form-select" name="user_reported" id="user_reported">
                                <option value="">Select Employee</option>
                                <?php
                                    $query = "SELECT * from users WHERE role_id = 2";
                                    $total_row = mysqli_query($conn, $query);
                                    if(mysqli_num_rows($total_row) > 0){
                                        foreach($total_row as $row){
                                    ?>
                                    <option id="userId" name="user_id" value=<?php echo $row['user_id'];?>><?php echo $row['firstname'];?> <?php echo $row['lastname'];?></option>
                                    <?php
                                        }
                                    }else{
                                        echo "No Data Found";
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <div class="display-4 text-info mb-2">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <h4 class="card-title text-info">Total Items</h4>
                            <h2 class="card-subtitle mb-2">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM items";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <div class="display-4 text-warning mb-2">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <h4 class="card-title text-warning">Low Stock Items</h4>
                            <h2 class="card-subtitle mb-2">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM items WHERE status_id = 2";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <div class="display-4 text-danger mb-2">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <h4 class="card-title text-danger">Out of Stock</h4>
                            <h2 class="card-subtitle mb-2">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM items WHERE status_id = 3";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo $row['total'];
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="table-container table-responsive">
            <table class="table table-hover align-middle bg-white">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="text-center">#ID</th>
                        <th scope="col">Item Name</th>
                        <?php if ($role_id == 1) : ?>
                            <th scope="col">User</th>
                        <?php endif; ?>
                        <th scope="col" class="text-center">Reported Qty</th>
                        <th scope="col">Date Reported</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col">Updated at</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="pilferage_record"></tbody>
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
        <div class="text-center mt-3">
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#add-report-modal">
                <i class="bi bi-plus"></i> Report
            </button>
        </div>
    </div>

    <?php include "modals/add_report_modal.php"?>
    <?php include "modals/view_info_modal.php"?>
    <?php include "modals/edit_report_modal.php"?>
    <?php include "modals/alert_modal.php"; ?>
    <?php include "modals/profile_modal.php"; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/alert.js"></script>
    <script src="js/pilferage.js"></script>
    <script src="js/profile.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessionMessages = document.getElementById('sessionMessages');
            if (sessionMessages) {
                const message = sessionMessages.getAttribute('data-message');
                const type = sessionMessages.getAttribute('data-type');
                if (message) {
                    Swal.fire({
                        title: type.charAt(0).toUpperCase() + type.slice(1),
                        text: message,
                        icon: type,
                        confirmButtonColor: '#28a745',
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            }
        });
        $('#exportCSV').click(function() {
            window.location.href = 'processes/export_pilferage_csv.php';
        });
        $('#exportPDF').click(function() {
            window.location.href = 'processes/export_pilferage_pdf.php';
        });
        
        function updateTime() {
            const timeElement = document.querySelector('.col.text-center h2');
            setInterval(() => {
                const now = new Date();
                const options = {
                    timeZone: 'Asia/Manila',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    hour12: true
                };
                timeElement.textContent = now.toLocaleString('en-US', options);
            }, 1000);
        }


        document.addEventListener('DOMContentLoaded', updateTime);

    </script>

</body>
</html>
