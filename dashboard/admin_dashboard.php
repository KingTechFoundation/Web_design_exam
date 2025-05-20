<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: #fff;
      padding: 20px 15px;
    }

    .sidebar h4 {
      margin-bottom: 30px;
      font-weight: bold;
      text-align: center;
    }

    .sidebar button {
      width: 100%;
      margin-bottom: 15px;
      background-color: #495057;
      border: none;
      padding: 12px;
      color: #fff;
      text-align: left;
      border-radius: 8px;
      transition: background 0.3s;
    }

    .sidebar button:hover {
      background-color: #6c757d;
    }

    .main-content {
      flex-grow: 1;
      padding: 30px;
      background-color: #f8f9fa;
    }

    .section {
      display: none;
    }

    .section.active {
      display: block;
    }

    .form-group {
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="dashboard-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h4>Admin Panel</h4>
    <button onclick="showSection('addEmployee')">Add Employee</button>
    <button onclick="showSection('employeeList')">Employee List</button>
    <button onclick="showSection('assignTask')">Assign Task</button>
    <button onclick="showSection('sendNotification')">Send Notification</button>
    <a href="../auth/logout.php" class="btn btn-danger mt-4 w-100">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> (Admin)</h2>

    <!-- Sections -->

    <!-- Add Employee Section -->
    <div id="addEmployee" class="section active">
      <h4>Add Employee</h4>
      <div class="d-flex gap-2 my-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
          Add Employee
        </button>
        </div>
    </div>

    <div id="employeeList" class="section">
     <h4>Employee List</h4>
    <?php include '../components/employee_table.php'; ?>
    </div>

    <!-- Assign Task Section -->
    <div id="assignTask" class="section">
  <h4>Assign Task</h4>
  <div class="d-flex gap-2 my-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
      Assign Task
    </button>
  </div>

  
  <?php include '../components/assign_task_modal.php'; ?>


  <?php include '../components/task_table.php'; ?>
</div>


    <!-- Send Notification Section -->
    <div id="sendNotification" class="section">
      <h4>Send Notification</h4>
      <div class="d-flex gap-2 my-3">
        <button class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
          Send Notification
        </button>
      </div>
    </div>

  </div> <!-- End of Main Content -->
</div> <!-- End of Dashboard Container -->

<!-- Modals -->

<?php include '../components/add_employee_modal.php'; ?>
<?php include '../components/employee_table.php'; ?>
<?php include '../components/assign_task_modal.php'; ?>
<?php include '../components/send_notification_modal.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/admin.js"></script>
<script src="../js/attendance.js"></script>
<script src="../js/notifications.js"></script>
<script src="../js/tasks.js"></script>
<script src="../js/manage_tasks.js"></script>
<script src="../js/search.js"></script>

<script>
  function showSection(id) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
      section.classList.remove('active');
    });
    document.getElementById(id).classList.add('active');
  }

  document.getElementById('addEmployeeForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
      const response = await fetch('../handlers/add_employee.php', {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();
      if (result.success) {
        alert('Employee added successfully!');
        this.reset();
      } else {
        alert('Error: ' + result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred.');
    }
  });
</script>

</body>
</html>
