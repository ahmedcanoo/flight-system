<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve passenger-specific data
    $user_id = $_POST['user_id'] ?? null;
    $passport_image = $_FILES['passport_image']['tmp_name'] ?? null;

    if ($user_id && $passport_image) {
        // Process the passport image as binary data
        $passport_blob = file_get_contents($passport_image);

        // SQL query to insert data into passenger_info table
        $query = "INSERT INTO passenger_info (user_id, passport_image) 
                  VALUES (:user_id, :passport_image)";

        // Prepare and execute the SQL query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':passport_image', $passport_blob, PDO::PARAM_LOB);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Passenger information saved successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error saving passenger information."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
    }
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
?>
