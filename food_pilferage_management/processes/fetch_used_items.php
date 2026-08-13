<?php
session_start();
include "../include/connect_db.php";

$records_per_page = 10;
$page = isset($_POST['page']) ? $_POST['page'] : 1;
$start = ($page - 1) * $records_per_page;

$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
$date = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : '';
$user = isset($_POST['user']) ? mysqli_real_escape_string($conn, $_POST['user']) : '';

$where_clause = [];
$params = [];
$types = '';

if(!empty($search)) {
    $where_clause[] = "i.item_name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if(!empty($date)) {
    $where_clause[] = "DATE(iu.usage_date) = ?";
    $params[] = $date;
    $types .= 's';
}

if(!empty($user)) {
    $where_clause[] = "iu.user_id = ?";
    $params[] = $user;
    $types .= 'i';
}

$where = !empty($where_clause) ? "WHERE " . implode(' AND ', $where_clause) : "";

$query = "SELECT iu.*, i.item_name, u.username 
          FROM item_usage iu
          JOIN items i ON iu.item_id = i.item_id
          JOIN users u ON iu.user_id = u.user_id
          $where
          ORDER BY iu.usage_date DESC
          LIMIT ?, ?";

$types .= 'ii';
$params[] = $start;
$params[] = $records_per_page;

$stmt = $conn->prepare($query);
if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$output = '';
while($row = $result->fetch_assoc()) {
    $output .= "<tr>
        <td>" . htmlspecialchars($row['item_name']) . "</td>
        <td>" . htmlspecialchars($row['quantity_used']) . "</td>
        <td>" . htmlspecialchars($row['servings']) . "</td>
        <td>₱" . number_format($row['price_per_serving'], 2) . "</td>
        <td>₱" . number_format($row['total_cost'], 2) . "</td>
        <td>" . htmlspecialchars($row['username']) . "</td>
        <td>" . date('M d, Y h:i A', strtotime($row['usage_date'])) . "</td>
        <td class='text-center'>
            <button class='btn btn-sm btn-info view-btn' data-id='" . $row['usage_id'] . "'>
                <i class='bi bi-eye'></i>
            </button>
            " . ($_SESSION['role_id'] == 1 ? "
            <button class='btn btn-sm btn-danger delete-btn' data-id='" . $row['usage_id'] . "'>
                <i class='bi bi-trash'></i>
            </button>" : "") . "
        </td>
    </tr>";
}

$count_query = preg_replace('/SELECT.*?FROM/s', 'SELECT COUNT(*) as total FROM', $query);
$count_query = preg_replace('/LIMIT.*$/s', '', $count_query);
$stmt = $conn->prepare($count_query);
if(!empty($params)) {
    array_pop($params);
    array_pop($params);
    $types = substr($types, 0, -2);
    if(!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
}
$stmt->execute();
$total_result = $stmt->get_result()->fetch_assoc();
$total_records = $total_result['total'];
$total_pages = ceil($total_records / $records_per_page);

$pagination = '';
for($i = 1; $i <= $total_pages; $i++) {
    $active = $page == $i ? 'success' : 'outline-success';
    $pagination .= "<button class='btn btn-$active page-link' data-page='$i'>$i</button>";
}

$response = [
    'html' => $output,
    'pagination' => $pagination,
    'total_records' => $total_records,
    'start' => $start + 1,
    'end' => min($start + $records_per_page, $total_records)
];

echo json_encode($response);
