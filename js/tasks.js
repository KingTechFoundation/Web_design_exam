document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('taskForm');
  const alertBox = document.getElementById('taskAlert');

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
});
