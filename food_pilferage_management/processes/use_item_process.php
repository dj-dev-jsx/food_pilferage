<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

include "../include/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => ''];
    
    try {
        $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
        $quantity = floatval($_POST['quantity']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $total_cost = floatval($_POST['total_cost']);
        $user_id = $_SESSION['user_id'];
        
        mysqli_begin_transaction($conn);
        
        // Get current stock quantity
        $current_query = "SELECT stock_quantity FROM items WHERE item_id = ?";
        $stmt = $conn->prepare($current_query);
        $stmt->bind_param("s", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_stock = $result->fetch_assoc()['stock_quantity'];
        
        // Calculate new quantity
        $new_quantity = $current_stock - $quantity;
        
        // Update inventory quantity with status check
        $update_query = "UPDATE items SET
                        stock_quantity = ?,
                        status_id = CASE
                            WHEN ? <= 10 THEN 2
                            WHEN ? <= 0 THEN 3
                            ELSE 1
                        END
                        WHERE item_id = ?";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ddds", $new_quantity, $new_quantity, $new_quantity, $item_id);
        $stmt->execute();
        
        // Record usage
        $usage_query = "INSERT INTO item_usage
        (item_id, user_id, quantity_used, description, total_cost)
        VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($usage_query);
        $stmt->bind_param("sidsd", $item_id, $user_id, $quantity, $description, $total_cost);
        $stmt->execute();
        
        // Log the action with correct previous and new values
        $log_query = "INSERT INTO inventory_logs
                    (item_id, action_type, previous_value, new_value, user_id)
                    VALUES (?, 'ITEM_USED', ?, ?, ?)";
        $stmt = $conn->prepare($log_query);
        $stmt->bind_param("sddi", $item_id, $current_stock, $new_quantity, $user_id);
        $stmt->execute();
        
        mysqli_commit($conn);
        
        $response['status'] = 'success';
        $response['message'] = 'Item used successfully';
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
}
