window.onload = function() {
    const studentEnrollmentNo = localStorage.getItem('student_enrollment_no');
    
    if (!studentEnrollmentNo) {
        window.location.href = 'login.html'; // Redirect to login if no enrollment number
    } else {
        fetchStudentProfile(studentEnrollmentNo);
    }
};

function fetchStudentProfile(enrollmentNo) {
    fetch(`http://localhost/api/get_student_profile.php?enrollment_no=${enrollmentNo}`)
        .then(response => response.json())
        .then(data => {
            const createOrUpdateBtn = document.getElementById('createOrUpdateBtn');
            if (data.status === 'success' && data.profile) {
                window.profileData = data.profile;  // Store profile data globally

                // Profile exists, show Update Profile
                displayProfileData(window.profileData);
                createOrUpdateBtn.innerText = 'Update Profile';
                createOrUpdateBtn.onclick = toggleProfileForm;
            } else {
                // No profile found, show Create Profile
                createOrUpdateBtn.innerText = 'Create Profile';
                createOrUpdateBtn.onclick = toggleProfileForm;
            }
        })
        .catch(error => {
            console.error('Error fetching student profile:', error);
            alert('An error occurred while fetching the profile data.');
        });
}


function displayProfileData(profile) {
    // Display profile data on the page
    document.getElementById('nameDisplay').innerText = profile.name;
    document.getElementById('ageDisplay').innerText = profile.age;
    document.getElementById('addressDisplay').innerText = profile.address;
    document.getElementById('courseDisplay').innerText = profile.course;
    document.getElementById('yearDisplay').innerText = profile.year;
    document.getElementById('perDisplay').innerText = profile.percentage;
}

function toggleProfileForm() {
    const displayInfo = document.getElementById('display-info');
    const editInfo = document.getElementById('edit-info');
    const createOrUpdateBtn = document.getElementById('createOrUpdateBtn');

    displayInfo.classList.toggle('d-none');
    editInfo.classList.toggle('d-none');

    if (!editInfo.classList.contains('d-none') && createOrUpdateBtn.innerText === 'Update Profile') {
        // Prefill data for editing if updating
        document.getElementById('nameInput').value = window.profileData.name || "";
        document.getElementById('ageInput').value = window.profileData.age || "";
        document.getElementById('addressInput').value = window.profileData.address || "";
        document.getElementById('courseInput').value = window.profileData.course || "";
        document.getElementById('yearInput').value = window.profileData.year || "";
        document.getElementById('perInput').value = window.profileData.percentage || "";
    }
}


function saveProfile() {
    const studentEnrollmentNo = localStorage.getItem('student_enrollment_no');
    const name = document.getElementById('nameInput').value;
    const age = document.getElementById('ageInput').value;
    const address = document.getElementById('addressInput').value;
    const course = document.getElementById('courseInput').value;
    const year = document.getElementById('yearInput').value;
    const percentage = document.getElementById('perInput').value;

    const profileData = {
        enrollment_no: studentEnrollmentNo,
        name: name,
        age: age,
        address: address,
        course: course,
        year: year,
        percentage: percentage
    };

    // Determine URL based on whether profile exists
    const url = window.profileData ? 'http://localhost/api/update_profile.php' : 'http://localhost/api/create_profile.php';

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(profileData)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.message.includes('successfully')) {
            fetchStudentProfile(studentEnrollmentNo); // Reload profile
            toggleProfileForm();  // Hide the form
        }
    })
    .catch(error => {
        console.error('Error saving profile:', error);
        alert('An error occurred while saving the profile.');
    });
}

