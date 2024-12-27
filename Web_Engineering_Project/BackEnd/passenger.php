<?php
// passenger.php - Manages passenger actions (book flight, view bookings)

include_once 'database.php';  // Include database connection

session_start();

// Check if the user is logged in and is a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    echo json_encode(["status" => "error", "message" => "Access denied. Please log in as a passenger."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'bookFlight') {
        // Book a flight
        $flightId = $_POST['flightId'];

        $query = "INSERT INTO bookings (flight_id, passenger_id) VALUES (:flight_id, :passenger_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':flight_id', $flightId);
        $stmt->bindParam(':passenger_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Flight booked successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error booking flight."]);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // View all bookings for the logged-in passenger
    $query = "SELECT b.id, f.name AS flight_name, f.itinerary, b.booking_date, b.status
              FROM bookings b
              JOIN flights f ON b.flight_id = f.id
              WHERE b.passenger_id = :passenger_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':passenger_id', $_SESSION['user_id']);
    $stmt->execute();

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["status" => "success", "bookings" => $bookings]);
}
?>
