<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Pilferage Management - Certificates</title>
    <link rel="shortcut icon" href="images/food_logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #20c997;
        }

        .certificate-container {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            padding: 80px 20px;
        }

        .certificate-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }

        .certificate-card:hover {
            transform: translateY(-5px);
        }

        .certificate-image {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%;
        }

        .certificate-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .certificate-card:hover .certificate-image img {
            transform: scale(1.05);
        }

        .certificate-content {
            padding: 20px;
        }

        .certificate-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .certificate-date {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .certificate-description {
            margin-top: 10px;
            color: #495057;
        }

        .view-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .view-button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

    <div class="certificate-container">
        <div class="container">
            <h2 class="text-white text-center mb-5">My Certificates</h2>
            <div class="row">
                <!-- Certificate Card 1 -->
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/1.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">Responsive Web Design With Bootstrap 5</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/2.jpg" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">Introduction to Game Development with HTML5 and JavaScript</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/3.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">HTML5 Game Development Gameplay and Multiplayer Proof of Concept Course </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/4.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">Interactive Games using HTML 5 and JavaScript</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/5.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">Creating a Shooting Game using HTML5 Canvas and JavaScript </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/6.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">Diploma in HTML5, CSS3 and JavaScript </h4>
                        </div>
                    </div>
                </div>

                <!-- Add more certificate cards as needed -->
            </div>
            <h2 class="text-white text-center mb-5">Assessments</h2>
            <div class="row">
                <!-- Certificate Card 1 -->
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/ass1.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">
                            <h4 class="certificate-title">Introduction to Game Development with HTML5 and JavaScript. Assessment 1</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="certificate-card">
                        <div class="certificate-image">
                            <img src="certs/ass2.png" alt="Certificate 1">
                        </div>
                        <div class="certificate-content">   
                            <h4 class="certificate-title">Introduction to Game Development with HTML5 and JavaScript. Assessment 2</h4>
                        </div>
                    </div>
                </div>
                <!-- Add more certificate cards as needed -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
