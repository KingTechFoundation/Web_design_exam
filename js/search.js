
document.getElementById('employeeSearch').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#employeeTable tbody tr');

    rows.forEach(row => {
        const rowText = row.innerText.toLowerCase();
        if (rowText.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

