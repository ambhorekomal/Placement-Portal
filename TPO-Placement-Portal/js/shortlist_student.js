document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('applyFilterBtn').addEventListener('click', function() {
        const course = document.getElementById('course').value;
        const year = document.getElementById('year').value;
        const percentage = document.getElementById('percentage').value;

        let url = `http://localhost/api/shortlist_student.php?percentage=${percentage}`;
        if (course) url += `&course=${course}`;
        if (year) url += `&year=${year}`;

        console.log("Fetching students with URL:", url);

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const studentTableBody = document.getElementById('studentTableBody');
                studentTableBody.innerHTML = '';

                if (!data || data.length === 0) {
                    alert('No students found for the given filter criteria.');
                    return;
                }

                data.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${student.enrollment_no}</td>
                        <td>${student.name}</td>
                        <td>${student.age}</td>
                        <td>${student.address}</td>
                        <td>${student.course}</td>
                        <td>${student.year}</td>
                        <td>${student.percentage}</td>
                        <td>${student.jobs_applied || 'None'}</td>
                        <td><button class="btn btn-info">View Profile</button></td>
                        <td><input type="checkbox" class="shortlist-checkbox" data-enrollment="${student.enrollment_no}" /></td>
                    `;
                    studentTableBody.appendChild(row);
                });

                const shortlistCheckboxes = document.querySelectorAll('.shortlist-checkbox');
                const sendNotificationBtn = document.getElementById('sendNotificationBtn');
                sendNotificationBtn.style.display = shortlistCheckboxes.length > 0 ? 'inline-block' : 'none';
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Error occurred while fetching data. Please try again.');
            });
    });

    document.getElementById('sendNotificationBtn').addEventListener('click', function() {
        const selectedStudents = [];
        const message = document.getElementById('notificationMessage').value.trim();

        document.querySelectorAll('.shortlist-checkbox:checked').forEach(checkbox => {
            const enrollmentNo = checkbox.getAttribute('data-enrollment');
            selectedStudents.push(enrollmentNo);
        });

        if (selectedStudents.length > 0 && message !== "") {
            const data = { students: selectedStudents, message: message };

            fetch('http://localhost/api/send_notifications.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notifications sent to the shortlisted students.');
                    } else {
                        alert('Failed to send notifications: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error sending notifications:', error);
                    alert('Error occurred while sending notifications. Please try again.');
                });
        } else {
            alert('Please select at least one student and enter a message.');
        }
    });
});