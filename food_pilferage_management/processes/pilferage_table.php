<?php
session_start();
include "../include/connect_db.php";

if(isset($_POST['action']) && $_POST['action'] == 'fetchPilferageData') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
    $offset = ($page - 1) * $items_per_page;
    
    $base_query = "SELECT pr.report_id, pr.reported_quantity, pr.date_reported, pr.description,
        rs.report_status, rs.report_status_id, i.item_name,
        u.firstname, u.lastname, u.role_id, pr.updated_at
        FROM pilferage_report pr
        JOIN report_status rs ON pr.report_status_id = rs.report_status_id
        JOIN items i ON pr.item_id = i.item_id
        JOIN users u ON pr.user_id = u.user_id";
    
    $count_query = "SELECT COUNT(*) as total FROM ($base_query) as count_table";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    $query = $base_query . " ORDER BY CAST(pr.report_id AS UNSIGNED) DESC LIMIT $offset, $items_per_page";
    
    $records = getPilferageData($query);
    
    $response = [
        'records' => $records,
        'total_records' => $total_records
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
function getPilferageData($query) {
    global $conn;
    $output = "";
    
    try {
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $statusClass = getStatusClass($row['report_status']);
                
                $output .= '<tr class="align-middle">
                    <td class="text-center">'.htmlspecialchars($row['report_id']).'</td>
                    <td>'.htmlspecialchars($row['item_name']).'</td>';
                
                if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                    $output .= '<td>'.htmlspecialchars($row['firstname'].' '.$row['lastname']).'</td>';
                }
                $formattedDateReported = date('F j, Y h:i A', strtotime($row['date_reported']));
                $formattedUpdatedAt = date('F j, Y h:i A', strtotime($row['updated_at']));
                $output .= '<td class="text-center">'.htmlspecialchars($row['reported_quantity']).'</td>
                    <td>'.$formattedDateReported.'</td>
                    <td class="text-center"><span class="badge '.$statusClass.'">'.htmlspecialchars($row['report_status']).'</span></td>
                    <td>'.$formattedUpdatedAt.'</td>
                    <td class="text-center">
                        <div class="action-buttons d-flex justify-content-center gap-2">
                            <button class="btn btn-success btn-sm view-btn" data-id="'.htmlspecialchars($row['report_id']).'"
                                data-bs-toggle="modal" data-bs-target="#view-modal" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>';
                
                if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                    $output .= '<button id="statusBtn_'.htmlspecialchars($row['report_id']).'"
                        class="btn btn-primary btn-sm status-btn"
                        data-id="'.htmlspecialchars($row['report_id']).'"
                        data-current-status="'.htmlspecialchars($row['report_status_id']).'"
                        title="Update Status">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>';
                }
                
                $output .= '</div>
                    </td>
                </tr>';
            }
        } else {
            $output = "<tr><td colspan='8' class='text-center'>No Reports Found</td></tr>";
        }
    } catch (Exception $e) {
        error_log("Database query error: " . $e->getMessage());
        $output = "<tr><td colspan='8' class='text-center'>An error occurred while fetching data</td></tr>";
    }
    
    return $output;
}


function getStatusClass($status) {
    switch(strtolower($status)) {
        case 'pending':
            return 'bg-warning';
        case 'under investigation':
            return 'bg-info';
        case 'resolved':
            return 'bg-success';
        case 'closed':
            return 'bg-secondary';
        default:
            return 'bg-primary';
    }
}
