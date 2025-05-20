<div class="mb-3">
    <input type="text" id="employeeSearch" class="form-control" placeholder="Search employees by name, email, username, or phone...">
</div>

<!-- Export Buttons -->
<div class="mb-3">
    <button class="btn btn-success" onclick="exportTableToExcel('employeeTable')">Export to Excel</button>
    <button class="btn btn-primary" onclick="exportTableToCSV('employeeTable')">Export to CSV</button>
    <button class="btn btn-danger" onclick="exportTableToPDF()">Download PDF</button>
</div>

<!-- Employee Table -->
<table class="table table-striped" id="employeeTable">
<thead>
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Username</th>
    <th>Role</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Position</th>
    <th>Department</th>
    <th>Salary</th>
    <th>Date of Joining</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$stmt = $pdo->query("SELECT * FROM employees ORDER BY id DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>
        <td>" . htmlspecialchars($row['username']) . "</td>
        <td>" . htmlspecialchars($row['role']) . "</td>
        <td>" . htmlspecialchars($row['email']) . "</td>
        <td>" . htmlspecialchars($row['phone']) . "</td>
        <td>" . htmlspecialchars($row['position']) . "</td>
        <td>" . htmlspecialchars($row['department']) . "</td>
        <td>" . htmlspecialchars($row['salary']) . "</td>
        <td>" . htmlspecialchars($row['date_of_joining']) . "</td>
        <td>" . htmlspecialchars($row['status']) . "</td>
        <td>
            <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}'>Edit</button>
            <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'>Delete</button>
        </td>
    </tr>";
}
?>
</tbody>
</table>

<hr>

<h3 class="mt-5">Employee Attendance</h3>

<table class="table table-bordered mt-3" id="attendanceTable">
<thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Position</th>
        <th>Date</th>
        <th>Status</th>
        <th>Remarks</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
<?php
$stmt = $pdo->query("SELECT * FROM employees");
$i = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
    <tr data-employee-id="<?= $row['id']; ?>">
        <td><?= $i++; ?></td>
        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
        <td><?= htmlspecialchars($row['position']); ?></td>
        <td>
            <input type="date" class="form-control attendance-date" value="<?= date('Y-m-d'); ?>">
        </td>
        <td>
            <select class="form-select attendance-status">
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Leave">Leave</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control attendance-remarks" placeholder="Optional remarks">
        </td>
        <td>
            <button class="btn btn-sm btn-primary attendance-submit-btn">Submit</button>
        </td>
    </tr>
<?php endwhile; ?>
</tbody>
</table>

<!-- Required Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Export Functions -->
<script>
function exportTableToExcel(tableId) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet:"Sheet1"});
    XLSX.writeFile(wb, "employees.xlsx");
}

function exportTableToCSV(tableId) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet:"Sheet1"});
    XLSX.writeFile(wb, "employees.csv");
}

function exportTableToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4');
    html2canvas(document.querySelector("#employeeTable")).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        doc.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight);
        doc.save("employees.pdf");
    });
}
</script>
