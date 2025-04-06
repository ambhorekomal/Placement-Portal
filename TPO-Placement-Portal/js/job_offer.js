document.addEventListener("DOMContentLoaded", function() {
    const jobForm = document.getElementById("jobOfferForm");

    jobForm.addEventListener("submit", async function(event) {
        event.preventDefault(); // Prevent default form submission

        // Gather form data
        const formData = new FormData(jobForm);

        try {
            // Send form data using Fetch API
            const response = await fetch("http://localhost/api/job_offer.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                jobForm.reset(); // Reset form after successful submission
                const modal = bootstrap.Modal.getInstance(document.getElementById("uploadJobModal"));
                modal.hide(); // Close modal after submission
            } else {
                alert("Error: " + result.message);
            }
        } catch (error) {
            console.error("Error submitting job offer:", error);
            alert("An error occurred while uploading the job offer.");
        }
    });
});