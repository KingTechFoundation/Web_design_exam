<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header('Location: ../auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/user_dashboard.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['username']); ?></span>
            </div>
            <ul class="sidebar-menu">
    <li>
        <a href="#" onclick="loadNotifications()">
            <i class="fas fa-bell"></i>
            Notifications
            <span id="notification-count" class="badge">0</span>
        </a>
    </li>
    <li>
        <a href="#" onclick="loadTasks()">
            <i class="fas fa-tasks"></i>
            My Tasks
        </a>
    </li>
    <li>
        <a href="../auth/logout.php">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </li>
</ul>
        </aside>
        
        <main class="main-content">
    <h2 id="main-title">Your Notifications</h2>
    <div id="notifications">
        <p>Loading notifications...</p>
    </div>

    <div id="tasks" style="display:none;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="task-list">
                <!-- Task rows will be added here -->
            </tbody>
        </table>
    </div>
</main>
    </div>
    <script src="../js/tasks.js"></script>
    <script src="../js/notificationz.js"></script>
</body>
</html>
