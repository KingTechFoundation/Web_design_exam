<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    $salary = trim($_POST['salary']);
    $date_of_joining = trim($_POST['date_of_joining']);
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($id) || empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit;
    }

    try {
        // Build SQL dynamically in case password is updated
        if (!empty($password)) {
            // Password is being updated, so hash it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE employees SET 
                        first_name = ?, last_name = ?, email = ?, phone = ?, 
                        position = ?, department = ?, salary = ?, date_of_joining = ?,
                        username = ?, role = ?, password = ?
                    WHERE id = ?";
            $params = [
                $first_name, $last_name, $email, $phone,
                $position, $department, $salary, $date_of_joining,
                $username, $role, $hashed_password, $id
            ];
        } else {
            // No password update
            $sql = "UPDATE employees SET 
                        first_name = ?, last_name = ?, email = ?, phone = ?, 
                        position = ?, department = ?, salary = ?, date_of_joining = ?,
                        username = ?, role = ?
                    WHERE id = ?";
            $params = [
                $first_name, $last_name, $email, $phone,
                $position, $department, $salary, $date_of_joining,
                $username, $role, $id
            ];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Employee updated successfully.']);
    } catch (PDOException $e) {
        // Handle duplicate email/username error gracefully
        if ($e->errorInfo[1] == 1062) {
            echo json_encode(['success' => false, 'message' => 'Email or Username already exists.']);
        } else {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
?>
