// Toggle visibility of the Add Flight Form
function toggleAddFlightForm() {
    const form = document.getElementById("addFlightForm");
    form.style.display = form.style.display === "none" || !form.style.display ? "block" : "none";
}

// Fetch flights and display them
function fetchFlights() {
    fetch("http://localhost/Web_Engineering_Project/BackEnd/flights.php", {
        method: "GET",
    })
        .then((response) => response.json())
        .then((data) => {
            const flightsList = document.getElementById("flightsList");
            flightsList.innerHTML = ""; // Clear the list before adding new items
            if (data.length === 0) {
                flightsList.innerHTML = "<li>No flights available</li>";
            } else {
                data.forEach((flight) => {
                    const listItem = document.createElement("li");
                    listItem.classList.add("flight-item");
                    listItem.textContent = `${flight.name} - ${flight.itinerary}`;
                    
                    const viewButton = document.createElement("button");
                    viewButton.textContent = "View Details";
                    viewButton.onclick = () => viewFlightDetails(flight.id);
                    
                    const cancelButton = document.createElement("button");
                    cancelButton.textContent = "Cancel";
                    cancelButton.onclick = () => cancelFlight(flight.id);

                    listItem.appendChild(viewButton);
                    listItem.appendChild(cancelButton);
                    flightsList.appendChild(listItem);
                });
            }
        })
        .catch((error) => console.error("Error fetching flights:", error));
}

// Add a new flight
document.getElementById("flightForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent form submission

    const name = document.getElementById("flightName").value;
    const itinerary = document.getElementById("itinerary").value;
    const passengers = document.getElementById("passengers").value;
    const fees = document.getElementById("fees").value;

    if (!name || !itinerary || !passengers || !fees) {
        alert("All fields are required!");
        return;
    }

    fetch("http://localhost/Web_Engineering_Project/BackEnd/flights.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=addFlight&name=${encodeURIComponent(name)}&itinerary=${encodeURIComponent(itinerary)}&passengers=${passengers}&fees=${fees}`,
    })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            if (data.status === "success") {
                fetchFlights(); // Reload flights
                toggleAddFlightForm(); // Hide the form
                document.getElementById("flightForm").reset(); // Reset form fields
            }
        })
        .catch((error) => console.error("Error adding flight:", error));
});

// Cancel a flight
function cancelFlight(flightId) {
    if (confirm("Are you sure you want to cancel this flight?")) {
        fetch("http://localhost/Web_Engineering_Project/BackEnd/flights.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=cancelFlight&flightId=${flightId}`,
        })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message);
                if (data.status === "success") {
                    fetchFlights(); // Refresh the flight list
                }
            })
            .catch((error) => console.error("Error canceling flight:", error));
    }
}

// View flight details
function viewFlightDetails(id) {
    window.location.href = `flight_details.html?id=${id}`;
}

// Load flights when the page loads
window.onload = fetchFlights;
