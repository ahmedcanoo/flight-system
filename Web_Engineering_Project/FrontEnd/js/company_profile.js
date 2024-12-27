const profileForm = document.getElementById('profileForm');
const flightsList = document.getElementById('flightsList');

// Preview uploaded logo
const logoInput = document.getElementById('logo');
const logoPreview = document.getElementById('logoPreview');
logoInput.addEventListener('change', function () {
    const file = logoInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            logoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Fetch current profile details
fetch('http://localhost/Web_Engineering_Project/BackEnd/company_profile.php', {
    method: 'GET',
})
    .then((response) => response.json())
    .then((data) => {
        if (data.status === 'success') {
            const profile = data.profile;
            document.getElementById('name').value = profile.name;
            document.getElementById('bio').value = profile.bio || '';
            document.getElementById('address').value = profile.address || '';
            if (profile.logo) {
                logoPreview.src = `http://localhost/Web_Engineering_Project/uploads/${profile.logo}`;
            }
            fetchFlights();
        }
    })
    .catch((error) => console.error('Error fetching profile:', error));

// Fetch flights list
function fetchFlights() {
    fetch('http://localhost/Web_Engineering_Project/BackEnd/flights.php', {
        method: 'GET',
    })
        .then((response) => response.json())
        .then((data) => {
            flightsList.innerHTML = '';
            if (data.length === 0) {
                flightsList.innerHTML = '<li>No flights available</li>';
            } else {
                data.forEach((flight) => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${flight.name} - ${flight.itinerary}`;
                    flightsList.appendChild(listItem);
                });
            }
        })
        .catch((error) => console.error('Error fetching flights:', error));
}

// Update profile
profileForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const bio = document.getElementById('bio').value;
    const address = document.getElementById('address').value;

    const formData = new FormData();
    formData.append('bio', bio);
    formData.append('address', address);
    if (logoInput.files[0]) {
        formData.append('logo', logoInput.files[0]);
    }

    fetch('http://localhost/Web_Engineering_Project/BackEnd/company_profile.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            if (data.status === 'success') {
                location.reload();
            }
        })
        .catch((error) => console.error('Error updating profile:', error));
});
