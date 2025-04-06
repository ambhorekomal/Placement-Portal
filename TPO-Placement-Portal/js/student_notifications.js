document.addEventListener('DOMContentLoaded', function() {
    const studentEnrollmentNo = localStorage.getItem('student_enrollment_no');

    if (!studentEnrollmentNo) {
        alert('You must be logged in to view notifications.');
        return; // Stop further execution if no enrollment number is found
    }

    // Function to fetch notifications for the specific enrollment number
    function fetchNotifications() {
        // Append the enrollment number to the URL as a query parameter
        fetch(`http://localhost/api/get_notifications.php?enrollment_no=${studentEnrollmentNo}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationList = document.getElementById('notificationList');
                    if (data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            const notificationItem = document.createElement('a');
                            notificationItem.classList.add('list-group-item', 'list-group-item-action');
                            notificationItem.innerHTML = `
                                <strong>${notification.date_sent}</strong><br>
                                ${notification.message}
                            `;
                            notificationList.appendChild(notificationItem);
                        });
                    } else {
                        notificationList.innerHTML = '<p>No notifications available.</p>';
                    }
                } else {
                    alert('Failed to fetch notifications: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching notifications.');
            });
    }

    // Fetch notifications when the page is loaded
    fetchNotifications();
});