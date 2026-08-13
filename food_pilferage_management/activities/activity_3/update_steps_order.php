<?php
include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$problem_id = $data['problem_id'];
$steps = $data['steps'];

foreach ($steps as $step) {
    $id = $step['id'];
    $order = $step['order'];
    $query = "UPDATE solutions SET step_order = $order WHERE id = $id AND problem_id = $problem_id";
    mysqli_query($conn, $query);
}
?>
