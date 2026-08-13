<?php
session_start();
include "../include/connect_db.php";

if(isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if($action === 'fetchProfile') {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT username, email, firstname, lastname, middlename, contact_number, role_id 
                 FROM users WHERE user_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($row = $result->fetch_assoc()) {
            echo json_encode($row);
        }
    }
    
    if($action === 'updateProfile') {
        $user_id = $_SESSION['user_id'];
        $email = $_POST['email'];
        $firstname = $_POST['first_name'];
        $lastname = $_POST['last_name'];
        $middlename = $_POST['middle_name'];
        $contact = $_POST['contact_number'];
        
        $query = "UPDATE users SET 
                 email = ?, 
                 firstname = ?, 
                 lastname = ?, 
                 middlename = ?, 
                 contact_number = ?
                 WHERE user_id = ?";
                 
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $email, $firstname, $lastname, $middlename, $contact, $user_id);
        
        if($stmt->execute()) {
            if(!empty($_POST['new_password'])) {
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $pwd_query = "UPDATE users SET password = ? WHERE user_id = ?";
                $pwd_stmt = $conn->prepare($pwd_query);
                $pwd_stmt->bind_param("si", $new_password, $user_id);
                $pwd_stmt->execute();
            }
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
        }
    }
}
