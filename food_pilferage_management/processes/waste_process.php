<?php
include "include/connect_db.php";

function handleExpiredItems($conn) {
    $expired_query = "SELECT i.*, c.category_name
                     FROM items i
                     JOIN categories c ON i.category_id = c.category_id
                     WHERE i.expiration_date <= CURDATE()
                     AND i.is_deleted = 0
                    AND i.stock_quantity > 0
                     AND i.category_id IN (1,2)"; // Meat Products and Vegetables
    
    $expired_result = $conn->query($expired_query);
    
    while($item = $expired_result->fetch_assoc()) {
        mysqli_begin_transaction($conn);
        
        try {
            $total_cost = $item['stock_quantity'] * $item['unit_price'];
            $reason = "Expired on " . date('Y-m-d', strtotime($item['expiration_date']));
            
            // Insert into wastes table
            $waste_query = "INSERT INTO wastes (item_id, quantity, reason, total_cost, user_id)
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($waste_query);
            $stmt->bind_param("sdsdi",
                $item['item_id'],
                $item['stock_quantity'],
                $reason,
                $total_cost,
                $_SESSION['user_id']
            );
            $stmt->execute();
            
            // Log the action
            $log_query = "INSERT INTO inventory_logs
                        (item_id, action_type, previous_value, new_value, user_id)
                        VALUES (?, 'EXPIRED_WASTE', ?, 0, ?)";
            $stmt = $conn->prepare($log_query);
            $stmt->bind_param("sdi",
                $item['item_id'],
                $item['stock_quantity'],
                $_SESSION['user_id']
            );
            $stmt->execute();
            
            // Update inventory - status_id 3 is 'Out of Stock'
            $update_query = "UPDATE items
                           SET stock_quantity = 0,
                               status_id = 3
                           WHERE item_id = ?";
            $stmt = $conn->prepare($stmt);
            $stmt->bind_param("s", $item['item_id']);
            $stmt->execute();
            
            mysqli_commit($conn);
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            error_log("Error processing expired item {$item['item_id']}: " . $e->getMessage());
        }
    }
}

// Function to display waste records
function getWasteRecords($conn) {
    $query = "SELECT w.*, i.item_name, u.username
            FROM wastes w
            JOIN items i ON w.item_id = i.item_id
            JOIN users u ON w.user_id = u.user_id
            ORDER BY w.timestamp DESC";
    return $conn->query($query);
}

// Run the process
handleExpiredItems($conn);
