<?php
session_start();
include "../include/connect_db.php";

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['username']) && isset($_POST['access_code'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $access_code = mysqli_real_escape_string($conn, $_POST['access_code']);

    $query = "SELECT user_id, username, role_id, firstname, lastname 
            FROM users 
            WHERE username = ? AND access_code = ?";
            
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $access_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];

        echo json_encode([
            'status' => 200,
            'message' => 'Login successful',
            'redirect' => match($user['role_id']) {
                1 => 'dashboard.php',
                3 => 'inventory.php',
                default => 'pilferage_report.php'
            }
        ]);
        
    } else {
        echo json_encode([
            'status' => 401,
            'message' => 'Invalid username or access code'
        ]);
    }
}
