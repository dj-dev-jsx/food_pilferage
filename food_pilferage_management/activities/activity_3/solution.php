<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problem Solutions</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .solution-item {
            cursor: move;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            background: white;
        }
        .step-number {
            font-weight: bold;
            margin-right: 10px;
        }
        .solution-item {
            cursor: move;
            padding: 10px;
            margin: 5px 0;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .solution-item.dragging {
            opacity: 0.5;
            background: #e9ecef;
        }
    </style>
</head>
<body class="bg-success">
    <div class="container mt-4 bg-dark p-5">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Problem Description</h3>
            </div>
            <div class="card-body">
                <?php
                    
                    include 'db_connect.php';

                    // Get problem ID with validation
                    $problem_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                    // Verify problem exists
                    $query = "SELECT * FROM problems WHERE id = $problem_id";
                    $result = mysqli_query($conn, $query);

                    if (!$result || mysqli_num_rows($result) == 0) {
                        echo "<div class='alert alert-danger'>Problem not found</div>";
                        exit();
                    }

                    $row = mysqli_fetch_assoc($result);
                    echo "<p class='problem-description'>".$row['description']."</p>";
                    ?>
                </div>
            </div>
        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addSolutionModal">
            Add Solution Step
        </button>

        <div class="card">
        <div class="card-header">
            <h4>Step by Step Solutions</h4>
        </div>
        <div class="card-body">
            <div id="solutionsList" class="solutions-container">
                <?php
                    $solutions_query = "SELECT * FROM solutions WHERE problem_id = $problem_id ORDER BY step_order";
                    $solutions_result = mysqli_query($conn, $solutions_query);
                    while($solution = mysqli_fetch_assoc($solutions_result)) {
                        echo "<div class='solution-item' draggable='true' data-id='".$solution['id']."'>";
                        echo "<span class='step-number'>".$solution['step_order'].".</span> ";
                        echo $solution['description'];
                        echo "</div>";
                    }
                ?>
            </div>
        </div>
    </div>

    </div>

    <div class="modal fade" id="addSolutionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Solution Step</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="solutionForm" method="POST" action="save_solution.php">
                        <input type="hidden" name="problem_id" value="<?php echo $problem_id; ?>">
                        <div class="mb-3">
                            <label for="solutionText" class="form-label">Solution Description</label>
                            <textarea class="form-control" id="solutionText" name="solution_text" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Solution</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.js"></script>
    <script>
        const solutionsList = document.getElementById('solutionsList');
        const items = solutionsList.getElementsByClassName('solution-item');

        for (let item of items) {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('drop', handleDrop);
        }

        let draggedItem = null;

        function handleDragStart(e) {
            draggedItem = this;
            this.classList.add('dragging');
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
        }

        function handleDragOver(e) {
            e.preventDefault();
        }

        function handleDrop(e) {
            e.preventDefault();
            if (this !== draggedItem) {
                let allItems = [...items];
                let draggedIndex = allItems.indexOf(draggedItem);
                let droppedIndex = allItems.indexOf(this);

                if (draggedIndex < droppedIndex) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }

                updateStepNumbers();
                saveNewOrder();
            }
        }

        function updateStepNumbers() {
            Array.from(items).forEach((item, index) => {
                item.querySelector('.step-number').textContent = (index + 1) + '.';
            });
        }

        function saveNewOrder() {
            const newOrder = Array.from(items).map((item, index) => ({
                id: item.dataset.id,
                order: index + 1
            }));

            fetch('update_steps_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    problem_id: <?php echo $problem_id; ?>,
                    steps: newOrder
                })
            });
        }
    </script>
</body>
</html>