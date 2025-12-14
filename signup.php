<?php
require 'db.php'; // your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname  = trim($_POST['firstname']);
    $lastname   = trim($_POST['lastname']);
    $username   = trim($_POST['username']);
    $phone      = trim($_POST['phoneNumber']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        die("Please fill in all required fields.");
    }

    if ($password !== $confirm) {
        die("Passwords do not match!");
    }

    // Check if username or email exists
    $sql_check = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        die("Username or email already taken.");
    }
    $stmt_check->close();

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Default values for unused fields
    $address = null;
    $group_size = null;
    $age = null;
    $gender = null;
    $profile_picture = null;
    $bio = null;
    $telegram = null;
    $discord = null;
    $role_id = null;
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO users 
    (username, firstname, lastname, phoneNumber, email, address, group_size, age, gender, profile_picture, bio, telegram, discord, password, role_id, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssis", 
        $username, 
        $firstname, 
        $lastname, 
        $phone, 
        $email, 
        $address, 
        $group_size, 
        $age, 
        $gender, 
        $profile_picture, 
        $bio, 
        $telegram, 
        $discord, 
        $hashed, 
        $role_id, 
        $created_at
    );

    if ($stmt->execute()) {
        header("Location: information.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>

