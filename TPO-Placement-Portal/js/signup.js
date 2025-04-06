document.addEventListener("DOMContentLoaded", function() {
    const signupForm = document.getElementById("signupForm");
    const roleSelect = document.getElementById("role");
    const studentFields = document.getElementById("studentFields");
    const signupMessage = document.getElementById("signupMessage");

    // Toggle student-specific fields
    roleSelect.addEventListener("change", function() {
        if (roleSelect.value === "student") {
            studentFields.style.display = "block";
        } else {
            studentFields.style.display = "none";
        }
    });

    // Form Submission
    signupForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const role = roleSelect.value;
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        let requestData = {
            action: role === "student" ? "signup_student" : "signup_tpo",
            username: username,
            password: password
        };

        if (role === "student") {
            requestData.enrollment_no = document.getElementById("enrollment_no").value;
            requestData.email = document.getElementById("email").value;
            requestData.name = document.getElementById("name").value;
        }

        // Send data to backend
        fetch("http://localhost/api/login_signup.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    signupMessage.classList.remove("text-danger");
                    signupMessage.classList.add("text-success");
                    signupMessage.innerText = data.message;

                    // Redirect to login page after successful signup
                    setTimeout(() => {
                        window.location.href = "login.html";
                    }, 2000);
                } else {
                    signupMessage.classList.remove("text-success");
                    signupMessage.classList.add("text-danger");
                    signupMessage.innerText = data.message;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                signupMessage.classList.add("text-danger");
                signupMessage.innerText = "An error occurred. Please try again.";
            });
    });
});