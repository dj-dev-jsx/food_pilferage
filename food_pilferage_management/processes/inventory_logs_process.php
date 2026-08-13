<?php
session_start();
include "../include/connect_db.php";

if(isset($_POST['action'])) {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
    $offset = ($page - 1) * $items_per_page;

    $where_conditions = [];
    
    // Enhanced search with multiple fields
    if (!empty($_POST['search_term'])) {
        $search_term = mysqli_real_escape_string($conn, $_POST['search_term']);
        $where_conditions[] = "(
            COALESCE(i.item_name, '') LIKE '%$search_term%' OR 
            il.action_type LIKE '%$search_term%' OR 
            u.username LIKE '%$search_term%'
        )";
    }
    
    // Date range filter
    if (!empty($_POST['date_filter'])) {
        $date = mysqli_real_escape_string($conn, $_POST['date_filter']);
        $where_conditions[] = "DATE(il.timestamp) = '$date'";
    }
    
    // Action type filter with validation
    if (!empty($_POST['action_filter']) && $_POST['action_filter'] !== 'all') {
        $valid_actions = ['NEW_ITEM_ADDED', 'STOCK_UPDATED', 'ITEM_UPDATED', 'ITEM_DELETED', 'PILFERAGE_DEDUCTION'];
        $action = mysqli_real_escape_string($conn, $_POST['action_filter']);
        if(in_array($action, $valid_actions)) {
            $where_conditions[] = "il.action_type = '$action'";
        }
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    // Optimized count query
    $count_query = "SELECT COUNT(*) as total
                    FROM inventory_logs il
                    LEFT JOIN items i ON il.item_id = i.item_id
                    LEFT JOIN users u ON il.user_id = u.user_id
                    $where_clause";
    
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];

    // Main data query with additional fields
    $query = "SELECT 
                il.*,
                i.item_name,
                i.unit_of_measure,
                COALESCE(u.username, 'System') as username,
                i.unit_price
             FROM inventory_logs il
             LEFT JOIN items i ON il.item_id = i.item_id
             LEFT JOIN users u ON il.user_id = u.user_id
             $where_clause
             ORDER BY il.timestamp DESC
             LIMIT $offset, $items_per_page";

    $data = [
        'records' => getLogRecords($query),
        'total_records' => $total_records
    ];
    
    echo json_encode($data);
}

function getLogRecords($query) {
    global $conn;
    $output = "";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $actionClass = getActionClass($row['action_type']);
            $item_name = $row['item_name'] ?? 'N/A';
            $value_change = getValueChange($row['previous_value'], $row['new_value'], $row['unit_of_measure']);
            
            $output .= "
                <tr>
                    <td>{$row['log_id']}</td>
                    <td class='fw-bold'>{$item_name}</td>
                    <td><span class='badge {$actionClass}'>{$row['action_type']}</span></td>
                    <td>{$value_change}</td>
                    <td>{$row['username']}</td>
                    <td>" . date('M j, Y g:i A', strtotime($row['timestamp'])) . "</td>
                    <td class='text-center'>
                        <button class='btn btn-sm btn-info view-log' data-id='{$row['log_id']}'>
                            <i class='bi bi-eye'></i>
                        </button>
                    </td>
                </tr>";
        }
    } else {
        $output = "<tr>
                    <td colspan='7' class='text-center py-4'>
                        <div class='text-muted'>
                            <i class='bi bi-inbox fs-1 d-block mb-2'></i>
                            No logs found
                        </div>
                    </td>
                  </tr>";
    }
    return $output;
}

function getActionClass($action) {
    $classes = [
        'NEW_ITEM_ADDED' => 'bg-success',
        'STOCK_UPDATED' => 'bg-info',
        'ITEM_UPDATED' => 'bg-warning',
        'ITEM_DELETED' => 'bg-danger',
        'PILFERAGE_DEDUCTION' => 'bg-dark',
        'ITEM_USED' => 'bg-primary'
    ];
    return $classes[$action] ?? 'bg-secondary';
}

function getValueChange($prev, $new, $unit = '') {
    if($prev === $new) return "No change";
    $unit = $unit ? " $unit" : '';
    $arrow = $new > $prev ? '↑' : '↓';
    return "$prev$unit → $new$unit $arrow";
}
?>
