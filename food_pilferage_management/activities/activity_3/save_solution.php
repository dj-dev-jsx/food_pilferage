<?php
include 'db_connect.php';

$problem_id = $_POST['problem_id'];
$solution_text = $_POST['solution_text'];

// Get the next step order number
$query = "SELECT MAX(step_order) as max_order FROM solutions WHERE problem_id = $problem_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$next_order = ($row['max_order'] ?? 0) + 1;

$insert_query = "INSERT INTO solutions (problem_id, description, step_order) VALUES ($problem_id, '$solution_text', $next_order)";
mysqli_query($conn, $insert_query);

header("Location: solution.php?id=$problem_id");
?>
