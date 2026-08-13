<?php
include "../include/connect_db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $starting_quantity = $_POST['starting_quantity'];
    
    // Check if entry already exists for today
    $check_query = "SELECT record_id FROM daily_inventory 
                   WHERE item_id = ? AND DATE(record_date) = CURRENT_DATE()";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $item_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Day already started for this item'
        ]);
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Get current stock quantity for logging
        $current_stock_query = "SELECT stock_quantity FROM items WHERE item_id = ?";
        $stock_stmt = $conn->prepare($current_stock_query);
        $stock_stmt->bind_param("s", $item_id);
        $stock_stmt->execute();
        $current_stock = $stock_stmt->get_result()->fetch_assoc()['stock_quantity'];

        // Update items table stock quantity
        $update_stock = "UPDATE items SET stock_quantity = ? WHERE item_id = ?";
        $update_stmt = $conn->prepare($update_stock);
        $update_stmt->bind_param("ds", $starting_quantity, $item_id);
        $update_stmt->execute();

        // Insert daily inventory record
        $insert_query = "INSERT INTO daily_inventory 
                        (item_id, starting_quantity, expected_quantity, record_date) 
                        VALUES (?, ?, ?, CURRENT_DATE())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sdd", $item_id, $starting_quantity, $starting_quantity);
        $insert_stmt->execute();

        // Log the action with previous stock value
        $log_query = "INSERT INTO inventory_logs 
                     (item_id, action_type, previous_value, new_value, user_id) 
                     VALUES (?, 'START_DAY', ?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("sddi", $item_id, $current_stock, $starting_quantity, $_SESSION['user_id']);
        $log_stmt->execute();

        $conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Day started successfully'
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to start day'
        ]);
    }
}
