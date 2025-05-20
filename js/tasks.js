// Task form handler for Admin (inside DOMContentLoaded to ensure elements exist)
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('taskForm');
  const alertBox = document.getElementById('taskAlert');

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      try {
        const response = await fetch('../handlers/assign_task.php', {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.status === 'success') {
          alertBox.classList.remove('alert-danger');
          alertBox.classList.add('alert-success');
          alertBox.textContent = data.message;
          form.reset();
        } else {
          alertBox.classList.remove('alert-success');
          alertBox.classList.add('alert-danger');
          alertBox.textContent = data.message;
        }

        alertBox.style.display = 'block';
      } catch (error) {
        alertBox.classList.remove('alert-success');
        alertBox.classList.add('alert-danger');
        alertBox.textContent = 'An error occurred. Please try again.';
        alertBox.style.display = 'block';
      }
    });
  }
});

// Employee Task Loader (must be globally accessible)
function loadTasks() {
  const title = document.getElementById('main-title');
  const notifications = document.getElementById('notifications');
  const tasks = document.getElementById('tasks');
  const taskList = document.getElementById('task-list');

  if (!title || !notifications || !tasks || !taskList) {
    console.error('Task view elements not found in DOM.');
    return;
  }

  title.innerText = 'My Tasks';
  notifications.style.display = 'none';
  tasks.style.display = 'block';

  fetch('../backend/get_tasks.php')
    .then((res) => res.json())
    .then((data) => {
      taskList.innerHTML = '';

      if (data.status === 'success') {
        if (data.tasks.length === 0) {
          taskList.innerHTML =
            '<tr><td colspan="5">No tasks assigned.</td></tr>';
        } else {
          data.tasks.forEach((task, index) => {
            taskList.innerHTML += `
              <tr>
                <td>${index + 1}</td>
                <td>${task.title}</td>
                <td>${task.description}</td>
                <td>${task.due_date}</td>
                <td>${task.status}</td>
              </tr>
            `;
          });
        }
      } else {
        taskList.innerHTML = `<tr><td colspan="5" class="text-danger">${data.message}</td></tr>`;
      }
    })
    .catch((error) => {
      console.error('Fetch error:', error);
      taskList.innerHTML = `<tr><td colspan="5" class="text-danger">Error loading tasks.</td></tr>`;
    });
}
