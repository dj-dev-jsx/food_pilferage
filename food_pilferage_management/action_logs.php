<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "include/connect_db.php";
include "functions/functions.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="images/food_logo.png" type="image/x-icon">
    <style>
    body {
            background-color: #f8f9fa;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 1020;
            width: 250px;
            background-color: #28a745;
            overflow-y: auto;
            transition: all 0.3s;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .content {
            margin-left: 250px;
            margin-top: 56px;
            padding: 20px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .sidebar span {
                display: none;
            }
            .content {
                margin-left: 60px;
            }
        }
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
        <?php if (!isAdmin()): ?>
            <div class="container mt-5">
                <div class="alert alert-danger text-center">
                    <h3><i class="bi bi-exclamation-triangle-fill"></i> Access Restricted</h3>
                    <p>This page is for admin only.</p>
                    <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
                </div>
            </div>
        <?php else: ?>
            <div class="container-fluid bg-white text-dark rounded p-3 mt-3 border-top border-5 border-success border-bottom">
                <div class="row d-flex justify-content-between align-items-center">
                    <div class="col">
                        <h2><i class="bi bi-clock-history"></i> Action Logs</h2>
                    </div>
                    <div class="col text-center">
                        <h2>
                        <?php
                        date_default_timezone_set('Asia/Manila');
                        $date = new DateTime();
                        echo $date->format('F j, Y h:i A');
                        ?>
                        </h2>
                    </div>
                    <div class="col text-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-success" id="exportCSV">
                                <i class="bi bi-file-earmark-excel"></i> Export CSV
                            </button>
                            <button class="btn btn-outline-danger" id="clearLogs">
                                <i class="bi bi-trash"></i> Clear Logs
                            </button>
                        </div>
                    </div>
                </div>

                <div class="filter-section bg-white rounded-3 p-4 shadow-sm mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchLogs" placeholder="Search logs...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" class="form-control" id="dateFilter">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-funnel"></i></span>
                                <select class="form-select" id="actionFilter">
                                    <option value="">All Actions</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                    <option value="create">Create</option>
                                    <option value="update">Update</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <select class="form-select" id="userFilter">
                                    <option value="">All Users</option>
                                    <!-- Will be populated via PHP/AJAX -->
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
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="logsData">
                        <!-- Log records will be loaded here via AJAX -->
                    </tbody>
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
        <?php endif; ?>
    </div>
</div>
<?php include "modals/profile_modal.php"; ?>


    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// JavaScript for handling the functionality
$(document).ready(function() {
    loadLogs();
    
    function loadLogs(page = 1) {
        $.ajax({
            url: 'processes/fetch_logs.php',
            method: 'POST',
            data: {
                page: page,
                date: $('#dateFilter').val(),
                action: $('#actionFilter').val(),
                user: $('#userFilter').val(),
                search: $('#searchLogs').val()
            },
            success: function(response) {
                $('#logsData').html(response.data);
                updatePagination(response.pagination);
            }
        });
    }

    // Event listeners for filters
    $('#dateFilter, #actionFilter, #userFilter').change(function() {
        loadLogs();
    });

    // Search functionality
    $('#searchLogs').keyup(function() {
        loadLogs();
    });

    // Export functionality
    $('#exportCSV').click(function() {
        window.location.href = 'processes/export_logs.php';
    });

    // Clear logs functionality
    $('#clearLogs').click(function() {
        if(confirm('Are you sure you want to clear all logs?')) {
            $.ajax({
                url: 'processes/clear_logs.php',
                method: 'POST',
                success: function(response) {
                    loadLogs();
                }
            });
        }
    });
});
</script>
<script src="js/profile.js"></script>
</body>
</html>
