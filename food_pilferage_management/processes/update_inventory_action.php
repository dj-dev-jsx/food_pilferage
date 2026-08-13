<?php
include "../include/connect_db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $action_type = $_POST['action_type'];
    $quantity = $_POST['quantity'];
    $expiration_date = isset($_POST['expiration_date']) ? $_POST['expiration_date'] : null;

    $conn->begin_transaction();

    try {
        // Get current values
        $select_query = "SELECT di.*, i.stock_quantity, i.category_id
                        FROM daily_inventory di
                        JOIN items i ON di.item_id = i.item_id
                        WHERE di.item_id = ? AND DATE(di.record_date) = CURRENT_DATE()";
        $select_stmt = $conn->prepare($select_query);
        $select_stmt->bind_param("s", $item_id);
        $select_stmt->execute();
        $current_record = $select_stmt->get_result()->fetch_assoc();

        if (!$current_record) {
            throw new Exception('No daily inventory record found for today');
        }

        // Update daily inventory
        if ($action_type === 'addition') {
            $update_query = "UPDATE daily_inventory
                           SET additions = additions + ?,
                               expected_quantity = expected_quantity + ?
                           WHERE item_id = ? AND DATE(record_date) = CURRENT_DATE()";
        } else {
            $update_query = "UPDATE daily_inventory
                           SET usage_quantity = usage_quantity + ?,
                               expected_quantity = expected_quantity - ?
                           WHERE item_id = ? AND DATE(record_date) = CURRENT_DATE()";
        }
        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("dds", $quantity, $quantity, $item_id);
        $update_stmt->execute();

        // Update items table stock quantity and expiration date if provided
        $stock_update_query = "UPDATE items SET stock_quantity = stock_quantity " . 
                     ($action_type === 'addition' ? '+' : '-') . " ?";

        if ($expiration_date && $action_type === 'addition' && in_array($current_record['category_id'], [1, 2])) {
            $stock_update_query .= ", expiration_date = ? WHERE item_id = ?";
            $stock_stmt = $conn->prepare($stock_update_query);
            $stock_stmt->bind_param("dss", $quantity, $expiration_date, $item_id);
        } else {
            $stock_update_query .= " WHERE item_id = ?";
            $stock_stmt = $conn->prepare($stock_update_query);
            $stock_stmt->bind_param("ds", $quantity, $item_id);
        }
        $stock_stmt->execute();

        // Log the change
        $log_query = "INSERT INTO inventory_logs
                     (item_id, action_type, previous_value, new_value, user_id)
                     VALUES (?, ?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        $action = $action_type === 'addition' ? 'STOCK_ADDED' : 'STOCK_USED';
        $previous_stock = $current_record['stock_quantity'];
        $new_stock = $action_type === 'addition' ?
                    $previous_stock + $quantity :
                    $previous_stock - $quantity;
        
        $log_stmt->bind_param("sssdi",
            $item_id,
            $action,
            $previous_stock,
            $new_stock,
            $_SESSION['user_id']
        );
        $log_stmt->execute();

        $conn->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Inventory updated successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>
