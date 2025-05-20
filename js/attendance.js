document.querySelectorAll('.attendance-submit-btn').forEach((button) => {
  button.addEventListener('click', async (e) => {
    const row = e.target.closest('tr');
    const employeeId = row.dataset.employeeId;
    const date = row.querySelector('.attendance-date').value;
    const status = row.querySelector('.attendance-status').value;
    const remarks = row.querySelector('.attendance-remarks').value;

    if (!date || !status) {
      alert('Date and status are required.');
      return;
    }

    const formData = new FormData();
    formData.append('employee_id', employeeId);
    formData.append('date', date);
    formData.append('status', status);
    formData.append('remarks', remarks);

    const response = await fetch('../handlers/mark_attendance.php', {
      method: 'POST',
      body: formData,
    });

    const result = await response.json();
    alert(result.message);
  });
});
