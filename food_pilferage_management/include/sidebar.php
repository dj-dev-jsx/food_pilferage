<style>
    .sidebar {
        background: linear-gradient(145deg, #28a745, #218838);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .nav-link {
        padding: 12px 20px;
        margin: 5px 15px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .nav-link:hover {
        background: rgba(255,255,255,0.15);
        transform: translateX(5px);
    }
    .nav-link.active {
        background: rgba(255,255,255,0.2) !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #fff;
        font-weight: 600;
    }
    .nav-link i {
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }
    .nav-link span {
        font-size: 0.95rem;
    }
    hr.text-white {
        opacity: 0.2;
        margin: 15px;
    }
    .nav-link.dropdown-toggle {
        color: white !important;
    }
    .dropdown-menu-dark {
        background: linear-gradient(145deg, #28a745, #218838);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .dropdown-item {
        color: rgba(255,255,255,0.9);
        transition: all 0.3s ease;
    }
    .dropdown-item:hover {
        background-color: rgba(255,255,255,0.15);
        color: white;
        transform: translateX(5px);
    }
    .nav-item.dropdown .dropdown-toggle::after {
        display: none;
    }
    @media (max-width: 768px) {
        .nav-item.dropdown .nav-link {
            padding: 12px 10px;
            margin: 5px 8px;
        }
        .dropdown-menu {
            margin-top: 0;
            margin-left: 5px;
        }
    }
</style>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div id="sidebar" class="col-md-2 sidebar">
    <div class="mt-4">
        <hr class="text-white">
        <ul class="nav flex-column">
            <?php if ($_SESSION['role_id'] == 3): // Kitchen Staff ?>
                <li class="nav-item">
                    <a href="inventory.php" class="nav-link text-white <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        <span class="ms-2">Inventory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="used_items.php" class="nav-link text-white <?php echo ($current_page == 'used_items.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="ms-2">Used Items</span>
                    </a>
                </li>
            <?php else: // Admin (1) and Inventory Staff (2) ?>
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span class="ms-2">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="inventory.php" class="nav-link text-white <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        <span class="ms-2">Inventory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="used_items.php" class="nav-link text-white <?php echo ($current_page == 'used_items.php') ? 'active' : ''; ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="ms-2">Used Items</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="inventory_logs.php" class="nav-link text-white <?php echo ($current_page == 'inventory_logs.php') ? 'active' : ''; ?>">
                        <i class="bi bi-journal-text"></i>
                        <span class="ms-2">Inventory Logs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pilferage_report.php" class="nav-link text-white <?php echo ($current_page == 'pilferage_report.php') ? 'active' : ''; ?>">
                        <i class="bi bi-flag"></i>
                        <span class="ms-2">Pilferage Report</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="waste.php" class="nav-link text-white <?php echo ($current_page == 'waste.php') ? 'active' : ''; ?>">
                        <i class="bi bi-recycle"></i>
                        <span class="ms-2">Wastes</span>
                    </a>
                </li>
                <?php if ($_SESSION['role_id'] == 1): // Admin only ?>
                    <li class="nav-item">
                        <a href="users.php" class="nav-link text-white <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                            <i class="bi bi-people"></i>
                            <span class="ms-2">Users</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <li class="nav-item">
                <hr class="bg-white">
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="activitiesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-book"></i>
                    <span class="ms-2">Activities</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="activitiesDropdown">
                    <li><a class="dropdown-item" href="activities/activity_1/index.html">Activity 1</a></li>
                    <li><a class="dropdown-item" href="activities/activity_2/index.html">Activity 2</a></li>
                    <li><a class="dropdown-item" href="activities/activity_3/index.php">Midterms</a></li>
                    <li><a class="dropdown-item" href="activities/certificates.php">Certificates</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
