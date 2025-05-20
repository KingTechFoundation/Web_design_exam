<?php
session_start();
require_once '../config/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $priority = $_POST['priority'] ?? 'Medium';

    if (!$employee_id || !$title || !$due_date || !$priority) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (employee_id, title, description, due_date, priority) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$employee_id, $title, $description, $due_date, $priority]);
        echo json_encode(['status' => 'success', 'message' => 'Task assigned successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
