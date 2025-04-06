const users = []; // Array to store user data for sign-up

// Handle Login Form Submission
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;

    const studentCredentials = { username: '2212280386', password: 'student123' };
    const tpoCredentials = { username: 'tpo', password: 'tpo123' };

    if (role === 'student' && username === studentCredentials.username && password === studentCredentials.password) {
        window.location.href = 'student.html';
    } else if (role === 'tpo' && username === tpoCredentials.username && password === tpoCredentials.password) {
        window.location.href = 'tpo.html';
    } else {
        document.getElementById('errorMessage').innerText = 'Invalid login credentials. Please try again.';
    }
});

// Handle Sign Up Form Submission
document.getElementById('signupForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const newUsername = document.getElementById('newUsername').value;
    const newPassword = document.getElementById('newPassword').value;
    const newEnrollment = document.getElementById('newEnrollment').value;

    // Store the new user data
    users.push({ username: newUsername, password: newPassword, enrollment: newEnrollment });
    document.getElementById('signupMessage').innerText = 'Registration successful! You can now log in.';

    // Clear the form
    document.getElementById('signupForm').reset();
});

// Show Sign Up Form
document.getElementById('showSignup').addEventListener('click', function() {
    document.getElementById('loginFormContainer').style.display = 'none';
    document.getElementById('signupFormContainer').style.display = 'block';
});

// Show Login Form
document.getElementById('showLogin').addEventListener('click', function() {
    document.getElementById('signupFormContainer').style.display = 'none';
    document.getElementById('loginFormContainer').style.display = 'block';
});