document.addEventListener("DOMContentLoaded", function() {
    console.log("Fetching student data...");

    fetch("http://localhost/api/fetch_students.php", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Cache-Control": "no-cache, no-store, must-revalidate", // Removed "Expires" header
                "Pragma": "no-cache"
            },
            mode: "cors"
        })
        .then(response => response.text())
        .then(text => {
            console.log("Raw Response from API:", text);
            try {
                const data = JSON.parse(text);
                console.log("Parsed JSON:", data);

                if (data.success) {
                    const tableBody = document.getElementById("studentTableBody");
                    if (!tableBody) {
                        console.error("Table body not found! Ensure correct ID in HTML.");
                        return;
                    }

                    tableBody.innerHTML = "";
                    data.data.forEach(student => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                        <td>${student.enrollment_no}</td>
                        <td>${student.name}</td>
                        <td>${student.age}</td>
                        <td>${student.address}</td>
                        <td>${student.course}</td>
                        <td>${student.year}</td>
                        <td>${student.percentage}%</td>
                        <td><a href="view_profile.html?enrollment_no=${student.enrollment_no}" class="btn btn-sm btn-primary">View Profile</a></td>
                    `;
                        tableBody.appendChild(row);
                    });
                } else {
                    console.error("Error fetching students:", data.message);
                }
            } catch (error) {
                console.error("JSON Parse Error:", error);
            }
        })
        .catch(error => console.error("Fetch error:", error));
});