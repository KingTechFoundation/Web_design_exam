<?php
session_start();
require_once '../config/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT t.*, e.first_name, e.last_name 
                         FROM tasks t
                         JOIN employees e ON t.employee_id = e.id
                         ORDER BY t.due_date ASC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'tasks' => $tasks]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
