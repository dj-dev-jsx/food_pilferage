<?php
include "../include/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    
    $query = "SELECT item_id, item_name, unit_price, stock_quantity, unit_of_measure 
              FROM items 
              WHERE item_id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    }
}
