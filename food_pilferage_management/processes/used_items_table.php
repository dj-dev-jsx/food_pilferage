<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

include "../include/connect_db.php";

if(isset($_POST['action'])) {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
    $offset = ($page - 1) * $items_per_page;

    $where_clause = "WHERE 1=1";
    
    // Search filter
    if(isset($_POST['search']) && !empty($_POST['search'])) {
        $search = mysqli_real_escape_string($conn, $_POST['search']);
        $where_clause .= " AND (i.item_name LIKE '%$search%' OR u.username LIKE '%$search%')";
    }

    // Date filter
    if(isset($_POST['date']) && !empty($_POST['date'])) {
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $where_clause .= " AND DATE(iu.usage_date) = '$date'";
    }

    // User filter
    if(isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $where_clause .= " AND iu.user_id = '$user_id'";
    }

    $count_query = "SELECT COUNT(*) as total 
                    FROM item_usage iu 
                    JOIN items i ON iu.item_id = i.item_id
                    JOIN users u ON iu.user_id = u.user_id
                    $where_clause";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];

    $query = "SELECT iu.*, i.item_name, i.unit_of_measure, u.username 
             FROM item_usage iu
             JOIN items i ON iu.item_id = i.item_id
             JOIN users u ON iu.user_id = u.user_id
             $where_clause
             ORDER BY iu.usage_date DESC
             LIMIT $offset, $items_per_page";

    $data = [
        'records' => getData($query),
        'total_records' => $total_records
    ];

    echo json_encode($data);
}

function getData($query) {
    global $conn;
    $output = "";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $output .= '<tr>
                <td class="fw-bold">'.$row['item_name'].'</td>
                <td>'.$row['quantity_used'].' '.$row['unit_of_measure'].'</td>
                <td>'.$row['description'].'</td>
                <td>₱'.number_format($row['total_cost'], 2).'</td>
                <td>'.$row['username'].'</td>
                <td>'.formatDateTime($row['usage_date']).'</td>
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info view-btn" data-id="'.$row['usage_id'].'" 
                                data-bs-toggle="tooltip" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row['usage_id'].'"
                                data-bs-toggle="tooltip" title="Delete Record">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>';
        }
    } else {
        $output = '<tr><td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No Used Items Found
                    </div>
                </td></tr>';
    }
    
    return $output;
}

function formatDateTime($datetime) {
    return date("M j, Y g:i A", strtotime($datetime));
}
?>
