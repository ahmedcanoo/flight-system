document.getElementById('passengerForm').addEventListener('submit', (e) => {
    e.preventDefault();

    const passportNumber = document.getElementById('passportNumber').value;
    const passportImage = document.getElementById('passportImage').files[0];

    console.log("Submitting passenger details:", { passportNumber, passportImage });

    // Add server-side submission logic here

    alert('Passenger Registration Complete!');
    window.location.href = 'passenger_home.html'; // Redirect to the passenger home page
});