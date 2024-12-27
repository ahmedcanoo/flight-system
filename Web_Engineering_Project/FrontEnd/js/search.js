document.getElementById('searchForm').addEventListener('submit', (e) => {
    e.preventDefault();

    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '<div onclick="viewFlight(1)">Flight to Paris</div>';
});

function viewFlight(id) {
    window.location.href = `flight_info.html?id=${id}`;
}
