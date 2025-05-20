<!-- Send Notification Modal -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="notificationForm">
        <div class="modal-header">
          <h5 class="modal-title" id="sendNotificationModalLabel">Send Notification</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="alert" id="notificationAlert" style="display:none;"></div>
            <div class="mb-3">
                <label for="employee_id" class="form-label">Select Employee</label>
                <select class="form-select" name="employee_id" required>
                    <option value="">-- Select Employee --</option>
                    <?php
                    $stmt = $pdo->query("SELECT id, first_name, last_name FROM employees ORDER BY first_name ASC");
                    while ($emp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$emp['id']}'>" . htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" name="message" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Send</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
