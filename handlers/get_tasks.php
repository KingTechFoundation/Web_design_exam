<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$employee_id = $_SESSION['user_id']; // Assuming this is how you're storing logged in user ID

try {
    $stmt = $pdo->prepare("SELECT t.*, e.first_name, e.last_name 
                           FROM tasks t
                           JOIN employees e ON t.employee_id = e.id
                           WHERE t.employee_id = ?
                           ORDER BY t.due_date ASC");
    $stmt->execute([$employee_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'tasks' => $tasks]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
