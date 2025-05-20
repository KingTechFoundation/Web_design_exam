document.addEventListener('DOMContentLoaded', () => {
  const notificationsContainer = document.getElementById('notifications');
  const notificationCount = document.getElementById('notification-count');

  async function fetchNotifications() {
    try {
      const response = await fetch('../handlers/get_notifications.php', {
        credentials: 'include',
      });
      const data = await response.json();

      if (data.status === 'success') {
        displayNotifications(data.notifications);
        updateBadge(data.notifications);
      } else {
        notificationsContainer.innerHTML = `<p>${data.message}</p>`;
        notificationCount.textContent = '0';
      }
    } catch (error) {
      notificationsContainer.innerHTML =
        '<p>An error occurred while fetching notifications.</p>';
      notificationCount.textContent = '0';
    }
  }

  function displayNotifications(notifications) {
    if (notifications.length === 0) {
      notificationsContainer.innerHTML = '<p>No notifications.</p>';
      return;
    }

    const list = document.createElement('ul');
    list.className = 'notification-list';

    notifications.forEach((notification) => {
      const item = document.createElement('li');
      item.className = notification.is_read
        ? 'notification read'
        : 'notification unread';
      item.innerHTML = `
                <h4>${notification.title}</h4>
                <p>${notification.message}</p>
                <small>Sent at: ${notification.sent_at}</small>
                <div>
                    <button class="mark-btn" data-id="${
                      notification.id
                    }" data-read="${notification.is_read ? 0 : 1}">
                        Mark as ${notification.is_read ? 'Unread' : 'Read'}
                    </button>
                </div>
            `;
      list.appendChild(item);
    });

    notificationsContainer.innerHTML = '';
    notificationsContainer.appendChild(list);

    document.querySelectorAll('.mark-btn').forEach((button) => {
      button.addEventListener('click', async () => {
        const id = button.dataset.id;
        const newStatus = button.dataset.read;
        await markNotification(id, newStatus);
        fetchNotifications(); // Refresh after update
      });
    });
  }

  function updateBadge(notifications) {
    const unreadCount = notifications.filter((n) => n.is_read == 0).length;
    notificationCount.textContent = unreadCount;
  }

  async function markNotification(id, isRead) {
    try {
      await fetch('../handlers/update_notification_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `notification_id=${id}&is_read=${isRead}`,
        credentials: 'include',
      });
    } catch (error) {
      console.error('Failed to update notification status.');
    }
  }

  fetchNotifications();
});
