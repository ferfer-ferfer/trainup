<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TrainUp | Verify Email</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
<link rel="stylesheet" href="css/style.css">
<style>
body {
   
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0,217,255,0.2);
    padding: 40px 30px;
    width: 90%;
    max-width: 400px;
    margin: 80px auto;
    text-align: center;
    backdrop-filter: blur(10px);
}

h2 {
    color: #00e5ff;
    margin-bottom: 25px;
    font-size: 1.8rem;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

input {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #00bfff;
    background: transparent;
    color: #fff;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

input:focus {
    border-color: #00e5ff;
    box-shadow: 0 0 6px #00e5ff;
}

button {
    width: 100%;
    padding: 12px;
    background: #00d5ff;
    color: #001f54;
    font-weight: 600;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}

button:hover {
    background: #00a9cc;
    transform: scale(1.03);
}

.success, .error {
    font-size: 14px;
    margin-bottom: 10px;
}

.success { color: lightgreen; }
.error { color: #ff6b6b; }

.switch-link {
    margin-top: 15px;
    font-size: 14px;
}

.switch-link a {
    color: #00d5ff;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.switch-link a:hover {
    text-decoration: underline;
}

/* Small screens */
@media (max-width:480px) {
    .container {
        padding: 25px 20px;
        margin: 50px 10px;
    }

    h2 { font-size: 1.5rem; }
    input, button { font-size: 13px; padding: 10px; }
}
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
