<?php
include "../include/connect_db.php";
include "pilferage_table.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pagination setup
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
$offset = ($page - 1) * $items_per_page;

// Base query
$base_query = "SELECT DISTINCT pr.*, i.item_name, u.firstname, u.lastname,
               rs.report_status, u.role_id
               FROM pilferage_report pr
               JOIN items i ON pr.item_id = i.item_id
               JOIN users u ON pr.user_id = u.user_id
               JOIN report_status rs ON pr.report_status_id = rs.report_status_id";

if(isset($_POST['action'])) {
    $where_clause = "";
    
    switch($_POST['action']) {
        case 'searchInvTable':
            if (!empty($_POST['search_inv_inp'])) {
                $searched_inp = mysqli_real_escape_string($conn, $_POST['search_inv_inp']);
                $where_clause = "WHERE i.item_name LIKE '%$searched_inp%'";
            }
            break;
            
        case 'reportStatusFilter':
            if (!empty($_POST['report_status_id'])) {
                $report_status = mysqli_real_escape_string($conn, $_POST['report_status_id']);
                $where_clause = "WHERE rs.report_status = '$report_status'";
            }
            break;
            
        case 'userReported':
            if (!empty($_POST['user_id'])) {
                $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
                $where_clause = "WHERE pr.user_id = '$user_id'";
            }
            break;
            
        case 'dateFilter':
            if (!empty($_POST['report_date'])) {
                $report_date = mysqli_real_escape_string($conn, $_POST['report_date']);
                $where_clause = "WHERE DATE(pr.date_reported) = '$report_date'";
            }
            break;
    }

    // Build final queries with error logging
    $count_query = $base_query . " " . $where_clause;
    $data_query = $count_query . " ORDER BY CAST(pr.report_id AS UNSIGNED) DESC LIMIT $offset, $items_per_page";
    
    error_log("Executing query: " . $data_query);

    // Get total records with error handling
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM ($count_query) as count_table");
    
    if (!$count_result) {
        $response = [
            'error' => true,
            'message' => 'Query failed: ' . mysqli_error($conn),
            'query' => $count_query
        ];
    } else {
        $total_records = mysqli_fetch_assoc($count_result)['total'];
        
        $response = [
            'records' => getPilferageData($data_query),
            'total_records' => $total_records,
            'current_page' => $page,
            'items_per_page' => $items_per_page,
            'query' => $data_query // For debugging
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
