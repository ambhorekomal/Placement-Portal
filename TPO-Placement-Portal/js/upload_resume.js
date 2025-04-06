document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        const messageElement = document.getElementById('message'); // Ensure this exists
        if (!messageElement) {
            console.error("Error: Element with id 'message' not found.");
            return;
        }

        messageElement.innerText = "Uploading..."; // Show uploading message

        const formData = new FormData();
        formData.append('enrollment_no', document.getElementById('enrollment_no').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('file', document.getElementById('file').files[0]);
        formData.append('fileType', document.getElementById('fileType').value); // Include fileType

        fetch('http://localhost/api/upload_resume.php', { // Adjust URL if needed
                method: 'POST',
                body: formData,
            })
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                if (data.success) {
                    messageElement.innerHTML = `<span style="color: green;">✔ ${data.message}</span>`;
                    document.getElementById('uploadForm').reset(); // Reset form after successful upload
                } else {
                    messageElement.innerHTML = `<span style="color: red;">❌ ${data.message}</span>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageElement.innerHTML = `<span style="color: red;">❌ An error occurred.</span>`;
            });
    });
});