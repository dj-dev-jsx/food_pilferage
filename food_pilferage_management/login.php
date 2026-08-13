<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "include/connect_db.php";

if (isset($_SESSION['message'])) {
    echo '<div id="sessionMessages" data-message="' . htmlspecialchars($_SESSION['message']) . '" data-type="' . $_SESSION['message_type'] . '"></div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management - Log In and Sign Up</title>
    <link rel="shortcut icon" href="images/food_logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">i
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #20c997;
            --dark-color: #343a40;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 80px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 1000px;
            width: 90%;
            margin: 20px auto;
        }

        .brand-side {
            background: linear-gradient(45deg, #343a40, #495057);
            padding: 3rem;
            position: relative;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .credentials-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 2rem;
        }

        .login-form {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 600px;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
        }

        .btn-login {
            background: var(--primary-color);
            color: white;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .food-icon {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.3);
            position: absolute;
            animation: floatIcons 8s infinite;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1));
        }

        @keyframes floatIcons {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        .icon1 { top: 10%; left: 15%; animation-delay: 0s; }
        .icon2 { top: 50%; left: 80%; animation-delay: 2s; }
        .icon3 { top: 85%; left: 25%; animation-delay: 4s; }
        .icon4 { top: 30%; left: 60%; animation-delay: 6s; }
        .icon5 { top: 40%; left: 30%; animation-delay: 8s; }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .links a:hover {
            color: #218838;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-md-3 fixed-top">
    <div class="container-fluid py-4">
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
                    <a class="nav-link dropdown-toggle" href="#" id="activitiesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book"></i>
                        <span class="ms-2">Activities</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="activitiesDropdown">
                        <li><a class="dropdown-item" href="activities/activity_1/index.html"><i class="bi bi-1-circle"></i> Activity 1</a></li>
                        <li><a class="dropdown-item" href="activities/activity_2/index.html"><i class="bi bi-2-circle"></i> Activity 2</a></li>
                        <li><a class="dropdown-item" href="activities/activity_3/index.php"><i class="bi bi-3-circle"></i> Midterms</a></li>
                        <li><a class="dropdown-item" href="activities/certificates.php"><i class="bi bi-3-circle"></i> Certificates</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="login-container">
        <div class="row g-0">
            <div class="col-md-6 brand-side">
                <div class="text-center position-relative">
                    <img src="images/food_logo.png" alt="Logo" class="img-fluid mb-4" style="width: 120px;">
                    <h3 class="text-white mb-3">Food Pilferage Management</h3>
                    <p class="text-white-50">Secure Your Inventory, Maximize Efficiency</p>
                    <div class="credentials-box mt-4 p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px;">
                        <h5 class="text-white mb-3">Sample Login Credentials</h5>
                        <div class="text-start text-white-50">
                            <p class="mb-2"><strong>Administrator:</strong><br>
                            Username: deejay<br>
                            Access Code: admin-12112003</p>
                            <p class="mb-2"><strong>Inventory Staff:</strong><br>
                            Username: deejay<br>
                            Access Code: inventory-12112003</p>
                            <p class="mb-0"><strong>Kitchen Staff:</strong><br>
                            Username: mercygrace<br>
                            Access Code: kitchen-07052004</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 login-form">
                <div id="errorMessageLogin" class="alert alert-danger d-none"></div>
                <div id="successMessageLogin" class="alert alert-success d-none"></div>
                
                <h2 class="text-center mb-4">Welcome</h2>
                <form id="loginForm">
                    <div class="mb-4">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" 
                                class="form-control" 
                                name="username" 
                                placeholder="Enter your username" 
                                required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Access Code</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="text"
                                class="form-control"
                                name="access_code"
                                placeholder="Enter your access code"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </form>
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
</body>
</html>
