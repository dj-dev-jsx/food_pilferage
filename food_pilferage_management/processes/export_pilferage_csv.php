<?php
session_start();
include "../include/connect_db.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="pilferage_report_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, array('Report ID', 'Item Name', 'Reported By', 'Reported Quantity', 'Date Reported', 'Status', 'Updated At'));

$query = "SELECT p.*, i.item_name, u.username 
          FROM pilferage_reports p
          JOIN items i ON p.item_id = i.item_id
          JOIN users u ON p.user_id = u.user_id
          ORDER BY p.date_reported DESC";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, array(
        $row['report_id'],
        $row['item_name'],
        $row['username'],
        $row['reported_quantity'],
        $row['date_reported'],
        $row['status'],
        $row['updated_at']
    ));
}

fclose($output);
?>
