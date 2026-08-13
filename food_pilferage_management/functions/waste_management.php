<?php
function processExpiredItems($conn) {
    $expired_query = "SELECT i.*, c.category_name 
                     FROM items i 
                     JOIN categories c ON i.category_id = c.category_id
                     WHERE i.expiration_date <= CURDATE() 
                     AND i.is_deleted = 0 
                     AND i.stock_quantity > 0
                     AND i.category_id IN (1,2)";
    
    return $conn->query($expired_query);
}

?>