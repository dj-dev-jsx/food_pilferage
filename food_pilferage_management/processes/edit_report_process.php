<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
include '../include/connect_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'fetch') {
        $reportId = isset($_POST['report_id']) ? $_POST['report_id'] : null;
        
        if ($reportId === null) {
            echo json_encode(['error' => 'Report ID not provided']);
            exit;
        }

        $query = "SELECT pr.report_id, pr.reported_quantity, pr.date_reported, pr.description,
                rs.report_status, rs.report_status_id, i.item_name, i.item_id,
                u.firstname, u.lastname, u.role_id, pr.updated_at
                FROM pilferage_report pr
                JOIN report_status rs ON pr.report_status_id = rs.report_status_id
                JOIN items i ON pr.item_id = i.item_id
                JOIN users u ON pr.user_id = u.user_id
                WHERE pr.report_id = ?";
               
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'Report not found']);
        }
    }elseif (isset($_POST['action']) && $_POST['action'] === 'update'){
        $reportId = $_POST['reportId'];
        $reported_quantity = $_POST['reported_quantity'];
        $report_status_id = $_POST['report_status_id'];
        $description = $_POST['description'];

        $update_query = "UPDATE pilferage_report SET reported_quantity = ?, description = ?, report_status_id = ?, updated_at = NOW() where report_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("dsii", $reported_quantity, $description, $report_status_id, $reportId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 
            'message' => 'Item updated successfully'
        ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update item: ' . $conn->error]);
        } 
    }else {
        echo json_encode(['error' => 'Invalid request']);
    }
}else {
    echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();
