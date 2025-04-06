document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("fetchJobsBtn").addEventListener("click", fetchJobs);
});

async function fetchJobs() {
    const enrollmentNo = document.getElementById("enrollment_no").value;
    if (!enrollmentNo) {
        alert("Please enter your Enrollment No!");
        return;
    }

    try {
        const response = await fetch(`http://localhost/api/get_student_jobs.php?enrollment_no=${enrollmentNo}`);
        const data = await response.json();
        displayJobs(data, enrollmentNo);
    } catch (error) {
        console.error("Error fetching jobs:", error);
        alert("Error fetching jobs. Please try again.");
    }
}

function displayJobs(data, enrollmentNo) {
    const jobList = document.getElementById("job-list");
    jobList.innerHTML = "";

    if (data.status === "success" && data.jobs.length > 0) {
        jobList.innerHTML = `<p class="text-success">Your Percentage: <strong>${data.percentage}%</strong></p>`;

        data.jobs.forEach(job => {
            jobList.innerHTML += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">${job.job_title}</h5>
                        <p><strong>Salary:</strong> ${job.salary} INR</p>
                        <p><strong>Qualifications:</strong> ${job.qualifications}</p>
                        <p><strong>Eligibility:</strong> ${job.eligibility}%</p>
                        <p><strong>Work Mode:</strong> ${job.work_mode}</p>
                        <p><strong>Location:</strong> ${job.location}</p>
                        <button class="btn btn-primary apply-btn" data-enrollment="${enrollmentNo}" data-job-id="${job.job_id}">Apply Now</button>
                    </div>
                </div>
            `;
        });

        // Add event listeners to Apply buttons
        document.querySelectorAll(".apply-btn").forEach(button => {
            button.addEventListener("click", function() {
                applyJob(this.dataset.enrollment, this.dataset.jobId);
            });
        });
    } else {
        jobList.innerHTML = `<p class="text-danger">No job opportunities available for your percentage.</p>`;
    }
}

async function applyJob(enrollmentNo, jobId) {
    try {
        const response = await fetch("http://localhost/api/apply_for_job.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ enrollment_no: enrollmentNo, job_id: jobId })
        });

        const data = await response.json();
        alert(data.message);
    } catch (error) {
        console.error("Error applying for job:", error);
        alert("Error submitting application. Please try again.");
    }
}