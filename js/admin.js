document.addEventListener('DOMContentLoaded', () => {
  const addEmployeeForm = document.getElementById('addEmployeeForm');

  addEmployeeForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addEmployeeForm);
    const isEdit = formData.get('id');
    const handlerUrl = isEdit
      ? '../handlers/update_employee.php'
      : '../handlers/add_employee.php';

    try {
      const response = await fetch(handlerUrl, {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        alert(
          isEdit
            ? 'Employee updated successfully!'
            : 'Employee added successfully!'
        );
        location.reload();
      } else {
        alert('Error: ' + result.message);
      }
    } catch (error) {
      console.error('Fetch error:', error);
      alert('An error occurred. Please try again.');
    }
  });
  // DELETE
  document.querySelectorAll('.delete-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      if (confirm('Are you sure you want to delete this employee?')) {
        const id = btn.getAttribute('data-id');
        const formData = new FormData();
        formData.append('id', id);

        const response = await fetch('../handlers/delete_employee.php', {
          method: 'POST',
          body: formData,
        });
        const result = await response.json();

        if (result.success) {
          alert('Employee deleted successfully!');
          location.reload();
        } else {
          alert('Error: ' + result.message);
        }
      }
    });
  });

  // EDIT functionality
  document.querySelectorAll('.edit-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      const formData = new FormData();
      formData.append('id', id);

      const response = await fetch('../handlers/get_employee.php', {
        method: 'POST',
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        const emp = result.employee;

        // Prefill the form
        document.querySelector('input[name="first_name"]').value =
          emp.first_name;
        document.querySelector('input[name="last_name"]').value = emp.last_name;
        document.querySelector('input[name="email"]').value = emp.email;
        document.querySelector('input[name="phone"]').value = emp.phone;
        document.querySelector('input[name="position"]').value = emp.position;
        document.querySelector('input[name="department"]').value =
          emp.department;
        document.querySelector('input[name="salary"]').value = emp.salary;
        document.querySelector('input[name="date_of_joining"]').value =
          emp.date_of_joining;

        // Change form to edit mode
        document.getElementById('addEmployeeModalLabel').textContent =
          'Edit Employee';
        const submitBtn = document.querySelector(
          '#addEmployeeForm button[type="submit"]'
        );
        submitBtn.textContent = 'Update Employee';

        // Add a hidden field for ID
        let hiddenInput = document.querySelector('input[name="id"]');
        if (!hiddenInput) {
          hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'id';
          addEmployeeForm.appendChild(hiddenInput);
        }
        hiddenInput.value = emp.id;

        // Show the modal
        const addEmployeeModal = new bootstrap.Modal(
          document.getElementById('addEmployeeModal')
        );
        addEmployeeModal.show();
      } else {
        alert('Error fetching employee: ' + result.message);
      }
    });
  });

  // Handle update on form submit if ID exists
  addEmployeeForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(addEmployeeForm);
    const isEdit = formData.get('id');

    const handlerUrl = isEdit
      ? '../handlers/update_employee.php'
      : '../handlers/add_employee.php';

    const response = await fetch(handlerUrl, {
      method: 'POST',
      body: formData,
    });
    const result = await response.json();

    if (result.success) {
      alert(
        isEdit
          ? 'Employee updated successfully!'
          : 'Employee added successfully!'
      );
      location.reload();
    } else {
      alert('Error: ' + result.message);
    }
  });
});
