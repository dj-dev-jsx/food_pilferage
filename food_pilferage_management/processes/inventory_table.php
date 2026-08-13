<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

include "../include/connect_db.php";

if(isset($_POST['action']) && $_POST['action'] == 'fetchData') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
    $offset = ($page - 1) * $items_per_page;

    $count_query = "SELECT COUNT(*) as total FROM items WHERE is_deleted = 0";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];

    $query = "SELECT items.*, status.status, categories.category_name,
    CASE
        WHEN categories.category_id IN (1, 2) THEN 'Perishable'
        ELSE 'Non-Perishable'
    END as perishable_status
    FROM items
    JOIN status ON items.status_id = status.status_id
    LEFT JOIN categories ON items.category_id = categories.category_id
    WHERE items.is_deleted = 0
    ORDER BY perishable_status, items.item_name ASC
    LIMIT $offset, $items_per_page";

    $data = [
        'records' => getData($query),
        'total_records' => $total_records
    ];

    echo json_encode($data);
}

function getData($query) {
    global $conn;
    $categoryClasses = [
        'Meat Products' => 'bg-danger',
        'Vegetables' => 'bg-success',
        'Seasonings' => 'bg-warning',
        'Dry Goods' => 'bg-info',
        'Beverages' => 'bg-primary'
    ];
   
    $output = "";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        $currentPerishableStatus = '';
       
        while($row = mysqli_fetch_assoc($result)) {
            if($currentPerishableStatus != $row['perishable_status']) {
                $currentPerishableStatus = $row['perishable_status'];
                $headerIcon = ($currentPerishableStatus == 'Perishable') ? 'bi-clock-history' : 'bi-infinity';
                $headerClass = ($currentPerishableStatus == 'Perishable') ? 'bg-danger' : 'bg-success';
               
                $output .= '<tr>
                    <td colspan="10" class="fw-bold '.$headerClass.' text-white p-3">
                        <i class="bi '.$headerIcon.' me-2"></i> ' . $currentPerishableStatus . ' Items
                    </td>
                </tr>';
            }

            $formattedDateTime = ($row['expiration_date'])
                ? date("F j, Y", strtotime($row['expiration_date']))
                : "N/A";

            $statusClass = getStatusClass($row['status']);
           
            $output .= '<tr>
                <td>'.$row['item_id'].'</td>
                <td class="fw-bold">'.$row['item_name'].'</td>
                <td>₱'.number_format($row['unit_price'], 2).'</td>
                <td><span class="badge '.$categoryClasses[$row['category_name']].'">'.$row['category_name'].'</span></td>
                <td class="text-center">'.$row['stock_quantity'].'</td>
                <td class="text-center"><span class="badge '.$statusClass.'">'.$row['status'].'</span></td>
                <td>'.$row['unit_of_measure'].'</td>
                <td>'.$formattedDateTime.'</td>
                <td>'.formatDateTime($row['last_updated']).'</td>
                <td class="text-center">
                    <div class="inventory-btn-group">';
            
            if ($_SESSION['role_id'] == 3) { // Kitchen Staff
                $output .= '<button class="btn btn-sm btn-success use-item-btn" data-id="'.$row['item_id'].'" data-bs-toggle="modal" data-bs-target="#use-modal">
                    <i class="bi bi-box-arrow-right"></i> Use
                </button>';
            } else { // Admin and Inventory Staff
                $output .= '<button class="btn btn-sm btn-primary edit-btn" data-id="'.$row['item_id'].'" data-bs-toggle="modal" data-bs-target="#edit-modal">
                    <i class="bi bi-pencil"></i>
                </button>
                <div class="inventory-dropdown">
                    <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="inventoryDropdown'.$row['item_id'].'" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                    </button>
                    <ul class="inventory-dropdown-menu dropdown-menu dropdown-menu-end" aria-labelledby="inventoryDropdown'.$row['item_id'].'">
                        <li><button class="inventory-dropdown-item dropdown-item" data-id="'.$row['item_id'].'" data-stock="'.$row['stock_quantity'].'">
                            <i class="bi bi-sunrise me-2"></i>Start Day
                        </button></li>
                        <li><button class="inventory-dropdown-item dropdown-item" data-id="'.$row['item_id'].'">
                            <i class="bi bi-arrow-repeat me-2"></i>Update Stock
                        </button></li>
                        <li><button class="inventory-dropdown-item dropdown-item" data-id="'.$row['item_id'].'">
                            <i class="bi bi-sunset me-2"></i>End Day
                        </button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="inventory-dropdown-item use-item-btn  dropdown-item" data-id="'.$row['item_id'].'" data-bs-toggle="modal" data-bs-target="#use-modal">
                            <i class="bi bi-box-arrow-right me-2"></i>Use Item
                        </button></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row['item_id'].'">
                    <i class="bi bi-trash"></i>
                </button>';
            }
            
            $output .= '</div></td></tr>';
        }
    } else {
        $output = '<tr><td colspan="10" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No Items Found
                    </div>
                </td></tr>';
    }
   
    return $output;
}

function getStatusClass($status) {
    $statusClasses = [
        'In Stock' => 'bg-success p-2',
        'Low Stock' => 'bg-warning p-2',
        'Out of Stock' => 'bg-danger p-2',
        'Expired' => 'bg-secondary p-2'
    ];
    return $statusClasses[$status] ?? 'bg-primary';
}

function formatDateTime($datetime) {
    return date("M j, Y g:i A", strtotime($datetime));
}
?>
