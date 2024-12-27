<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database configuration file
require_once 'database.php';

// Sanitize function
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email     = sanitize_input($_POST['email']);
    $name      = sanitize_input($_POST['name']);
    $password  = $_POST['password']; 
    $phone     = sanitize_input($_POST['telephone']);
    $user_type = sanitize_input($_POST['user_type']);

    // Basic validation
    if (empty($email) || empty($name) || empty($password) || empty($phone) || empty($user_type)) {
        die("Please fill in all required fields.");
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Validate user type
    $allowed_user_types = ['Company', 'Passenger'];
    if (!in_array($user_type, $allowed_user_types)) {
        die("Invalid user type selected.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $check_email_query = "SELECT email FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($check_email_query)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            die("This email is already registered. Please use a different email or login.");
        }
        $stmt->close();
    } else {
        die("Database error: Unable to prepare statement (checking email).");
    }

    // 1) Insert into `users` table
    $insert_user_query = "
        INSERT INTO users (name, email, password, phone, type)
        VALUES (?, ?, ?, ?, ?)
    ";
    if ($stmt = $conn->prepare($insert_user_query)) {
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $user_type);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;  // newly created user primary key
            $stmt->close();

            // 2) Insert into the correct info table
            if ($user_type === 'Company') {
                $insert_company_query = "
                    INSERT INTO company_info (user_id, bio, address, location, username, logo, account_balance)
                    VALUES (?, '', '', '', '', NULL, 0.00)
                ";
                $stmt2 = $conn->prepare($insert_company_query);
                $stmt2->bind_param("i", $user_id);
                $stmt2->execute();
                $stmt2->close();

                // Redirect
                $_SESSION['user_id']   = $user_id;
                $_SESSION['user_type'] = $user_type;
                header("Location: ../FrontEnd/pages/company_register.html");
                exit();
            } else {
                $insert_passenger_query = "
                    INSERT INTO passenger_info (user_id, photo, passport_image, flights, account_balance)
                    VALUES (?, NULL, NULL, '[]', 0.00)
                ";
                $stmt3 = $conn->prepare($insert_passenger_query);
                $stmt3->bind_param("i", $user_id);
                $stmt3->execute();
                $stmt3->close();

                // Redirect
                $_SESSION['user_id']   = $user_id;
                $_SESSION['user_type'] = $user_type;
                header("Location: ../FrontEnd/pages/passenger_register.html");
                exit();
            }
        } else {
            $stmt->close();
            die("Registration failed. Please try again.");
        }
    } else {
        die("Database error: Unable to prepare statement (inserting user).");
    }
} else {
    // If the form was not submitted via POST, redirect
    header("Location: ../FrontEnd/pages/register.html");
    exit();
}
