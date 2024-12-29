<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once 'database.php'; // Include database connection

// Ensure the user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Company') {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['user_id'];

// Fetch company information
$query = "SELECT name, bio, address, logo FROM company_info WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No company data found. Please ensure your company details are registered.");
}

$company = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = !empty($_POST['name']) ? $_POST['name'] : $company['name'];
    $bio = !empty($_POST['bio']) ? $_POST['bio'] : $company['bio'];
    $address = !empty($_POST['address']) ? $_POST['address'] : $company['address'];
    $logo = $_FILES['logo']['tmp_name'] ?? null;

    $logo_blob = null;
    if ($logo) {
        $logo_blob = file_get_contents($logo);
    }

    // Update company information
    if ($logo_blob) {
        $updateQuery = "UPDATE company_info SET name = ?, bio = ?, address = ?, logo = ? WHERE user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssi", $name, $bio, $address, $logo_blob, $user_id);
    } else {
        $updateQuery = "UPDATE company_info SET name = ?, bio = ?, address = ? WHERE user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $name, $bio, $address, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='company_profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company Profile</title>
    <link rel="stylesheet" href="../Frontend/css/edit_company_profile.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Company Profile</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Company Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($company['name']) ?>">
            </div>
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio"><?= htmlspecialchars($company['bio']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($company['address']) ?>">
            </div>
            <div class="form-group">
                <label for="logo">Upload New Logo (optional)</label>
                <input type="file" id="logo" name="logo">
            </div>
            <button type="submit">Save Changes</button>
        </form>
        <button onclick="window.location.href='company_profile.php'">Cancel</button>
    </div>
</body>
</html>
