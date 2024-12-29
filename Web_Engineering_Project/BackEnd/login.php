<?php
include_once 'database.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if ($email && $password) {
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database query error."]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['type'] = $user['type'];

            if ($user['type'] === 'Company') {
                echo json_encode(["status" => "success", "redirect" => "../../Backend/company_home.php"]);
            } elseif ($user['type'] === 'Passenger') {
                echo json_encode(["status" => "success", "redirect" => "../../Backend/passenger_home.php"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid user type."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
}
?>
