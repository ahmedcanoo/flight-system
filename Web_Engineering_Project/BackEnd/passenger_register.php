<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve passenger-specific data
    $user_id = $_POST['user_id'] ?? null;
    $photo = $_FILES['photo']['tmp_name'] ?? null;
    $passport_image = $_FILES['passport_image']['tmp_name'] ?? null;

    if ($user_id && $photo && $passport_image) {
        // Process photo and passport image as binary data
        $photo_blob = file_get_contents($photo);
        $passport_blob = file_get_contents($passport_image);

        // SQL query to insert data into passenger_info table
        $query = "INSERT INTO passenger_info (user_id, photo, passport_image, flights, account_balance) 
                  VALUES (?, ?, ?, '[]', 0.00)";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("iss", $user_id, $photo_blob, $passport_blob);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Passenger information saved successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to save passenger information."]);
            }

            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Database error: Unable to prepare statement."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
