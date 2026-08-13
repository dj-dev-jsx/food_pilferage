<?php
include 'db_connect.php';

$problem_id = $_POST['problem_id'];
$solution_id = $_POST['solution_id'];
$new_order = $_POST['new_order'];

$update_query = "UPDATE solutions SET step_order = $new_order WHERE id = $solution_id AND problem_id = $problem_id";
mysqli_query($conn, $update_query);
?>
