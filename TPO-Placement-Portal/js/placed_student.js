// Wait for the DOM to be ready
document.addEventListener("DOMContentLoaded", function() {
    // Fetch the placed students data from the PHP file
    fetch('http://localhost/api/placed_students.php') // Ensure the path matches your server setup
        .then(response => {
            // Check if the response is OK
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Get the table body where the rows will be inserted
            const tableBody = document.getElementById('placed-students-table');

            // Loop through the data and create rows for each student
            data.forEach(student => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${student.name}</td>
                    <td>${student.enrollment_no}</td>
                    <td>${student.company_name}</td>
                    <td>${student.position}</td>
                    <td>${student.salary}</td>
                    <td>${student.placement_date}</td>
                `;

                // Append the row to the table
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
            alert('There was an error fetching the student data.');
        });

    // Search functionality (optional)
    document.getElementById("search-input").addEventListener("keyup", function() {
        var filter = this.value.toUpperCase();
        var rows = document.getElementById("placed-students-table").getElementsByTagName("tr");

        for (var i = 0; i < rows.length; i++) {
            var nameCell = rows[i].getElementsByTagName("td")[0]; // Name column
            if (nameCell) {
                var name = nameCell.textContent || nameCell.innerText;
                if (name.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    });
});