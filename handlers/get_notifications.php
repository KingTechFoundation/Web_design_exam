<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

require '../config/db_connection.php';

$employeeId = $_SESSION['employee_id'];

$stmt = $pdo->prepare('SELECT id, title, message, is_read, sent_at FROM notifications WHERE employee_id = ? ORDER BY sent_at DESC');
$stmt->execute([$employeeId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'notifications' => $notifications,
]);
