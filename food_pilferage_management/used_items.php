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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                    <h2><i class="bi bi-box-arrow-right"></i> Used Items</h2>
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
            </div>

            <div class="filter-section bg-white rounded-3 p-4 shadow-sm mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input id="search_inp" type="text" class="form-control" placeholder="Search used items...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control" id="date_filter">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <select class="form-select" id="user_filter">
                                <option value="">All Users</option>
                                <?php
                                $query = "SELECT DISTINCT u.user_id, u.username FROM users u 
                                         JOIN item_usage iu ON u.user_id = iu.user_id";
                                $result = mysqli_query($conn, $query);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['user_id'] . "'>" . $row['username'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container bg-white rounded-3 p-4 shadow-sm table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Used</th>
                        <th>Reason</th>
                        <th>Total Cost</th>
                        <th>Used By</th>
                        <th>Date Used</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="use-item-records"></tbody>
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
    </div>

    <?php include "modals/profile_modal.php"; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/alert.js"></script>
    <script src="js/used_items.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>