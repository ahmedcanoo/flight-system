<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve additional company data
    $user_id = $_POST['user_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $address = $_POST['address'] ?? null;
    $location = $_POST['location'] ?? null;
    $logo = $_FILES['logo']['tmp_name'] ?? null;

    if ($user_id && $name) {
        $logo_blob = null;
        if ($logo) {
            $logo_blob = file_get_contents($logo);
        }

        // SQL query to insert data into company_info table
        $query = "INSERT INTO company_info (user_id, name, bio, address, location, logo, flights, account_balance) 
                  VALUES (?, ?, ?, ?, ?, ?, '[]', 0.00)";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("isssss", $user_id, $name, $bio, $address, $location, $logo_blob);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Company information saved successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to save company information."]);
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
