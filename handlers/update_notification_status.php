<?php
session_start();

if (!isset($_SESSION['employee_id'])) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Invalid request method.';
    exit;
}

require '../config/db_connection.php';

$employeeId = $_SESSION['employee_id'];
$notificationId = $_POST['notification_id'] ?? null;
$isRead = $_POST['is_read'] ?? null;

if (!$notificationId || !in_array($isRead, ['0', '1'], true)) {
    http_response_code(400);
    echo 'Invalid data.';
    exit;
}

// Check ownership (optional but secure)
$stmt = $pdo->prepare('UPDATE notifications SET is_read = ? WHERE id = ? AND employee_id = ?');
$updated = $stmt->execute([$isRead, $notificationId, $employeeId]);

if ($updated) {
    echo 'Notification status updated.';
} else {
    http_response_code(500);
    echo 'Failed to update notification.';
}
