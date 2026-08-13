<?php
session_start();
include "include/connect_db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch total items count
$total_items_query = "SELECT COUNT(*) as total FROM items";
$total_items_result = $conn->query($total_items_query);
$total_items = $total_items_result->fetch_assoc()['total'];
if (!$total_items_result) {
    die("Query failed: " . $conn->error);
}

// Fetch items by category for pie chart
$category_query = "SELECT c.category_name, COUNT(i.item_id) as count 
                  FROM categories c 
                  LEFT JOIN items i ON c.category_id = i.category_id 
                  GROUP BY c.category_id";
$category_result = $conn->query($category_query);

$categories = [];
$category_counts = [];
while($row = $category_result->fetch_assoc()) {
    $categories[] = $row['category_name'];
    $category_counts[] = $row['count'];
}

// Fetch monthly inventory changes for line chart
$monthly_query = "SELECT DATE_FORMAT(timestamp, '%Y-%m') as month, 
                  COUNT(*) as changes 
                  FROM inventory_logs 
                  GROUP BY month 
                  ORDER BY month DESC 
                  LIMIT 6";
$monthly_result = $conn->query($monthly_query);

$months = [];
$changes = [];
while($row = $monthly_result->fetch_assoc()) {
    $months[] = date('M', strtotime($row['month']));
    $changes[] = $row['changes'];
}

// Fetch stock status counts
$status_query = "SELECT s.status, COUNT(i.item_id) as count 
                FROM status s 
                LEFT JOIN items i ON s.status_id = i.status_id 
                GROUP BY s.status_id";
$status_result = $conn->query($status_query);
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
        <!-- Summary Cards Row -->
        <div class="row mb-4 mt-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Items</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_items; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-box fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php while($status = $status_result->fetch_assoc()): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <?php echo $status['status']; ?></div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $status['count']; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-graph-up fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Monthly Inventory Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Item Categories</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie">
                            <canvas id="categoryPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "modals/profile_modal.php"; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            // Line Chart
            var ctx = document.getElementById('inventoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_reverse($months)); ?>,
                    datasets: [{
                        label: 'Inventory Changes',
                        data: <?php echo json_encode(array_reverse($changes)); ?>,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                }
            });

            // Pie Chart
            var ctxPie = document.getElementById('categoryPieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($categories); ?>,
                    datasets: [{
                        data: <?php echo json_encode($category_counts); ?>,
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                            '#e74a3b', '#858796', '#f6c23e', '#1cc88a'
                        ]
                    }]
                }
            });
        });
    </script>
    <script src="js/profile.js"></script>
</body>
</html>
