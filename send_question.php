<?php
require 'db.php'; // database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($message)) {
        die("Please fill in all required fields.");
    }

    // Insert into database
    $stmt = $conn->prepare(
        "INSERT INTO message (firstname, lastname, email, phone, message)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        die("Database error.");
    }

    $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $message);

    if ($stmt->execute()) {
        // Redirect back with success flag
        header("Location: aboutus.php?sent=success");
        exit;
    } else {
        echo "Failed to send your message. Please try again.";
    }
}
?>
