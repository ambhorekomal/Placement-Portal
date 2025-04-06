document.addEventListener('DOMContentLoaded', function() {
    // Function to send notifications
    function sendNotifications() {
        // Get the selected students (this could be fetched from your table or selection)
        const selectedStudents = getSelectedStudents(); // Assuming this is a function you have to get selected students
        const message = document.getElementById('notificationMessage').value;

        if (selectedStudents.length > 0 && message.trim() !== "") {
            // Prepare the data to be sent in the POST request
            const data = {
                students: selectedStudents,
                message: message
            };

            // Send POST request to send notifications
            fetch('http://localhost/api/send_notifications.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notifications sent successfully!');
                    } else {
                        alert('Failed to send notifications: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending notifications.');
                });
        } else {
            alert('Please select students and enter a message.');
        }
    }

    // Attach the event to the "Send Notifications" button
    const sendButton = document.getElementById('sendNotificationBtn');
    sendButton.addEventListener('click', sendNotifications);
});

// Function to get the selected students (you will need to implement this based on your table or selection)
function getSelectedStudents() {
    const selectedStudents = [];
    const checkboxes = document.querySelectorAll('.student-checkbox:checked'); // Assuming checkboxes for student selection
    checkboxes.forEach(checkbox => {
        selectedStudents.push(checkbox.value); // Assuming the checkbox value is the enrollment_no
    });
    return selectedStudents;
}
$(document).ready(function() {
    fetch("http://localhost/api/get_notifications.php")
        .then(response => response.json())
        .then(data => {
            const notificationList = $("#notificationList");
            notificationList.empty(); // Clear previous notifications

            if (data.success && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    let card = `
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">${notification.title}</h5>
                                    <p class="card-text">${notification.message}</p>
                                    <p class="text-muted small">${new Date(notification.date).toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    notificationList.append(card);
                });
            } else {
                notificationList.html("<p class='text-center text-warning'>No notifications available.</p>");
            }
        })
        .catch(error => console.error("Error fetching notifications:", error));
});