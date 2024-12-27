<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve additional company data
    $user_id = $_POST['user_id'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $address = $_POST['address'] ?? null;
    $location = $_POST['location'] ?? null;
    $username = $_POST['username'] ?? null;
    $logo = $_FILES['logo']['tmp_name'] ?? null;
    
    if ($user_id && $bio && $username) {
        $logo_blob = null;
        if ($logo) {
            $logo_blob = file_get_contents($logo);
        }

        // SQL query to insert data into company_info table
        $query = "INSERT INTO company_info (user_id, bio, address, location, username, logo) 
                  VALUES (:user_id, :bio, :address, :location, :username, :logo)";

        // Prepare and execute the SQL query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':logo', $logo_blob, PDO::PARAM_LOB);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Company information saved successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error saving company information."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
    }
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
?>