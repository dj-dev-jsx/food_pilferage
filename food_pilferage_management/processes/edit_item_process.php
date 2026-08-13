<?php
error_reporting(0);
ini_set('display_errors', 0);
include '../include/connect_db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'fetch') {
        $itemId = $_POST['itemId'];
        $query = "SELECT * FROM items WHERE item_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'Item not found']);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $itemId = $_POST['edit_item_id'];
        $itemName = $_POST['edit_item_name'];
        $unitPrice = $_POST['edit_unit_price'];
        $categoryId = $_POST['edit_category_id'];
        $stockQuantity = $_POST['edit_stock_quantity'];
        $unitOfMeasure = $_POST['edit_unit_of_measure'];
        $expiryDate = $_POST['edit_expiry_date'] ?: null;
        $user_id = $_SESSION['user_id'];

        if ($stockQuantity == 0) {
            $status_id = 3;
        } elseif ($stockQuantity <= 10) {
            $status_id = 2;
        } else {
            $status_id = 1;
        }

        $conn->begin_transaction();

        try {
            // Get current item data
            $getCurrentData = "SELECT * FROM items WHERE item_id = ?";
            $dataStmt = $conn->prepare($getCurrentData);
            $dataStmt->bind_param("s", $itemId);
            $dataStmt->execute();
            $currentData = $dataStmt->get_result()->fetch_assoc();

            // Update item
            $query = "UPDATE items SET item_name = ?, unit_price = ?, category_id = ?, stock_quantity = ?, unit_of_measure = ?, expiration_date = ?, status_id = ?, last_updated = NOW() WHERE item_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sdiissis", $itemName, $unitPrice, $categoryId, $stockQuantity, $unitOfMeasure, $expiryDate, $status_id, $itemId);
            $stmt->execute();

            // Convert stock quantities to integers for accurate comparison
            $currentStock = (int)$currentData['stock_quantity'];
            $newStock = (int)$stockQuantity;

            // Log stock changes only if quantity changed
            if ($currentStock !== $newStock) {
                $logStmt = $conn->prepare("INSERT INTO inventory_logs (item_id, action_type, previous_value, new_value, user_id, timestamp) VALUES (?, 'STOCK_UPDATED', ?, ?, ?, NOW())");
                $logStmt->bind_param("siii", $itemId, $currentStock, $newStock, $user_id);
                $logStmt->execute();
            }

            // Log other changes as ITEM_UPDATED
            if ($currentData['item_name'] !== $itemName ||
                (float)$currentData['unit_price'] !== (float)$unitPrice ||
                (int)$currentData['category_id'] !== (int)$categoryId ||
                $currentData['unit_of_measure'] !== $unitOfMeasure ||
                $currentData['expiration_date'] !== $expiryDate) {
                
                $logStmt = $conn->prepare("INSERT INTO inventory_logs (item_id, action_type, previous_value, new_value, user_id, timestamp) VALUES (?, 'ITEM_UPDATED', 0, 0, ?, NOW())");
                $logStmt->bind_param("si", $itemId, $user_id);
                $logStmt->execute();
            }

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Item updated successfully']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Failed to update item: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
?>
