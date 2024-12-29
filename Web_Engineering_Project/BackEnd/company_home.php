<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once 'database.php'; // Include database connection

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Company') {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['user_id'];

// Fetch company information from the database
$query = "SELECT name, logo FROM company_info WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No company data found. Please ensure your company details are registered.");
}

$company = $result->fetch_assoc();
$companyName = htmlspecialchars($company['name']);
$companyLogo = $company['logo'] ? 'data:image/jpeg;base64,' . base64_encode($company['logo']) : '../css/logo.png';

// Fetch flights associated with the company (adjusted query)
$flightsQuery = "SELECT id, name, itinerary FROM flights WHERE company_id = ?";
$stmt = $conn->prepare($flightsQuery);
if (!$stmt) {
    die("Database query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$flightsResult = $stmt->get_result();

$flights = [];
while ($row = $flightsResult->fetch_assoc()) {
    $flights[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Home</title>
    <link rel="stylesheet" href="../Frontend/css/company_home.css"></head>
<body>
    <!-- Navigation Bar -->
    <div class="nav-bar">
        <div class="logo">
            <img id="companyLogo" src="<?= $companyLogo ?>" alt="Company Logo" width="50">
        </div>
        <div class="company-name" id="companyName">Welcome, <?= $companyName ?>!</div>
        <div class="links">
            <a href="company_profile.php">Profile</a>
            <a href="messages.php">Messages</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Manage Flights</h2>
        <button onclick="window.location.href='add_flight.php'">Add Flight</button>

        <h2>Flights List</h2>
        <ul id="flightsList" class="flights-list">
            <?php if (count($flights) > 0): ?>
                <?php foreach ($flights as $flight): ?>
                    <li>
                        <a href="flight_details.php?id=<?= $flight['id'] ?>">
                            <?= htmlspecialchars($flight['name']) ?> - <?= htmlspecialchars($flight['itinerary']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No flights available. Add a new flight to start managing.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
