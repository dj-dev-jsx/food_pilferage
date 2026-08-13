
<?php
function logInventoryAction($conn, $item_id, $action_type, $previous_value, $new_value, $user_id) {
    $query = "INSERT INTO inventory_logs (item_id, action_type, previous_value, new_value, user_id) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi", $item_id, $action_type, $previous_value, $new_value, $user_id);
    return $stmt->execute();
}
function isAdmin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
}

