<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="food_pilferage_management/images/food_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-md-3 fixed-top">
    <div class="container-fluid py-4">
        <a class="navbar-brand" href="#">
            <img src="food_pilferage_management/images/food_logo.png" alt="Logo" height="40">
            Food Pilferage Management
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#process">How It Works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#faq">F.A.Q</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="activitiesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book"></i>
                        <span class="ms-2">Activities</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="activitiesDropdown">
                        <li><a class="dropdown-item" href="food_pilferage_management/activities/activity_1/index.html"><i class="bi bi-1-circle"></i> Laboratory 1</a></li>
                        <li><a class="dropdown-item" href="food_pilferage_management/activities/activity_2/index.html"><i class="bi bi-2-circle"></i> Laboratory 2</a></li>
                        <li><a class="dropdown-item" href="food_pilferage_management/activities/activity_3/index.php"><i class="bi bi-3-circle"></i> Midterms</a></li>
                        <li><a class="dropdown-item" href="food_pilferage_management/activities/certificates.php"><i class="bi bi-3-circle"></i> Certificates</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="btn btn-success ms-2" href="food_pilferage_management/login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>



    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <i class="fas fa-carrot food-icon icon1"></i>
        <i class="fas fa-apple-alt food-icon icon2"></i>
        <i class="fas fa-drumstick-bite food-icon icon3"></i>
        <i class="fas fa-fish food-icon icon4"></i>
        <div class="container">
            <div class="hero-content text-center text-white" data-aos="fade-up">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="bi bi-shield-check"></i> Food Pilferage Management System
                </h1>
                <p class="lead mb-4">Secure your inventory, prevent losses, and maximize profitability with our comprehensive management solution.</p>
                <a href="food_pilferage_management/login.php" class="btn btn-get-started">
                    <i class="bi bi-arrow-right-circle me-2"></i> Get Started
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3" data-aos="fade-up">
                    <div class="stat-card">
                        <i class="bi bi-box-seam display-4 mb-3"></i>
                        <h3>Inventory</h3>
                        <p>Real-time Stock Monitoring</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <i class="bi bi-graph-down-arrow display-4 mb-3"></i>
                        <h3>Loss Prevention</h3>
                        <p>Minimize Food Wastage</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <i class="bi bi-clipboard-data display-4 mb-3"></i>
                        <h3>Reports</h3>
                        <p>Detailed Analytics</p>
                    </div>
                </div>
                <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <i class="bi bi-shield-lock display-4 mb-3"></i>
                        <h3>Security</h3>
                        <p>Access Control System</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section" id="process">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="process-card text-center">
                        <i class="bi bi-shield-lock process-icon"></i>
                        <h4>Secure Access</h4>
                        <p>Role-based authentication system for controlled access</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="process-card text-center">
                        <i class="bi bi-graph-up process-icon"></i>
                        <h4>Track & Monitor</h4>
                        <p>Real-time inventory tracking and loss prevention</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="process-card text-center">
                        <i class="bi bi-file-earmark-text process-icon"></i>
                        <h4>Generate Reports</h4>
                        <p>Comprehensive reporting and analytics tools</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Key Features</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="feature-card text-center">
                        <i class="bi bi-clock-history feature-icon"></i>
                        <h5>Real-time Tracking</h5>
                        <p>Monitor inventory movements as they happen</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center">
                        <i class="bi bi-graph-up-arrow feature-icon"></i>
                        <h5>Analytics Dashboard</h5>
                        <p>Comprehensive data visualization and insights</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card text-center">
                        <i class="bi bi-shield-check feature-icon"></i>
                        <h5>Security Controls</h5>
                        <p>Advanced security measures to prevent unauthorized access</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FAQ Section -->
    <section class="faq-section py-5 bg-light" id="faq">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Frequently Asked Questions</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                                    How does the inventory tracking system work?
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Our system provides real-time monitoring of food inventory through a user-friendly dashboard. It tracks stock levels, movements, and generates automatic alerts for unusual patterns.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                                    What types of reports can be generated?
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    The system generates comprehensive reports including inventory status, pilferage incidents, stock movement history, and trend analysis with detailed visualizations.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                                    How secure is the system?
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We implement role-based access control, secure authentication, and maintain detailed audit logs of all system activities to ensure maximum security.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4">
                                    Can multiple users access the system simultaneously?
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, the system supports multiple concurrent users with different access levels based on their roles and responsibilities.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <?php include "food_pilferage_management/include/footer.php";?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 1000,
            once: true
        });

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
