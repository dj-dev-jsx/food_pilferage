<?php

include "include/connect_db.php";

function handleExpiredItems($conn) {
    // Get expired perishable items
    $expired_query = "SELECT i.*, c.category_name 
                     FROM items i 
                     JOIN categories c ON i.category_id = c.category_id
                     WHERE i.expiration_date <= CURDATE() 
                     AND i.is_deleted = 0 
                     AND i.stock_quantity > 0
                     AND i.category_id IN (1,2)";
    
    $expired_result = $conn->query($expired_query);
    
    while($item = $expired_result->fetch_assoc()) {
        mysqli_begin_transaction($conn);
        
        try {
            $total_cost = $item['stock_quantity'] * $item['unit_price'];
            $reason = "Expired on " . date('Y-m-d', strtotime($item['expiration_date']));
            
            // Insert into wastes table (using the correct table name from your schema)
            $waste_query = "INSERT INTO wastes (item_id, quantity, total_cost, user_id, reason) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($waste_query);
            $stmt->bind_param("sddis", 
                $item['item_id'], 
                $item['stock_quantity'], 
                $total_cost,
                $_SESSION['user_id'],
                $reason
            );
            $stmt->execute();
            
            // Log the action in inventory_logs
            $log_query = "INSERT INTO inventory_logs 
                        (item_id, action_type, previous_value, new_value, user_id) 
                        VALUES (?, 'EXPIRED_WASTE', ?, '0', ?)";
            $stmt = $conn->prepare($log_query);
            $stmt->bind_param("sdi", 
                $item['item_id'], 
                $item['stock_quantity'], 
                $_SESSION['user_id']
            );
            $stmt->execute();
            
            // Update items table
            $update_query = "UPDATE items 
                           SET stock_quantity = 0, 
                               status_id = 3 
                           WHERE item_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("s", $item['item_id']);
            $stmt->execute();
            
            mysqli_commit($conn);
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            error_log("Error processing expired item {$item['item_id']}: " . $e->getMessage());
        }
    }
}

handleExpiredItems($conn);
