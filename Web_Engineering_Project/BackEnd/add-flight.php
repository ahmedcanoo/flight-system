<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session and include database connection
session_start();
include_once 'database.php';

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Company') {
    header("Location: login.html");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = $_SESSION['user_id'];
    $flight_name = $_POST['name'] ?? null;
    $flight_id = $_POST['id'] ?? null;
    $itinerary = $_POST['itinerary'] ?? null;
    $fees = $_POST['fees'] ?? null;
    $num_passengers = $_POST['num_passengers'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    // Validate input
    if (!$flight_name || !$flight_id || !$itinerary || !$fees || !$num_passengers || !$start_time || !$end_time) {
        $error_message = "All fields are required.";
    } else {
        // Correct query with proper column names
        $query = "INSERT INTO flights (id, name, itinerary, num_passengers_registered, num_passengers_pending, fees, start_time, end_time, completed, company_id)
                  VALUES (?, ?, ?, 0, ?, ?, ?, ?, 0, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issidssi", $flight_id, $flight_name, $itinerary, $num_passengers, $fees, $start_time, $end_time, $company_id);

        if ($stmt->execute()) {
            $success_message = "Flight added successfully.";
        } else {
            $error_message = "Failed to add flight. Please try again. Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flight</title>
    <link rel="stylesheet" href="../Frontend/css/add_flight.css">
    </head>
<body>
    <h1>Add New Flight</h1>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"> <?php echo $success_message; ?> </p>
    <?php elseif (isset($error_message)): ?>
        <p style="color: red;"> <?php echo $error_message; ?> </p>
    <?php endif; ?>

    <form action="add_flight.php" method="POST">
        <div>
            <label for="name">Flight Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="id">Flight ID:</label>
            <input type="number" id="id" name="id" required>
        </div>
        <div>
            <label for="itinerary">Itinerary:</label>
            <textarea id="itinerary" name="itinerary" required></textarea>
        </div>
        <div>
            <label for="fees">Fees:</label>
            <input type="number" id="fees" name="fees" step="0.01" required>
        </div>
        <div>
            <label for="num_passengers">Number of Passengers:</label>
            <input type="number" id="num_passengers" name="num_passengers" required>
        </div>
        <div>
            <label for="start_time">Start Time:</label>
            <input type="datetime-local" id="start_time" name="start_time" required>
        </div>
        <div>
            <label for="end_time">End Time:</label>
            <input type="datetime-local" id="end_time" name="end_time" required>
        </div>
        <button type="submit">Add Flight</button>
    </form>

    <button onclick="window.location.href='company_home.php'">Back to Home</button>
</body>
</html>
