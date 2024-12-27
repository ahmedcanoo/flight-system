<?php
// company_profile.php - Manages company profile actions (view/edit)

include_once 'database.php';  // Include database connection

session_start();

// Check if the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    echo json_encode(["status" => "error", "message" => "Access denied. Please log in as a company."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch company profile details
    $query = "SELECT name, email, bio, address FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "profile" => $profile]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update company profile
    $bio = $_POST['bio'];
    $address = $_POST['address'];

    $query = "UPDATE users SET bio = :bio, address = :address WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':id', $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating profile."]);
    }
}
?>
