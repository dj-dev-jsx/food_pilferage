<?php
session_start();
include "../include/connect_db.php";

if(isset($_POST['action']) && $_POST['action'] == 'fetchUsersData') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
    $offset = ($page - 1) * $items_per_page;
    
    $base_query = "SELECT u.user_id, u.firstname, u.lastname, u.middlename, u.email, u. contact_number, u.access_code, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id"; // Exclude admin accounts from listing
    
    $count_query = "SELECT COUNT(*) as total FROM ($base_query) as count_table";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    
    $query = $base_query . " ORDER BY u.user_id DESC LIMIT $offset, $items_per_page";
    
    $records = getUsersData($query);
    
    $response = [
        'records' => $records,
        'total_records' => (int)$total_records // Ensure integer type
    ];
    
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function getUsersData($query) {
    global $conn;
    $output = "";
    
    try {
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                
                
                $output .= '<tr>
                    <td>'.htmlspecialchars($row['user_id']).'</td>
                    <td>'.htmlspecialchars($row['firstname'].' '.$row['lastname']).'</td>
                    <td>'.htmlspecialchars($row['email']).'</td>
                    <td>'.htmlspecialchars($row['contact_number']).'</td>
                    <td>'.htmlspecialchars($row['role_name']).'</td>
                    <td>'.htmlspecialchars($row['access_code']).'</td>
                    
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-sm edit-btn ms-2" 
                                data-id="'.htmlspecialchars($row['user_id']).'" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit-user-modal" 
                                title="Edit User">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn ms-2" 
                                data-id="'.htmlspecialchars($row['user_id']).'"
                                title="Delete User">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>

                </tr>';
            }
        } else {
            $output = "<tr><td colspan='8' class='text-center'>No Users Found</td></tr>";
        }
    } catch (Exception $e) {
        error_log("Database query error: " . $e->getMessage());
        $output = "<tr><td colspan='8' class='text-center'>An error occurred while fetching data</td></tr>";
    }
    
    return $output;
}
