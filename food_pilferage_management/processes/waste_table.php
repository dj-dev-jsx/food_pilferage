<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../include/connect_db.php";

if(isset($_POST['action'])) {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 10;
    $offset = ($page - 1) * $items_per_page;

    // Get total records first
    $count_query = "SELECT COUNT(*) as total FROM wastes";
    $count_result = $conn->query($count_query);
    $total_records = $count_result->fetch_assoc()['total'];

    // Main query
    $query = "SELECT w.*, i.item_name, i.unit_of_measure, u.username 
              FROM wastes w
              LEFT JOIN items i ON w.item_id = i.item_id
              LEFT JOIN users u ON w.user_id = u.user_id
              ORDER BY w.timestamp DESC
              LIMIT ?, ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $offset, $items_per_page);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = "";
    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $output .= "<tr>
                <td>" . htmlspecialchars($row['waste_id']) . "</td>
                <td>" . htmlspecialchars($row['item_name'] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($row['quantity']) . " " . htmlspecialchars($row['unit_of_measure'] ?? 'units') . "</td>
                <td>" . htmlspecialchars($row['reason']) . "</td>
                <td>₱" . number_format((float)$row['total_cost'], 2) . "</td>
                <td>" . htmlspecialchars($row['username'] ?? 'Unknown') . "</td>
                <td>" . date('M j, Y g:i A', strtotime($row['timestamp'])) . "</td>
                <td class='text-center'>
                    <button class='btn btn-sm btn-info view-waste' data-id='" . htmlspecialchars($row['waste_id']) . "'>
                        <i class='bi bi-eye'></i>
                    </button>
                </td>
            </tr>";
        }
    }
     else {
        $output = "<tr><td colspan='8' class='text-center'>No waste records found</td></tr>";
    }

    $data = [
        'records' => $output,
        'total_records' => $total_records
    ];

    echo json_encode($data);
}
