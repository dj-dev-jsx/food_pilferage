<?php
session_start();
include "../include/connect_db.php";

// Enable error logging
error_log("Add item process started");

if(isset($_POST['save_item'])) {
    // Log received POST data
    error_log("Received POST data: " . print_r($_POST, true));

    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $unit_price = $_POST['unit_price'];
    $stock_quantity = $_POST['stock_quantity'];
    $unit_of_measure = $_POST['unit_of_measure'];
    $category_id = $_POST['category_id'];
    $minimum_stock = $_POST['minimum_stock'] ?? 10;
    $user_id = $_SESSION['user_id'];

    // Log parsed values
    error_log("Parsed values - Category ID: $category_id, Minimum Stock: $minimum_stock");

    // Updated perishable categories to match new structure
    $perishableCategories = [1, 2]; // Meat Products and Vegetables
    $expiry_date = null;
   
    // Only set expiry_date if item is in perishable categories
    if(in_array($category_id, $perishableCategories)) {
        $expiry_date = $_POST['expiry_date'];
        if(empty($expiry_date)) {
            echo json_encode([
                'status' => 422,
                'message' => 'Expiry date is required for Meat Products and Vegetables!'
            ]);
            return;
        }
        $expiry_date = date('Y-m-d', strtotime($expiry_date));
    }

    // Auto-generate or validate item_id with logging
    if(empty($item_id)) {
        $getLastIdQuery = "SELECT item_id FROM items ORDER BY CAST(item_id AS UNSIGNED) DESC LIMIT 1";
        error_log("Executing query: $getLastIdQuery");
        
        $result = $conn->query($getLastIdQuery);
        
        if ($result->num_rows > 0) {
            $lastId = $result->fetch_assoc()['item_id'];
            $item_id = sprintf("%03d", intval($lastId) + 1);
            error_log("Generated new item_id: $item_id");
        } else {
            $item_id = "001";
            error_log("First item, using item_id: 001");
        }
    }

    // Status determination with logging
    $status_id = ($stock_quantity == 0) ? 3 : ($stock_quantity <= $minimum_stock ? 2 : 1);
    error_log("Determined status_id: $status_id based on stock_quantity: $stock_quantity and minimum_stock: $minimum_stock");

    $conn->begin_transaction();
    error_log("Transaction started");

    try {
        $query = "INSERT INTO items (
            item_id, item_name, unit_price, stock_quantity,
            unit_of_measure, category_id, expiration_date,
            status_id, minimum_stock
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        error_log("Prepared insert query: $query");
        error_log("Binding parameters: " . print_r([
            $item_id, $item_name, $unit_price, $stock_quantity,
            $unit_of_measure, $category_id, $expiry_date,
            $status_id, $minimum_stock
        ], true));

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdisisii",
            $item_id,
            $item_name,
            $unit_price,
            $stock_quantity,
            $unit_of_measure,
            $category_id,
            $expiry_date,
            $status_id,
            $minimum_stock
        );

        $stmt->execute();
        error_log("Item insert executed successfully");

        // Log entry
        $logQuery = "INSERT INTO inventory_logs (
            item_id, action_type, previous_value, new_value,
            user_id, timestamp
        ) VALUES (?, 'NEW_ITEM_ADDED', 0, ?, ?, NOW())";
        
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("sii", $item_id, $stock_quantity, $user_id);
        $logStmt->execute();
        error_log("Log entry created successfully");

        $conn->commit();
        error_log("Transaction committed successfully");
        
        echo json_encode([
            'status' => 200,
            'message' => 'Item Added Successfully!'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error occurred: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        echo json_encode([
            'status' => 500,
            'message' => 'Error: ' . $e->getMessage(),
            'debug_info' => [
                'query' => $query,
                'parameters' => [
                    'item_id' => $item_id,
                    'category_id' => $category_id,
                    'expiry_date' => $expiry_date
                ]
            ]
        ]);
    }
}
?>
