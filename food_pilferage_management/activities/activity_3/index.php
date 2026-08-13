<?php
$conn = new mysqli('localhost', 'root', '', 'food_pilferage_db');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
   
    $sql = "INSERT INTO problems (title, description)
            VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $description);
    $stmt->execute();
    
    // Get the ID of the newly inserted problem
    $problem_id = $stmt->insert_id;
    
    // Redirect to solution.php with the problem ID
    header("Location: solution.php?id=" . $problem_id);
    exit();
}

$result = $conn->query("SELECT * FROM problems ORDER BY date_added DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problem Solving Tracker</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body class="bg-success">
    <div class="container mt-5 bg-dark">
        <h1 class="mb-4 text-white">Problem Solving Tracker</h1>
        
        <!-- Add New Problem Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Problem</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Problem Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Problem Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Problem</button>
                </form>
            </div>
        </div>

        <!-- Problem List -->
        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?= date('F j, Y', strtotime($row['date_added'])) ?>
                        </h6>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="bootstrap.min.bundle.js"></script>
</body>
</html>
