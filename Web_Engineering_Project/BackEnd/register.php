<?php
// Start the session
session_start();

// Include the database configuration file
require_once 'database.php';

/**
 * Sanitize user input to prevent XSS and other security issues.
 */
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $email     = sanitize_input($_POST['email']);
    $name      = sanitize_input($_POST['name']);
    $password  = $_POST['password'];  // Will be hashed, so we only sanitize above if needed
    $phone     = sanitize_input($_POST['telephone']); // matches the 'telephone' input from the form
    $user_type = sanitize_input($_POST['user_type']);
    
    // Basic validation
    if (empty($email) || empty($name) || empty($password) || empty($phone) || empty($user_type)) {
        die("Please fill in all required fields.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Validate user type
    $allowed_user_types = ['Company', 'Passenger'];
    if (!in_array($user_type, $allowed_user_types)) {
        die("Invalid user type selected.");
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email already exists
    $check_email_query = "SELECT email FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($check_email_query)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email already exists
            $stmt->close();
            die("This email is already registered. Please use a different email or login.");
        }
        $stmt->close();
    } else {
        // SQL statement preparation failed
        die("Database error: Unable to prepare statement.");
    }

    // Provide default (empty) values for columns not collected yet
    $bio      = "";
    $address  = "";
    $location = "";
    $logo     = "";

    // Insert the new user into the database
    $insert_query = "
        INSERT INTO users 
            (name, email, password, phone, type, bio, address, location, logo, account_balance)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00)
    ";

    if ($stmt = $conn->prepare($insert_query)) {
        // Bind parameters for the INSERT query
        $stmt->bind_param(
            "sssssssss",
            $name,
            $email,
            $hashed_password,
            $phone,
            $user_type,
            $bio,
            $address,
            $location,
            $logo
        );

        if ($stmt->execute()) {
            // Get the inserted user_id
            $user_id = $stmt->insert_id;

            // Store user_id and user_type in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;

            // Close the statement and connection
            $stmt->close();
            $conn->close();

            // Redirect based on user_type
            if ($user_type === 'Company') {
                header("Location: ../FrontEnd/pages/company_register.html");
                exit();
            } else {
                header("Location: ../FrontEnd/pages/passenger_register.html");
                exit();
            }
        } else {
            // Insertion failed
            $stmt->close();
            $conn->close();
            die("Registration failed. Please try again.");
        }
    } else {
        // SQL statement preparation failed
        die("Database error: Unable to prepare statement.");
    }
} else {
    // If the form was not submitted via POST, redirect to registration page
    header("Location: ../FrontEnd/pages/register.html");
    exit();
}
?>
