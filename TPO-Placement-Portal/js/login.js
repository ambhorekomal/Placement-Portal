document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const role = document.getElementById('role').value;
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    let apiUrl;
    let requestData;

    if (role === 'student') {
        apiUrl = 'http://localhost/api/student_login.php';
        requestData = { username: username, password: password }; // Treat username as enrollment_no
    } else if (role === 'tpo') {
        apiUrl = 'http://localhost/api/tpo_login.php';
        requestData = { username: username, password: password }; // TPO uses username directly
    } else {
        document.getElementById('errorMessage').innerText = 'Invalid role selected';
        return;
    }

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        });

        const result = await response.json();

        if (result.status === 'success') {
            if (role === 'student') {
                // Store enrollment_no in localStorage
                localStorage.setItem('student_enrollment_no', result.user.enrollment_no);
                window.location.href = 'student.html';
            } else if (role === 'tpo') {
                window.location.href = 'tpo.html';
            }
        } else {
            document.getElementById('errorMessage').innerText = result.message;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('errorMessage').innerText = 'An error occurred. Please try again.';
    }
});
