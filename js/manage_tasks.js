document.addEventListener('DOMContentLoaded', () => {
  const taskTableBody = document.getElementById('taskTableBody');

  const fetchTasks = async () => {
    try {
      const response = await fetch('../handlers/get_tasks.php');
      const data = await response.json();

      if (data.status === 'success') {
        taskTableBody.innerHTML = '';
        data.tasks.forEach((task) => {
          const row = document.createElement('tr');
          row.innerHTML = `
                        <td>${task.first_name} ${task.last_name}</td>
                        <td>${task.title}</td>
                        <td>${task.description || '-'}</td>
                        <td>${task.due_date}</td>
                        <td>${task.priority}</td>
                        <td>
                            <select class="form-select form-select-sm status-select" data-task-id="${
                              task.id
                            }">
                                <option value="Pending" ${
                                  task.status === 'Pending' ? 'selected' : ''
                                }>Pending</option>
                                <option value="In Progress" ${
                                  task.status === 'In Progress'
                                    ? 'selected'
                                    : ''
                                }>In Progress</option>
                                <option value="Completed" ${
                                  task.status === 'Completed' ? 'selected' : ''
                                }>Completed</option>
                            </select>
                        </td>
                        <td>${task.assigned_at}</td>
                    `;
          taskTableBody.appendChild(row);
        });
      } else {
        taskTableBody.innerHTML =
          '<tr><td colspan="7" class="text-danger">Failed to load tasks.</td></tr>';
      }
    } catch (error) {
      taskTableBody.innerHTML =
        '<tr><td colspan="7" class="text-danger">Error loading tasks.</td></tr>';
    }
  };

  // Update status on change
  document.addEventListener('change', async (e) => {
    if (e.target.classList.contains('status-select')) {
      const taskId = e.target.getAttribute('data-task-id');
      const newStatus = e.target.value;

      const formData = new FormData();
      formData.append('task_id', taskId);
      formData.append('status', newStatus);

      try {
        const response = await fetch('../handlers/update_task_status.php', {
          method: 'POST',
          body: formData,
        });
        const data = await response.json();
        if (data.status === 'success') {
          alert('Task status updated.');
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error updating task.');
      }
    }
  });

  fetchTasks(); 
});
