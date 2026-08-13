<?php
error_reporting(0);
ini_set('display_errors', 0);
include '../include/connect_db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $itemId = $_POST['itemId'];
    $user_id = $_SESSION['user_id'];

    $conn->begin_transaction();

    try {
        // Get current stock before soft deletion
        $getStockQuery = "SELECT stock_quantity FROM items WHERE item_id = ?";
        $stockStmt = $conn->prepare($getStockQuery);
        $stockStmt->bind_param("s", $itemId);
        $stockStmt->execute();
        $result = $stockStmt->get_result();
        $currentStock = $result->fetch_assoc()['stock_quantity'];

        // Log the deletion
        $logQuery = "INSERT INTO inventory_logs (item_id, action_type, previous_value, new_value, user_id, timestamp)
                    VALUES (?, 'ITEM_DELETED', ?, 0, ?, NOW())";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("sii", $itemId, $currentStock, $user_id);
        $logStmt->execute();

        // Soft delete the item
        $updateQuery = "UPDATE items SET is_deleted = 1 WHERE item_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("s", $itemId);
        $updateStmt->execute();

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete item: ' . $e->getMessage()]);
    }
}


$conn->close();
?>
