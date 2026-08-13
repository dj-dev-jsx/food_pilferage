<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "include/connect_db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$swalData = [];
if (isset($_SESSION['message'])) {
    $swalData = [
        'message' => $_SESSION['message'],
        'type' => $_SESSION['message_type']
    ];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
include "processes/handle_expired_items.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management - Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="images/food_logo.png" type="image/x-icon">
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
        .inventory-dropdown-menu {
    margin-top: 0;
    min-width: 200px;
}

.inventory-dropdown-item {
    cursor: pointer;
    padding: 0.5rem 1rem;
    transition: background-color 0.2s ease;
}

.inventory-btn-group .inventory-dropdown {
    display: inline-block;
}

.inventory-dropdown-item:hover {
    background-color: #f8f9fa;
}

.inventory-dropdown-item i {
    color: #28a745;
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
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="processes/logout_process.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php include "include/sidebar.php"; ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid bg-white text-dark rounded p-3 mt-3 border-top border-5 border-success border-bottom">
            <div class="row d-flex justify-content-center">
                <div class="col">
                    <h2> <i class="bi bi-box-seam"></i> Inventory Management</h2>
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

                <!-- <div class="col text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-success" id="exportCSV">
                            <i class="bi bi-file-earmark-excel"></i> Export CSV
                        </button>
                    </div>
                </div> -->
            </div>

            <div class="filter-section bg-white rounded-3 p-4 shadow-sm mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input id="search_inp" type="text" class="form-control" placeholder="Search inventory items...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-tags"></i>
                            </span>
                            <select class="form-select" id="category">
                                <option value="">All Categories</option>
                                <?php
                                $query = "SELECT * FROM categories";
                                $total_row = mysqli_query($conn, $query);
                                if(mysqli_num_rows($total_row) > 0){
                                    foreach($total_row as $row){
                                        echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-bar-chart"></i>
                            </span>
                            <select class="form-select" id="stockStatus">
                                <option value="">All Stock Status</option>
                                <?php
                                $query = "SELECT * FROM status";
                                $total_row = mysqli_query($conn, $query);
                                if(mysqli_num_rows($total_row) > 0){
                                    foreach($total_row as $row){
                                        echo "<option value='" . $row['status_id'] . "'>" . $row['status'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-light p-2 mb-1 rounded border-top border-3 border-dark mt-2">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex gap-3 align-items-center">
                        <div>
                            <button class="btn btn-sm btn-primary" disabled>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <span class="ms-2">Edit Item</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-success" disabled>
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                            <span class="ms-2">Use Item</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-danger" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                            <span class="ms-2">Delete Item</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="table-container bg-white rounded-3 p-4 shadow-sm table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Unit Price</th>
                        <th>Category</th>
                        <th class="text-center">Stock Qty</th>
                        <th class="text-center">Status</th>
                        <th>Unit</th>
                        <th>Expiration</th>
                        <th>Last Updated</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="records"></tbody>
            </table>
        </div>
        
        <div class="pagination-container mt-4 d-flex justify-content-between align-items-center">
            <div class="showing-entries">
                Showing <span id="entries-start">0</span> to <span id="entries-end">0</span> of <span id="total-entries">0</span> entries
            </div>
            <div class="pagination-controls d-flex">
                <button class="btn btn-outline-success" id="prev-page" disabled>Previous</button>
                <div class="btn-group mx-2" id="page-numbers"></div>
                <button class="btn btn-outline-success" id="next-page">Next</button>
            </div>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-modal">
                <i class="bi bi-plus-lg me-2"></i>Add New Item
            </button>
        </div>
    </div>

    <?php include "modals/add_item_modal.php"; ?>
    <?php include "modals/edit_item_modal.php"; ?>
    <?php include "modals/alert_modal.php"; ?>
    <?php include "modals/profile_modal.php"; ?>
    <?php include "modals/use_item_modal.php"; ?>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/alert.js"></script>
    <script src="js/inventory.js"></script>
    <script src="js/profile.js"></script>
    <script>
        const swalData = <?php echo !empty($swalData) ? json_encode($swalData) : 'null'; ?>;
        if (swalData) {
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: swalData.type === 'success' ? 'Success!' : 'Error!',
                    text: swalData.message,
                    icon: swalData.type,
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        }
        // Add this function to update the time
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
        
        // Call the function when the document loads
        document.addEventListener('DOMContentLoaded', updateTime);

        
    </script>
</body>
</html>
