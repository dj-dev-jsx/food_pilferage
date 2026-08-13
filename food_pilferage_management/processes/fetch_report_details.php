<?php

include "../include/connect_db.php"; // Include your database connection

if(isset($_POST['action']) && $_POST['action'] == 'fetchSinglePilferage'){
    $reportId = mysqli_real_escape_string($conn, $_POST['report_id']); // Sanitize the input
    
    // Query to fetch details of the selected report
    $query = "SELECT pr.report_id, pr.reported_quantity, pr.date_reported, pr.description, 
            rs.report_status, i.item_name, 
            u.firstname, u.lastname, u.role_id, pr.updated_at
            FROM pilferage_report pr
            JOIN report_status rs ON pr.report_status_id = rs.report_status_id
            JOIN items i ON pr.item_id = i.item_id
            JOIN users u ON pr.user_id = u.user_id
            WHERE pr.report_id = '$reportId'"; // Get details of the specific report

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        
        // Build the HTML content to display in the modal
        echo '
            <p><strong>Report ID:</strong> '.htmlspecialchars($row['report_id']).'</p>
            <p><strong>Item Name:</strong> '.htmlspecialchars($row['item_name']).'</p>
            <p><strong>Reported Quantity:</strong> '.htmlspecialchars($row['reported_quantity']).'</p>
            <p><strong>Date Reported:</strong> '.htmlspecialchars($row['date_reported']).'</p>
            <p><strong>Description:</strong> '.htmlspecialchars($row['description']).'</p>
            <p><strong>Reported by:</strong> '.htmlspecialchars($row['firstname'].' '.$row['lastname']).'</p>
            <p><strong>Status:</strong> '.htmlspecialchars($row['report_status']).'</p>
            <p><strong>Last Updated:</strong> '.htmlspecialchars($row['updated_at']).'</p>
        ';
    } else {
        echo '<p>No report found.</p>';
    }
}
?>
