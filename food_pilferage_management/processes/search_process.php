<?php
header('Content-Type: application/json');
include "../include/connect_db.php";
include "inventory_table.php";

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
$offset = ($page - 1) * $items_per_page;

$baseSelect = "SELECT items.*, status.status, categories.category_name,
    CASE 
        WHEN categories.category_id IN (1, 2) THEN 'Perishable'
        ELSE 'Non-Perishable'
    END as perishable_status";

$baseJoin = "FROM items
    JOIN status ON items.status_id = status.status_id
    LEFT JOIN categories ON items.category_id = categories.category_id";

if($_POST['action'] == 'searchTable') {
    $search_inp = mysqli_real_escape_string($conn, $_POST['search_inp']);
    $where = "WHERE items.is_deleted = 0 AND item_name LIKE '%$search_inp%'";
    
    $count_query = "SELECT COUNT(*) as total $baseJoin $where";
    $query = "$baseSelect $baseJoin $where 
              ORDER BY perishable_status, categories.category_name, items.item_name ASC 
              LIMIT $offset, $items_per_page";
}

if($_POST['action'] == 'statusFilter') {
    $status_id = mysqli_real_escape_string($conn, $_POST['status_id']);
    $where = "WHERE items.is_deleted = 0 AND items.status_id = '$status_id'";
    
    $count_query = "SELECT COUNT(*) as total $baseJoin $where";
    $query = "$baseSelect $baseJoin $where 
              ORDER BY perishable_status, categories.category_name, items.item_name ASC 
              LIMIT $offset, $items_per_page";
}

if($_POST['action'] == 'categoryFilter') {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $where = "WHERE items.is_deleted = 0 AND items.category_id = '$category_id'";
    
    $count_query = "SELECT COUNT(*) as total $baseJoin $where";
    $query = "$baseSelect $baseJoin $where 
              ORDER BY perishable_status, categories.category_name, items.item_name ASC 
              LIMIT $offset, $items_per_page";
}

if($_POST['action'] == 'combinedFilter') {
    $status_id = mysqli_real_escape_string($conn, $_POST['status_id']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    
    $conditions = ["items.is_deleted = 0"];
    if($status_id != '') {
        $conditions[] = "items.status_id = '$status_id'";
    }
    if($category_id != '') {
        $conditions[] = "items.category_id = '$category_id'";
    }
    
    $where = "WHERE " . implode(' AND ', $conditions);
    
    $count_query = "SELECT COUNT(*) as total $baseJoin $where";
    $query = "$baseSelect $baseJoin $where 
              ORDER BY perishable_status, categories.category_name, items.item_name ASC 
              LIMIT $offset, $items_per_page";
}

$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];

$records = getData($query);

echo json_encode([
    'records' => $records,
    'total_records' => $total_records
]);
?>
