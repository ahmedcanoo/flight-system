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
    $username  = sanitize_input($_POST['username']);
    $password  = $_POST['password']; 
    $phone     = sanitize_input($_POST['telephone']);
    $user_type = sanitize_input($_POST['user_type']);

    // Basic validation
    if (empty($email) || empty($username) || empty($password) || empty($phone) || empty($user_type)) {
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
        die("Database error: Unable to prepare statement (checking email)." . $conn->error);
    }

    // Check if username already exists
    $check_username_query = "SELECT username FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($check_username_query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            die("Username already exists.");
        }
        $stmt->close();
    } else {
        die("Database error: Unable to prepare statement (checking username)." . $conn->error);
    }

    // Insert into `users` table
    $insert_user_query = "INSERT INTO users (username, email, password, phone, type) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($insert_user_query)) {
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $phone, $user_type);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $user_id; // Store user ID in session
            $_SESSION['user_type'] = $user_type; // Store user type in session
            $stmt->close();

            // Insert into the correct info table
            if ($user_type === 'Company') {
                $insert_company_query = "INSERT INTO company_info (user_id, name, bio, address, location, logo, flights, account_balance) 
                                         VALUES (?, '', '', '', '', NULL, '[]', 0.00)";
                if ($stmt2 = $conn->prepare($insert_company_query)) {
                    $stmt2->bind_param("i", $user_id); // Bind user_id to the prepared statement
                    $stmt2->execute();
                    $stmt2->close();
            
                    // Redirect to company registration page
                    header("Location: ../FrontEnd/pages/company_register.html");
                    exit();
                } else {
                    die("Database error: Unable to prepare statement (inserting company info)." . $conn->error);
                }
            }else {
                $insert_passenger_query = "INSERT INTO passenger_info (user_id, photo, passport_image, flights, account_balance) VALUES (?, NULL, NULL, '[]', 0.00)";
                if ($stmt3 = $conn->prepare($insert_passenger_query)) {
                    $stmt3->bind_param("i", $user_id);
                    $stmt3->execute();
                    $stmt3->close();

                    // Redirect to passenger registration page
                    $_SESSION['user_id'] = $user_id;
                    header("Location: ../FrontEnd/pages/passenger_register.html?user_id=" . $user_id);
                    exit();
                } else {
                    die("Database error: Unable to prepare statement (inserting passenger info)." . $conn->error);
                }
            }
        } else {
            $stmt->close();
            die("Registration failed. Please try again." . $conn->error);
        }
    } else {
        die("Database error: Unable to prepare statement (inserting user)." . $conn->error);
    }
} else {
    // If the form was not submitted via POST, redirect
    header("Location: ../FrontEnd/pages/register.html");
    exit();
}
