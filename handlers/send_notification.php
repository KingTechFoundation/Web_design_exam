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
    $message = trim($_POST['message'] ?? '');

    if (!$employee_id || !$title || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (employee_id, title, message) VALUES (?, ?, ?)");
        $stmt->execute([$employee_id, $title, $message]);
        echo json_encode(['status' => 'success', 'message' => 'Notification sent successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
