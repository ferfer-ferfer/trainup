<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Email</title>
<link rel="stylesheet" href="css/style.css">
<style>
.container {
    background: #fff;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 100%;
    max-width: 400px;
    text-align: center;
    margin: 50px auto;
}

h2 { margin-bottom: 20px; color: #333; }
form { display: flex; flex-direction: column; align-items: center; gap: 15px; }
input, button { width: 80%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; }
input:focus { border-color: #007bff; outline: none; }
button { background-color: #007bff; color: #fff; border: none; cursor: pointer; }
button:hover { background-color: #0056b3; }
.success { color: green; margin-bottom: 15px; }
.error { color: red; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="container">
<h2>Verify Your Email</h2>
<form action="" method="POST">
    <input type="email" name="email" placeholder="Your email" required>
    <input type="text" name="code" placeholder="Verification code" required>
    <button type="submit">Verify</button>
</form>
</div>

<?php
require 'db.php';

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $code  = $_POST['code'];

    // Use correct column name
    $stmt = $conn->prepare("SELECT * FROM pending_users WHERE email=? AND verification_token=?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0){
        echo "<p class='error'>Invalid code or email.</p>";
        exit();
    }

    $user = $res->fetch_assoc();

    // Move user to main users table
    $stmt2 = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password, phone_number)
                             VALUES (?,?,?,?,?,?)");
    $stmt2->bind_param("ssssss",
        $user['firstname'],
        $user['lastname'],
        $user['username'],
        $user['email'],
        $user['password'],
        $user['phone']
    );
    $stmt2->execute();

    // Delete pending record
    $stmt3 = $conn->prepare("DELETE FROM pending_users WHERE id=?");
    $stmt3->bind_param("i", $user['id']);
    $stmt3->execute();

    // Success message and redirect
    echo "<p class='success'>Email verified successfully! Redirecting...</p>";
    header("Refresh: 3; URL=information.php"); // redirect after 3 seconds
    exit();
}
?>
</body>
</html>
