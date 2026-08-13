<?php
session_start();
include "../include/connect_db.php";

if (!isset($_SESSION['user_id'])) {
    $res = [
        'status' => 401,
        'message' => 'Unauthorized: Please login to submit a report'
    ];
    echo json_encode($res);
    return false;
}

if (isset($_POST['submit_report'])) {
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : NULL;
    $user_id = $_SESSION['user_id'];
    $reported_quantity = isset($_POST['reported_quantity']) ? (int)$_POST['reported_quantity'] : NULL;
    $report_status_id = isset($_POST['report_status_id']) ? (int)$_POST['report_status_id'] : NULL;
    $description = isset($_POST['description']) ? trim($_POST['description']) : NULL;

    if (empty($item_id) || empty($reported_quantity) || empty($report_status_id) || empty($description)) {
        $res = [
            'status' => 422,
            'message' => 'All fields are mandatory'
        ];
        echo json_encode($res);
        return false;
    }

    $conn->begin_transaction();

    try {
        // Check if item exists and get current stock
        $check_item_query = "SELECT item_id, stock_quantity FROM items WHERE item_id = ?";
        $check_stmt = $conn->prepare($check_item_query);
        $check_stmt->bind_param("s", $item_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception('Invalid item_id: The specified item does not exist');
        }

        $item_data = $result->fetch_assoc();
        $current_stock = $item_data['stock_quantity'];
        $new_stock = $current_stock - $reported_quantity;

        // Insert pilferage report
        $query = "INSERT INTO pilferage_report (item_id, user_id, reported_quantity, date_reported, description, report_status_id)
                 VALUES (?, ?, ?, NOW(), ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siisi", $item_id, $user_id, $reported_quantity, $description, $report_status_id);
        $stmt->execute();

        // Update items table
        $updateItemsTable = "UPDATE items SET stock_quantity = ?, last_updated = NOW() WHERE item_id = ?";
        $updateStmt = $conn->prepare($updateItemsTable);
        $updateStmt->bind_param("is", $new_stock, $item_id);
        $updateStmt->execute();

        // Log the inventory change
        $logQuery = "INSERT INTO inventory_logs (item_id, action_type, previous_value, new_value, user_id, timestamp)
                    VALUES (?, 'PILFERAGE_DEDUCTION', ?, ?, ?, NOW())";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("siii", $item_id, $current_stock, $new_stock, $user_id);
        $logStmt->execute();

        $conn->commit();

        $res = [
            'status' => 200,
            'message' => 'Report added and inventory updated successfully'
        ];
        echo json_encode($res);

    } catch (Exception $e) {
        $conn->rollback();
        $res = [
            'status' => 500,
            'message' => 'Error: ' . $e->getMessage()
        ];
        echo json_encode($res);
    } finally {
        if (isset($check_stmt)) $check_stmt->close();
        if (isset($stmt)) $stmt->close();
        if (isset($updateStmt)) $updateStmt->close();
        if (isset($logStmt)) $logStmt->close();
    }

} else {
    $res = [
        'status' => 400,
        'message' => 'Invalid request'
    ];
    echo json_encode($res);
}
?>
