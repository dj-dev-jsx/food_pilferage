<?php
include "../include/connect_db.php";
session_start();

ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $actual_quantity = $_POST['actual_quantity'];
    
    $conn->begin_transaction();
    
    try {
        // Get current values first
        $get_current = "SELECT di.*, i.item_name, i.unit_of_measure 
                       FROM daily_inventory di
                       JOIN items i ON di.item_id = i.item_id
                       WHERE di.item_id = ? AND DATE(record_date) = CURRENT_DATE()";
        $stmt_current = $conn->prepare($get_current);
        $stmt_current->bind_param("s", $item_id);
        $stmt_current->execute();
        $current_record = $stmt_current->get_result()->fetch_assoc();

        // Update daily inventory
        $update_query = "UPDATE daily_inventory 
                        SET actual_quantity = ?,
                            discrepancy = expected_quantity - ?
                        WHERE item_id = ? AND DATE(record_date) = CURRENT_DATE()";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("dds", $actual_quantity, $actual_quantity, $item_id);
        $stmt->execute();

        // Log the end day count
        $log_query = "INSERT INTO inventory_logs 
                     (item_id, action_type, previous_value, new_value, user_id)
                     VALUES (?, 'END_DAY_COUNT', ?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("sddi", 
            $item_id,
            $current_record['expected_quantity'],
            $actual_quantity,
            $_SESSION['user_id']
        );
        $log_stmt->execute();

        $conn->commit();
        
        $response = [
            'status' => 'success',
            'discrepancy' => floatval($current_record['expected_quantity'] - $actual_quantity),
            'unit' => $current_record['unit_of_measure'],
            'item_name' => $current_record['item_name'],
            'message' => 'End day count recorded successfully'
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        $response = [
            'status' => 'error',
            'message' => 'Failed to record end day count'
        ];
    }
    
    exit(json_encode($response));
}
