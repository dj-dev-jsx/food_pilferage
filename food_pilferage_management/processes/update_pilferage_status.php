<?php
include "../include/connect_db.php";

if(isset($_POST['report_id']) && isset($_POST['new_status'])) {
    $report_id = mysqli_real_escape_string($conn, $_POST['report_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    // Validate status exists in report_status table
    $status_check = "SELECT report_status_id FROM report_status WHERE report_status_id = ?";
    $stmt = $conn->prepare($status_check);
    $stmt->bind_param("i", $new_status);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        // Update the pilferage report status
        $update_query = "UPDATE pilferage_report 
                        SET report_status_id = ?, 
                            updated_at = CURRENT_TIMESTAMP 
                        WHERE report_id = ?";
        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $new_status, $report_id);
        
        if($update_stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Report status updated successfully'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to update status'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid status selected'
        ];
    }
    
    echo json_encode($response);
}
