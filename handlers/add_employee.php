<?php
header('Content-Type: application/json'); 
ini_set('display_errors', 0);              

require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    $salary = trim($_POST['salary']);
    $date_of_joining = trim($_POST['date_of_joining']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    try {
        $stmt = $pdo->prepare("INSERT INTO employees 
            (first_name, last_name, email, phone, position, department, salary, date_of_joining, username, password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $first_name, $last_name, $email, $phone,
            $position, $department, $salary, $date_of_joining,
            $username, $hashed_password, $role
        ]);
        echo json_encode(['success' => true, 'message' => 'Employee added successfully.']);
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // Duplicate entry
            echo json_encode(['success' => false, 'message' => 'Email or Username already exists.']);
        } else {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
?>
