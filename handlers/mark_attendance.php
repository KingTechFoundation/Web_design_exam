<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    if (empty($employee_id) || empty($date) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    try {
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM attendance WHERE employee_id = ? AND date = ?");
        $stmt->execute([$employee_id, $date]);

        if ($stmt->fetch()) {
            // Update
            $stmt = $pdo->prepare("UPDATE attendance SET status = ?, remarks = ? WHERE employee_id = ? AND date = ?");
            $stmt->execute([$status, $remarks, $employee_id, $date]);
            echo json_encode(['success' => true, 'message' => 'Attendance updated successfully.']);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, date, status, remarks) VALUES (?, ?, ?, ?)");
            $stmt->execute([$employee_id, $date, $status, $remarks]);
            echo json_encode(['success' => true, 'message' => 'Attendance marked successfully.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
