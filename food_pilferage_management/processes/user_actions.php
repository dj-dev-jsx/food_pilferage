<?php
session_start();
include "../include/connect_db.php";

header('Content-Type: application/json');

if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'getUserDetails':
            $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
            $query = "SELECT u.*, r.role_name 
                     FROM users u 
                     JOIN roles r ON u.role_id = r.role_id 
                     WHERE u.user_id = ?";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            echo json_encode([
                'status' => 200,
                'data' => $user
            ]);
            break;

        case 'updateUser':
            $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
            $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
            $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
            $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
            $role_id = mysqli_real_escape_string($conn, $_POST['role_id']);

            $query = "UPDATE users SET 
                     firstname = ?,
                     lastname = ?,
                     middlename = ?,
                     email = ?,
                     contact_number = ?,
                     role_id = ?
                     WHERE user_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssii", 
                $firstname, 
                $lastname, 
                $middlename, 
                $email, 
                $contact_number, 
                $role_id, 
                $user_id
            );

            if($stmt->execute()) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'User updated successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Error updating user'
                ]);
            }
            break;

        case 'deleteUser':
            $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
            
            $query = "DELETE FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            
            if($stmt->execute()) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Error deleting user'
                ]);
            }
            break;

        case 'resetAccessCode':
            $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
            $new_code = sprintf("%06d", mt_rand(1, 999999));
            
            $query = "UPDATE users SET access_code = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $new_code, $user_id);
            
            if($stmt->execute()) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'Access code reset successfully',
                    'new_code' => $new_code
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Error resetting access code'
                ]);
            }
            break;

            case 'addUser':
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
                $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
                $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
                $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
                $role = mysqli_real_escape_string($conn, $_POST['role']);
                $access_code = mysqli_real_escape_string($conn, $_POST['access_code']);
            
                // Map role names to role_id
                $role_id = ($role === 'Kitchen Staff') ? 3 : 2;
            
                // Check if email already exists
                $check_query = "SELECT email FROM users WHERE email = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("s", $email);
                $check_stmt->execute();
                if($check_stmt->get_result()->num_rows > 0) {
                    echo json_encode([
                        'status' => 400,
                        'message' => 'Email already exists'
                    ]);
                    exit;
                }
            
                $query = "INSERT INTO users (username, email, firstname, middlename, lastname, contact_number, role_id, access_code) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssssss", 
                    $username,
                    $email,
                    $firstname,
                    $middlename,
                    $lastname,
                    $contact_number,
                    $role_id,
                    $access_code
                );
            
                if($stmt->execute()) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'User registered successfully'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Registration failed'
                    ]);
                }
                break;
            
            

            if($stmt->execute()) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'User added successfully',
                    'access_code' => $access_code
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Error adding user'
                ]);
            }
            break;
    }
}
